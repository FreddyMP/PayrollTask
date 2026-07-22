<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Log;

class AuthController extends Controller
{
    /**
     * Handle a login request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(Request $request)
    {
        Log::info('Login request', $request->all());
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
            'device_name' => 'nullable|string',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => [__('Las credenciales proporcionadas son incorrectas.')],
            ]);
        }

        \App\Models\AccessLog::create([
            'user_id' => $user->id,
            'login_at' => now(),
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        $deviceName = $request->device_name ?? $request->header('User-Agent') ?? 'Unknown Device';
        $token = $user->createToken($deviceName)->plainTextToken;
        $employee = Employee::where('user_id', $user->id)->first();

        // Obtener superiores de la misma empresa
        $superiors = User::where('company_id', $user->company_id)
            ->whereIn('role', ['super', 'admin', 'supervisor'])
            ->where('id', '!=', $user->id)
            ->select('id', 'name', 'email', 'role', 'position', 'avatar')
            ->get();

        return response()->json([
            'token' => $token,
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role,
                'status' => $user->status,
                'phone' => $user->phone,
                'position' => $user->position,
                'avatar' => $user->avatar,
                'company' => $user->company,
                'employee_data' => $employee,
            ],
            'superiors' => $superiors,
            'message' => 'Inicio de sesión exitoso.'
        ]);
    }

    /**
     * Handle a logout request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout(Request $request)
    {
        $user = $request->user();

        $lastLog = \App\Models\AccessLog::where('user_id', $user->id)
            ->whereNull('logout_at')
            ->latest('login_at')
            ->first();

        if ($lastLog) {
            $lastLog->update(['logout_at' => now()]);
        }

        $user->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Cierre de sesión exitoso.'
        ]);
    }
}
