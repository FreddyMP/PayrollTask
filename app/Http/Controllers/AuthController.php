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

            AccessLog::create([
                'user_id' => Auth::id(),
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
}
