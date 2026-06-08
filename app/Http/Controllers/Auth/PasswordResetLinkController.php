<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use App\Models\User;
use Inertia\Inertia;
use Inertia\Response;

class PasswordResetLinkController extends Controller
{
    /**
     * Display the password reset link request view.
     */
    public function create(): Response
    {
        return Inertia::render('Auth/ForgotPassword', [
            'status' => session('status'),
        ]);
    }

    /**
     * Handle an incoming password reset link request.
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'email' => 'required|email|exists:usuarios,correo',
        ], [
            'email.required' => 'El correo electrónico es obligatorio.',
            'email.email' => 'El formato del correo es inválido.',
            'email.exists' => 'Este correo no está registrado en el sistema.',
        ]);

        $otp = rand(100000, 999999);

        session([
            'reset_email' => $request->email,
            'reset_otp' => $otp,
            'reset_otp_expires_at' => now()->addMinutes(10),
        ]);

        try {
            Mail::raw("Tu código de recuperación para restablecer tu contraseña en ProDoctor es: {$otp}", function ($message) use ($request) {
                $message->to($request->email)
                    ->subject('Código de recuperación de contraseña - ProDoctor');
            });
        } catch (\Exception $e) {
            Log::info("Failed to send password reset OTP to {$request->email}: " . $e->getMessage());
        }

        return redirect()->route('password.verify-otp')->with('status', "Código enviado. (Para desarrollo, el código es: {$otp})");
    }

    /**
     * Render the OTP verification page for password reset.
     */
    public function showOtpVerify(): Response|RedirectResponse
    {
        if (!session()->has('reset_email')) {
            return redirect()->route('password.request');
        }

        return Inertia::render('Auth/ForgotPasswordVerifyOtp', [
            'status' => session('status'),
            'email' => session('reset_email'),
        ]);
    }

    /**
     * Verify the entered OTP.
     */
    public function verifyOtp(Request $request): RedirectResponse
    {
        $request->validate([
            'code' => 'required|string|size:6',
        ], [
            'code.required' => 'El código es obligatorio.',
            'code.size' => 'El código debe tener exactamente 6 caracteres.',
        ]);

        if (!session()->has('reset_email') || !session()->has('reset_otp') || !session()->has('reset_otp_expires_at')) {
            return redirect()->route('password.request')->withErrors(['email' => 'La sesión ha expirado.']);
        }

        $expectedCode = session('reset_otp');
        $expiresAt = session('reset_otp_expires_at');

        if (now()->greaterThan($expiresAt)) {
            session()->forget(['reset_email', 'reset_otp', 'reset_otp_expires_at']);
            return redirect()->route('password.request')->withErrors(['email' => 'El código de verificación ha expirado.']);
        }

        if ($request->code !== (string)$expectedCode) {
            return back()->withErrors(['code' => 'El código de verificación es incorrecto.']);
        }

        session(['reset_verified' => true]);

        return redirect()->route('password.reset');
    }
}
