<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use App\Models\User;
use Inertia\Inertia;
use Inertia\Response;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): Response
    {
        return Inertia::render('Auth/Login', [
            'canResetPassword' => Route::has('password.request'),
            'status' => session('status'),
        ]);
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $user = Auth::user();

        if ($user->login_2fa) {
            // Log out immediately so session is not active until code is verified
            Auth::logout();

            // Generate OTP
            $otp = rand(100000, 999999);
            
            // Store details in session
            session([
                'otp_user_id' => $user->id_usuario,
                'otp_code' => $otp,
                'otp_expires_at' => now()->addMinutes(10),
            ]);

            // Send OTP to user's email
            try {
                Mail::raw("Tu código de verificación para ProDoctor es: {$otp}", function ($message) use ($user) {
                    $message->to($user->correo)
                        ->subject('Código de verificación de ProDoctor');
                });
            } catch (\Exception $e) {
                Log::info("Failed to send OTP email to {$user->correo}: " . $e->getMessage());
            }

            return redirect()->route('otp.verify')->with('status', "Código enviado. (Para desarrollo, el código es: {$otp})");
        }

        $request->session()->regenerate();

        return redirect()->intended(route('dashboard', absolute: false));
    }

    /**
     * Render the OTP verification page.
     */
    public function showOtpVerify(): Response|RedirectResponse
    {
        if (!session()->has('otp_user_id')) {
            return redirect()->route('login');
        }

        return Inertia::render('Auth/VerifyOtp', [
            'status' => session('status'),
        ]);
    }

    /**
     * Verify the entered OTP and authenticate.
     */
    public function verifyOtp(Request $request): RedirectResponse
    {
        $request->validate([
            'code' => 'required|string|size:6',
        ], [
            'code.required' => 'El código es obligatorio.',
            'code.size' => 'El código debe tener exactamente 6 caracteres.',
        ]);

        if (!session()->has('otp_user_id') || !session()->has('otp_code') || !session()->has('otp_expires_at')) {
            return redirect()->route('login')->withErrors(['correo' => 'La sesión de verificación ha expirado.']);
        }

        $userId = session('otp_user_id');
        $expectedCode = session('otp_code');
        $expiresAt = session('otp_expires_at');

        if (now()->greaterThan($expiresAt)) {
            session()->forget(['otp_user_id', 'otp_code', 'otp_expires_at']);
            return redirect()->route('login')->withErrors(['correo' => 'El código ha expirado. Inténtelo de nuevo.']);
        }

        if ($request->code !== (string)$expectedCode) {
            return back()->withErrors(['code' => 'El código ingresado es incorrecto.']);
        }

        // Clean session
        session()->forget(['otp_user_id', 'otp_code', 'otp_expires_at']);

        // Log the user in
        $user = User::find($userId);
        if ($user) {
            Auth::login($user);
            $request->session()->regenerate();
            return redirect()->intended(route('dashboard', absolute: false));
        }

        return redirect()->route('login');
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
