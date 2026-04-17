<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Mail\ResetPasswordMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;

class PasswordResetController extends Controller
{
    public function showLinkRequestForm()
    {
        return view('auth.forgot-password');
    }

    public function sendResetLinkEmail(Request $request)
    {
        $request->validate(['email' => 'required|email']);
        
        // Normalizar correo a minúsculas
        $email = strtolower($request->email);

        $user = User::where('email', $email)->first();

        if (!$user) {
            return back()->withErrors(['email' => 'No encontramos un usuario con ese correo electrónico.']);
        }

        // Generar token
        $token = Password::createToken($user);

        // Enviar correo
        Mail::to($user->email)->send(new ResetPasswordMail($token, $user->email));

        return back()->with('status', 'Hemos enviado un enlace de recuperación a tu correo electrónico.');
    }

    public function showResetForm(Request $request, $token)
    {
        return view('auth.reset-password')->with(
            ['token' => $token, 'email' => $request->email]
        );
    }

    public function reset(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|confirmed|min:8',
        ]);

        $email = strtolower($request->email);

        $user = User::where('email', $email)->first();

        if (!$user) {
            return back()->withErrors(['email' => 'No encontramos un usuario con ese correo electrónico.']);
        }

        // Validar token
        if (!Password::tokenExists($user, $request->token)) {
            return back()->withErrors(['email' => 'Este token de recuperación de contraseña es inválido.']);
        }

        // Actualizar contraseña
        $user->forceFill([
            'password' => Hash::make($request->password),
            'remember_token' => Str::random(60),
        ])->save();

        // Eliminar token usado
        Password::deleteToken($user);

        return redirect()->route('login')->with('status', 'Tu contraseña ha sido restablecida exitosamente.');
    }
}
