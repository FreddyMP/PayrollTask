<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class SettingsController extends Controller
{
    public function index()
    {
        return view('settings.index');
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'password' => ['required', 'confirmed', Password::min(8)],
        ]);

        $user = Auth::user();

        if (!Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => 'La contraseña actual es incorrecta.']);
        }

        $user->update(['password' => Hash::make($request->password)]);

        return back()->with('success', 'Contraseña actualizada exitosamente.');
    }

    public function updateEmail(Request $request)
    {
        $messages = [
            'email.unique' => 'Ese correo no está disponible.',
        ];

        $request->validate([
            'email' => [
                'required',
                'email',
                \Illuminate\Validation\Rule::unique('users', 'email')->where(function ($query) {
                    return $query->where('status', 'active');
                })->ignore(Auth::id())
            ],
            'password' => 'required',
        ], $messages);

        if (!Hash::check($request->password, Auth::user()->password)) {
            return back()->withErrors(['password' => 'La contraseña es incorrecta.']);
        }

        Auth::user()->update(['email' => strtolower($request->email)]);

        return back()->with('success', 'Correo actualizado exitosamente.');
    }

    public function updateProfile(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
        ]);

        Auth::user()->update($request->only('name', 'phone'));

        return back()->with('success', 'Perfil actualizado exitosamente.');
    }
}
