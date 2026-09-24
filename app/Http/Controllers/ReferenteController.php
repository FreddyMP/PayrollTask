<?php

namespace App\Http\Controllers;

use App\Models\Referente;
use App\Models\ReferenteDatosBancarios;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class ReferenteController extends Controller
{
    // -----------------------------------------------------------------------
    // Auth
    // -----------------------------------------------------------------------

    public function showRegister()
    {
        return view('referentes.register');
    }

    public function register(Request $request)
    {
        $request->validate([
            'nombre_completo' => 'required|string|max:255',
            'cedula_rnc'      => 'required|string|max:20|unique:referentes,cedula_rnc',
            'telefono'        => 'required|string|max:20',
            'password'        => 'required|string|min:8|confirmed',
        ], [
            'cedula_rnc.unique' => 'Esa cédula o RNC ya está registrada.',
        ]);

        $codigo = Referente::generateCode($request->nombre_completo);

        $referente = Referente::create([
            'nombre_completo' => $request->nombre_completo,
            'cedula_rnc'      => $request->cedula_rnc,
            'telefono'        => $request->telefono,
            'password'        => Hash::make($request->password),
            'codigo_referido' => $codigo,
            'status'          => 'active',
            'primera_vez'     => true,
        ]);

        Auth::guard('referente')->login($referente);

        // Redirect to dashboard; the banking modal will fire on first load
        return redirect()->route('referentes.dashboard')
            ->with('show_banking_modal', true);
    }

    public function showLogin()
    {
        return view('referentes.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'cedula_rnc' => 'required|string',
            'password'   => 'required|string',
        ]);

        $referente = Referente::where('cedula_rnc', $request->cedula_rnc)
            ->where('status', 'active')
            ->first();

        if (!$referente || !Hash::check($request->password, $referente->password)) {
            return back()->withErrors([
                'cedula_rnc' => 'Las credenciales proporcionadas no coinciden.',
            ])->onlyInput('cedula_rnc');
        }

        Auth::guard('referente')->login($referente, $request->filled('remember'));
        $request->session()->regenerate();

        return redirect()->route('referentes.dashboard');
    }

    public function logout(Request $request)
    {
        Auth::guard('referente')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('referentes.login');
    }

    // -----------------------------------------------------------------------
    // Dashboard
    // -----------------------------------------------------------------------

    public function dashboard()
    {
        /** @var Referente $referente */
        $referente = Auth::guard('referente')->user();

        // Empresas referidas con su suscripción
        $empresas = $referente->companies()
            ->withCount('users')
            ->orderByDesc('created_at')
            ->get();

        // Calcular comisiones (15% aplica desde el 3er mes)
        $precios = [
            'starter'    => 2000,
            'growth'     => 4000,
            'business'   => 7000,
            'enterprise' => 12000,
        ];

        $totalComision = 0;
        $comisionPendiente = 0;

        foreach ($empresas as $empresa) {
            if (!$empresa->subscription_plan) {
                continue;
            }
            $precioBase = $precios[$empresa->subscription_plan] ?? 0;
            $comision   = $precioBase * 0.15;

            // Aplica a partir del 3er mes
            $meses = (int) $empresa->created_at->diffInMonths(now());
            if ($meses >= 2) {
                $totalComision += $comision;
            } else {
                $comisionPendiente += $comision;
            }
        }

        $showBankingModal   = !$referente->datosBancarios;
        $showWelcomeModal   = $referente->primera_vez;

        // Mark as no longer first time
        if ($referente->primera_vez) {
            $referente->update(['primera_vez' => false]);
        }

        $referralUrl = url('/register?ref=' . $referente->codigo_referido);

        return view('referentes.dashboard', compact(
            'referente',
            'empresas',
            'totalComision',
            'comisionPendiente',
            'showBankingModal',
            'showWelcomeModal',
            'referralUrl'
        ));
    }

    // -----------------------------------------------------------------------
    // Banking Info
    // -----------------------------------------------------------------------

    public function saveBankingInfo(Request $request)
    {
        $request->validate([
            'banco'         => 'required|in:Popular,BHD,Banreservas,Banesco,Qik,Promerica',
            'tipo_cuenta'   => 'required|in:Corriente,Ahorro',
            'numero_cuenta' => 'required|string|max:50',
        ]);

        /** @var Referente $referente */
        $referente = Auth::guard('referente')->user();

        ReferenteDatosBancarios::updateOrCreate(
            ['referente_id' => $referente->id],
            [
                'banco'         => $request->banco,
                'tipo_cuenta'   => $request->tipo_cuenta,
                'numero_cuenta' => $request->numero_cuenta,
            ]
        );

        return back()->with('success', 'Datos bancarios guardados correctamente.');
    }
}
