<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class PasswordController extends Controller
{
    /**
     * Update the user's password.
     */
    public function update(Request $request): RedirectResponse
    {
        $user = $request->user();

        // 2FA Verification for all roles
        $request->validate([
            'codigo_otp' => ['required', 'string', 'size:6'],
        ], [
            'codigo_otp.required' => 'El código de verificación es obligatorio.',
            'codigo_otp.size' => 'El código de verificación debe tener 6 caracteres.',
        ]);

        if (!session()->has('password_otp_code') || !session()->has('password_otp_expires_at')) {
            return back()->withErrors(['codigo_otp' => 'Solicita un código de verificación primero.']);
        }

        if (now()->greaterThan(session('password_otp_expires_at'))) {
            session()->forget(['password_otp_code', 'password_otp_expires_at']);
            return back()->withErrors(['codigo_otp' => 'El código ha expirado. Solicita uno nuevo.']);
        }

        if ($request->codigo_otp !== (string)session('password_otp_code')) {
            return back()->withErrors(['codigo_otp' => 'El código de verificación es incorrecto.']);
        }

        // Clean session OTP
        session()->forget(['password_otp_code', 'password_otp_expires_at']);

        $validated = $request->validate([
            'contrasena_actual' => ['required', 'current_password'],
            'contrasena' => ['required', Password::defaults(), 'confirmed'],
        ]);

        $user->update([
            'clave' => Hash::make($validated['contrasena']),
        ]);

        return back();
    }
}
