<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Paciente;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rules;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Inertia\Response;
use App\Helpers\MailHelper;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): Response
    {
        return Inertia::render('Auth/Register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'nombres' => 'required|string|regex:/^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]+$/|max:100',
            'apellidos' => 'required|string|regex:/^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]+$/|max:100',
            'correo' => 'required|string|lowercase|email|max:255|unique:usuarios,correo',
            'telefono' => 'required|string|max:30',
            'contrasena' => ['required', 'confirmed', Rules\Password::defaults()],
        ], [
            'nombres.required' => 'El nombre es obligatorio.',
            'nombres.regex' => 'El nombre solo debe contener letras.',
            'apellidos.required' => 'El apellido es obligatorio.',
            'apellidos.regex' => 'El apellido solo debe contener letras.',
            'correo.required' => 'El correo electrónico es obligatorio.',
            'correo.email' => 'El correo electrónico debe tener un formato de correo válido.',
            'correo.unique' => 'Este correo ya está registrado en el sistema.',
            'telefono.required' => 'El teléfono es obligatorio.',
            'contrasena.required' => 'La contraseña es obligatoria.',
            'contrasena.confirmed' => 'La confirmación de la contraseña no coincide.',
        ]);

        // Generate OTP
        $otp = rand(100000, 999999);

        // Store registration details temporarily in session
        session([
            'reg_nombres' => $request->nombres,
            'reg_apellidos' => $request->apellidos,
            'reg_correo' => $request->correo,
            'reg_telefono' => $request->telefono,
            'reg_contrasena' => Hash::make($request->contrasena),
            'reg_otp_code' => $otp,
            'reg_otp_expires_at' => now()->addMinutes(15),
        ]);

        // Send OTP to user's email
        MailHelper::sendOtpMail(
            $request->correo,
            'Código de Verificación de Registro - ProDoctor',
            'Verificación de Registro',
            'Tu código de verificación para completar tu registro de paciente en ProDoctor es:',
            $otp
        );

        return redirect()->route('register.verify-otp')->with('status', "Código enviado. (Para desarrollo, el código es: {$otp})");
    }

    /**
     * Render the registration OTP verification view.
     */
    public function showOtpVerify(): Response|RedirectResponse
    {
        if (!session()->has('reg_correo')) {
            return redirect()->route('register');
        }

        return Inertia::render('Auth/RegisterVerifyOtp', [
            'status' => session('status'),
            'email' => session('reg_correo'),
        ]);
    }

    /**
     * Verify the entered registration OTP and create the user.
     */
    public function verifyOtp(Request $request): RedirectResponse
    {
        $request->validate([
            'code' => 'required|string|size:6',
        ], [
            'code.required' => 'El código es obligatorio.',
            'code.size' => 'El código debe tener exactamente 6 caracteres.',
        ]);

        if (!session()->has('reg_correo') || !session()->has('reg_otp_code') || !session()->has('reg_otp_expires_at')) {
            return redirect()->route('register')->withErrors(['correo' => 'La sesión de registro ha expirado.']);
        }

        $expectedCode = session('reg_otp_code');
        $expiresAt = session('reg_otp_expires_at');

        if (now()->greaterThan($expiresAt)) {
            session()->forget(['reg_nombres', 'reg_apellidos', 'reg_correo', 'reg_telefono', 'reg_contrasena', 'reg_otp_code', 'reg_otp_expires_at']);
            return redirect()->route('register')->withErrors(['correo' => 'El código de registro ha expirado. Comienza de nuevo.']);
        }

        if ($request->code !== (string)$expectedCode) {
            return back()->withErrors(['code' => 'El código ingresado es incorrecto.']);
        }

        // Create the user in database
        $user = User::create([
            'nombre_usuario' => session('reg_nombres') . ' ' . session('reg_apellidos'),
            'correo' => session('reg_correo'),
            'clave' => session('reg_contrasena'),
            'rol' => 'paciente',
        ]);

        // Create the linked Paciente
        Paciente::create([
            'id_usuario' => $user->id_usuario,
            'nombres' => session('reg_nombres'),
            'apellidos' => session('reg_apellidos'),
            'fecha_nacimiento' => '1990-01-01',
            'sexo' => 'Otro',
            'telefono' => session('reg_telefono') ?? 'Sin registrar',
            'direccion' => 'Sin registrar',
        ]);

        // Clean session
        session()->forget(['reg_nombres', 'reg_apellidos', 'reg_correo', 'reg_telefono', 'reg_contrasena', 'reg_otp_code', 'reg_otp_expires_at']);

        event(new Registered($user));

        // Authenticate user right away (optional but typical) or just let success screen handle login redirection
        Auth::login($user);

        return redirect()->route('register.success');
    }

    /**
     * Render the successful registration screen.
     */
    public function showSuccess(): Response|RedirectResponse
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        return Inertia::render('Auth/RegisterSuccess', [
            'nombre' => auth()->user()->nombre_usuario,
            'correo' => auth()->user()->correo,
        ]);
    }

    /**
     * Resend the registration OTP email.
     */
    public function resendOtp(Request $request)
    {
        if (!session()->has('reg_correo') || !session()->has('reg_otp_code')) {
            if ($request->wantsJson()) {
                return response()->json(['success' => false, 'message' => 'La sesión de registro ha expirado.'], 422);
            }
            return redirect()->route('register')->withErrors(['correo' => 'La sesión de registro ha expirado.']);
        }

        // Generate new OTP
        $otp = rand(100000, 999999);
        session([
            'reg_otp_code' => $otp,
            'reg_otp_expires_at' => now()->addMinutes(10),
        ]);

        $correo = session('reg_correo');

        // Send OTP
        $sent = MailHelper::sendOtpMail(
            $correo,
            'Nuevo Código de Verificación de Registro - ProDoctor',
            'Verificación de Registro',
            'Tu nuevo código de verificación para completar tu registro de paciente en ProDoctor es:',
            $otp
        );

        if (!$sent && $request->wantsJson()) {
            return response()->json(['success' => false, 'message' => 'Error al enviar el correo electrónico.'], 500);
        }

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Nuevo código de verificación enviado.',
                'otp' => $otp
            ]);
        }

        return back()->with('status', "Nuevo código enviado. (Para desarrollo, el código es: {$otp})");
    }
}
