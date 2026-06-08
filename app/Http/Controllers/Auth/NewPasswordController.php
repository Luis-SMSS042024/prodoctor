<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Inertia\Response;

class NewPasswordController extends Controller
{
    /**
     * Display the password reset view.
     */
    public function create(Request $request): Response|\Illuminate\Http\RedirectResponse
    {
        if (!session('reset_verified') || !session('reset_email')) {
            return redirect()->route('password.request')->withErrors(['email' => 'Debes verificar tu correo primero.']);
        }

        return Inertia::render('Auth/ResetPassword', [
            'email' => session('reset_email'),
        ]);
    }

    /**
     * Handle an incoming new password request.
     *
     * @throws ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        if (!session('reset_verified') || !session('reset_email')) {
            return redirect()->route('password.request')->withErrors(['email' => 'Debes verificar tu correo primero.']);
        }

        $request->validate([
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ], [
            'password.required' => 'La nueva contraseña es obligatoria.',
            'password.confirmed' => 'Las contraseñas no coinciden.',
        ]);

        $user = \App\Models\User::where('correo', session('reset_email'))->first();

        if (!$user) {
            return redirect()->route('password.request')->withErrors(['email' => 'Usuario no encontrado.']);
        }

        $user->update([
            'clave' => Hash::make($request->password),
        ]);

        // Clean password reset session variables
        session()->forget(['reset_email', 'reset_otp', 'reset_otp_expires_at', 'reset_verified']);

        return redirect()->route('login')->with('status', 'Tu contraseña ha sido restablecida con éxito.');
    }
}
