<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;
use Inertia\Response;
use App\Helpers\MailHelper;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): Response
    {
        return Inertia::render('Profile/Edit', [
            'mustVerifyEmail' => $request->user() instanceof MustVerifyEmail,
            'status' => session('status'),
        ]);
    }

    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $user = $request->user();
        $data = $request->validated();

        // 2FA Email verification check for all roles
        if ($request->correo !== $user->correo) {
            $request->validate([
                'contrasena_actual' => ['required', 'current_password'],
                'codigo_otp' => ['required', 'string', 'size:6'],
            ], [
                'contrasena_actual.required' => 'Debes ingresar tu contraseña actual para cambiar el correo.',
                'contrasena_actual.current_password' => 'La contraseña actual ingresada es incorrecta.',
                'codigo_otp.required' => 'El código de verificación es obligatorio.',
                'codigo_otp.size' => 'El código de verificación debe tener 6 caracteres.',
            ]);

            if (!session()->has('email_otp_code') || !session()->has('email_otp_target') || !session()->has('email_otp_expires_at')) {
                return back()->withErrors(['codigo_otp' => 'Solicita un código de verificación primero.']);
            }

            if (now()->greaterThan(session('email_otp_expires_at'))) {
                session()->forget(['email_otp_code', 'email_otp_expires_at', 'email_otp_target']);
                return back()->withErrors(['codigo_otp' => 'El código de verificación ha expirado. Solicita uno nuevo.']);
            }

            if ($request->codigo_otp !== (string)session('email_otp_code') || $request->correo !== session('email_otp_target')) {
                return back()->withErrors(['codigo_otp' => 'El código de verificación es incorrecto o no coincide con el nuevo correo.']);
            }

            // Clean session OTP
            session()->forget(['email_otp_code', 'email_otp_expires_at', 'email_otp_target']);
        }

        if ($request->hasFile('foto')) {
            // Delete old photo if exists
            if ($user->foto && file_exists(public_path($user->foto))) {
                @unlink(public_path($user->foto));
            }

            $file = $request->file('foto');
            $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('uploads/avatars'), $filename);
            $data['foto'] = 'uploads/avatars/' . $filename;
        }

        $user->fill($data);
        $user->save();

        return back();
    }

    /**
     * Send OTP for profile changes (email update or password update).
     */
    public function sendOtp(Request $request): \Illuminate\Http\JsonResponse
    {
        $request->validate([
            'type' => 'required|in:email,password',
            'email' => 'nullable|email',
        ]);

        $user = auth()->user();
        $otp = rand(100000, 999999);
        $expiresAt = now()->addMinutes(10);

        if ($request->type === 'password') {
            session([
                'password_otp_code' => $otp,
                'password_otp_expires_at' => $expiresAt,
            ]);
            $targetEmail = $user->correo;
        } else {
            // Email change OTP
            $request->validate([
                'email' => 'required|email|unique:usuarios,correo,' . $user->id_usuario . ',id_usuario',
            ], [
                'email.required' => 'El nuevo correo electrónico es obligatorio.',
                'email.unique' => 'Este correo ya está registrado por otro usuario.',
            ]);
            
            session([
                'email_otp_code' => $otp,
                'email_otp_expires_at' => $expiresAt,
                'email_otp_target' => $request->email,
            ]);
            
            $targetEmail = $request->email;
        }

        // Send OTP via email
        $topic = $request->type === 'password' ? 'cambio de contraseña' : 'cambio de correo';
        MailHelper::sendOtpMail(
            $targetEmail,
            'Código de verificación de ProDoctor',
            'Verificación de seguridad',
            "Tu código de verificación para autorizar tu {$topic} en ProDoctor es:",
            $otp
        );

        return response()->json([
            'success' => true,
            'message' => 'Código enviado correctamente.',
            'otp' => $otp, // Return for easy dev/test access
        ]);
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validate([
            'contrasena' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}
