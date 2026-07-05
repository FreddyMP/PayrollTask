<?php

namespace App\Http\Controllers;

use App\Models\AccessLog;
use App\Models\Company;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class AuthController extends Controller
{
    public function showLogin()
    {
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }
        return view('auth.login');
    }

    public function showRegister()
    {
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $messages = [
            'email.unique' => 'Ese correo no está disponible.',
        ];

        $request->validate([
            'company_name' => 'required|string|max:255',
            'name' => 'required|string|max:255',
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                \Illuminate\Validation\Rule::unique('users', 'email')->where(function ($query) {
                    return $query->where('status', 'active');
                })
            ],
            'password' => 'required|string|min:8|confirmed',
        ], $messages);

        try {
            DB::beginTransaction();

            $company = Company::create([
                'name' => $request->company_name,
                'status' => 'active',
                'plan' => 'basic',
            ]);

            $user = User::create([
                'company_id' => $company->id,
                'name' => $request->name,
                'email' => strtolower($request->email),
                'password' => Hash::make($request->password),
                'role' => 'super',
                'status' => 'active',
            ]);

            DB::commit();

            Auth::login($user);

            AccessLog::create([
                'user_id' => $user->id,
                'login_at' => now(),
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);

            $request->session()->put('just_logged_in', true);

            return redirect()->route('dashboard');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Hubo un error al procesar el registro. Por favor intente de nuevo.'])->withInput();
        }
    }
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $credentials['email'] = strtolower($credentials['email']);
        $credentials['status'] = 'active';

        if (Auth::attempt($credentials, $request->filled('remember'))) {
            $request->session()->regenerate();

            $user = Auth::user();

            // Check how many active companies this user belongs to (via employees table)
            $employeeCompanies = \App\Models\Employee::where('user_id', $user->id)
                ->whereHas('company', fn($q) => $q->where('status', 'active'))
                ->with('company')
                ->get();

            if ($employeeCompanies->count() > 1) {
                // Multiple companies — redirect to company selector
                // Don't set active_company_id yet, let the user choose
                AccessLog::create([
                    'user_id' => $user->id,
                    'login_at' => now(),
                    'ip_address' => $request->ip(),
                    'user_agent' => $request->userAgent(),
                ]);
                $request->session()->put('just_logged_in', true);
                return redirect()->route('select-company');
            }

            // Single or no employee record — auto-select primary company
            if ($employeeCompanies->count() === 1) {
                $request->session()->put('active_company_id', $employeeCompanies->first()->company_id);
            } else {
                $request->session()->put('active_company_id', $user->company_id);
            }

            AccessLog::create([
                'user_id' => $user->id,
                'login_at' => now(),
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);

            $request->session()->put('just_logged_in', true);

            if ($request->expectsJson()) {
                $user = Auth::user();
                $token = $user->createToken($request->device_name ?? 'Web API')->plainTextToken;
                return response()->json([
                    'token' => $token,
                    'user' => $user->load('company'),
                    'message' => 'Inicio de sesión exitoso.'
                ]);
            }

            return redirect()->intended(route('dashboard'));
        }

        return back()->withErrors([
            'email' => 'Las credenciales proporcionadas no coinciden.',
        ])->onlyInput('email');
    }

    public function logout(Request $request)
    {
        $lastLog = AccessLog::where('user_id', Auth::id())
            ->whereNull('logout_at')
            ->latest('login_at')
            ->first();

        if ($lastLog) {
            $lastLog->update(['logout_at' => now()]);
        }

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'Cierre de sesión exitoso.'
            ]);
        }

        return redirect()->route('login');
    }

    public function showSelectCompany()
    {
        $user = Auth::user();

        $companies = \App\Models\Employee::where('user_id', $user->id)
            ->whereHas('company', fn($q) => $q->where('status', 'active'))
            ->with(['company', 'user'])
            ->get();

        // If only one company or none, skip this screen
        if ($companies->count() <= 1) {
            $companyId = $companies->count() === 1
                ? $companies->first()->company_id
                : $user->company_id;
            session(['active_company_id' => $companyId]);
            return redirect()->route('dashboard');
        }

        return view('auth.select-company', compact('companies'));
    }

    public function selectCompany(Request $request)
    {
        $request->validate(['company_id' => 'required|integer|exists:companies,id']);

        $user = Auth::user();

        // Verify the user actually belongs to this company
        $belongs = \App\Models\Employee::where('user_id', $user->id)
            ->where('company_id', $request->company_id)
            ->exists();

        if (!$belongs && $user->company_id != $request->company_id) {
            return back()->withErrors(['company_id' => 'No tienes acceso a esta empresa.']);
        }

        session(['active_company_id' => $request->company_id]);
        return redirect()->route('dashboard');
    }

    public function switchCompany(Request $request)
    {
        $request->validate(['company_id' => 'required|integer|exists:companies,id']);

        $user = Auth::user();

        $belongs = \App\Models\Employee::where('user_id', $user->id)
            ->where('company_id', $request->company_id)
            ->exists();

        if (!$belongs && $user->company_id != $request->company_id) {
            return back()->withErrors(['company_id' => 'No tienes acceso a esta empresa.']);
        }

        session(['active_company_id' => $request->company_id]);
        return redirect()->route('dashboard');
    }
}
