<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ref, watch } from 'vue';

defineProps({
    canResetPassword: {
        type: Boolean,
    },
    status: {
        type: String,
    },
});

const form = useForm({
    correo: '',
    contrasena: '',
    rol: 'doctor', // 'doctor' o 'paciente'
    remember: false,
});

// Dynamic placeholder based on selected role
const emailPlaceholder = ref('doctor@prodoctor.com');

watch(() => form.rol, (newRole) => {
    if (newRole === 'doctor') {
        emailPlaceholder.value = 'doctor@prodoctor.com';
    } else {
        emailPlaceholder.value = 'paciente@correo.com';
    }
});

const submit = () => {
    form.post(route('login'), {
        onFinish: () => form.reset('contrasena'),
    });
};
</script>

<template>
    <Head title="Iniciar Sesión - ProDoctor" />

    <div class="relative min-h-screen bg-gradient-to-br from-[#040A18] via-[#091526] to-[#11233B] flex items-center justify-center p-6 overflow-hidden select-none font-sans">
        
        <!-- Glowing background blobs -->
        <div class="absolute -top-40 -left-40 w-[500px] h-[500px] rounded-full bg-blue-600/10 blur-[150px] pointer-events-none"></div>
        <div class="absolute -bottom-40 -right-40 w-[500px] h-[500px] rounded-full bg-emerald-600/5 blur-[150px] pointer-events-none"></div>

        <div class="relative w-full max-w-md bg-white rounded-2xl shadow-[0_20px_50px_rgba(0,0,0,0.5)] border border-slate-100 p-8">
            
            <!-- Logo & Brand Header -->
            <div class="text-center mb-8">
                <div class="inline-flex w-14 h-14 rounded-2xl bg-gradient-to-tr from-blue-600 to-indigo-600 items-center justify-center shadow-lg shadow-blue-500/30 mb-4">
                    <!-- ECG Heartbeat + Medical Cross Icon -->
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-8 h-8 text-white">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 12h3.5l1.5-3.5 2.5 7 2-4.5 1.5 1H20" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v2m0 12v2M8 12H6m12 0h-2" />
                    </svg>
                </div>
                <h1 class="text-2xl font-extrabold text-slate-800 tracking-tight">ProDoctor</h1>
                <p class="text-[10px] text-slate-400 font-bold uppercase tracking-widest mt-1.5">SISTEMA DE GESTIÓN MÉDICA</p>
                <span class="inline-block mt-2 px-2 py-0.5 rounded-full bg-slate-50 text-[9px] font-bold text-slate-400 border border-slate-150">v1.0.0</span>
            </div>

            <!-- Validation Status -->
            <div v-if="status" class="mb-4 p-3 rounded-lg bg-emerald-50 border border-emerald-100 text-xs font-semibold text-emerald-600 text-center">
                {{ status }}
            </div>

            <form @submit.prevent="submit" class="space-y-6">
                <!-- Selector de Tipo de Usuario (Botonera Estilo Mockup) -->
                <div class="space-y-2">
                    <label class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block">TIPO DE USUARIO</label>
                    <div class="grid grid-cols-2 gap-4">
                        <!-- Botón Doctor -->
                        <button
                            type="button"
                            @click="form.rol = 'doctor'"
                            class="flex flex-col items-center gap-3 py-6 px-4 rounded-xl border-2 text-sm font-bold transition-all duration-200 relative overflow-hidden focus:outline-none cursor-pointer"
                            :class="form.rol === 'doctor'
                                ? 'bg-blue-50/50 border-blue-500 text-blue-600 shadow-md shadow-blue-500/10 scale-[1.02]'
                                : 'bg-slate-50 border-slate-200 hover:border-slate-350 text-slate-500 hover:text-slate-600 hover:bg-slate-100/50'"
                        >
                            <!-- Doctor Stethoscope/User Icon -->
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-7 h-7">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75a3.75 3.75 0 117.5 0 3.75 3.75 0 01-7.5 0zM5.5 20.118a7.5 7.5 0 0113 0A17.933 17.933 0 0112 21.75c-2.327 0-4.545-.445-6.5-1.632z" />
                            </svg>
                            Doctor
                            
                            <span v-if="form.rol === 'doctor'" class="absolute top-1.5 right-1.5 w-2 h-2 rounded-full bg-blue-500"></span>
                        </button>

                        <!-- Botón Paciente -->
                        <button
                            type="button"
                            @click="form.rol = 'paciente'"
                            class="flex flex-col items-center gap-3 py-6 px-4 rounded-xl border-2 text-sm font-bold transition-all duration-200 relative overflow-hidden focus:outline-none cursor-pointer"
                            :class="form.rol === 'paciente'
                                ? 'bg-emerald-50/50 border-emerald-500 text-emerald-600 shadow-md shadow-emerald-500/10 scale-[1.02]'
                                : 'bg-slate-50 border-slate-200 hover:border-slate-350 text-slate-500 hover:text-slate-600 hover:bg-slate-100/50'"
                        >
                            <!-- Patient User Group Icon -->
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-7 h-7">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" />
                            </svg>
                            Paciente
                            
                            <span v-if="form.rol === 'paciente'" class="absolute top-1.5 right-1.5 w-2 h-2 rounded-full bg-emerald-500"></span>
                        </button>
                    </div>
                </div>

                <!-- Input Correo Electrónico con Icono -->
                <div class="space-y-1.5">
                    <label for="correo" class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block">CORREO ELECTRÓNICO</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0l-7.5-4.615a2.25 2.25 0 01-1.07-1.916V6.75" />
                            </svg>
                        </div>
                        <input
                            id="correo"
                            type="email"
                            class="block w-full pl-11 pr-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-slate-800 text-sm placeholder-slate-400 focus:outline-none focus:bg-white focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 transition duration-150"
                            v-model="form.correo"
                            required
                            autofocus
                            autocomplete="username"
                            :placeholder="emailPlaceholder"
                        />
                    </div>
                    <span v-if="form.errors.correo" class="text-xs text-rose-500 font-medium block mt-1">{{ form.errors.correo }}</span>
                </div>

                <!-- Input Contraseña con Icono -->
                <div class="space-y-1.5">
                    <label for="contrasena" class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block">CONTRASEÑA</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z" />
                            </svg>
                        </div>
                        <input
                            id="contrasena"
                            type="password"
                            class="block w-full pl-11 pr-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-slate-800 text-sm placeholder-slate-400 focus:outline-none focus:bg-white focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 transition duration-150"
                            v-model="form.contrasena"
                            required
                            autocomplete="current-password"
                            placeholder="••••••••"
                        />
                    </div>
                    <span v-if="form.errors.contrasena" class="text-xs text-rose-500 font-medium block mt-1">{{ form.errors.contrasena }}</span>
                </div>

                <!-- Recordarme & Forgot Links -->
                <div class="flex items-center justify-between text-xs">
                    <label class="flex items-center text-slate-500 select-none cursor-pointer">
                        <input
                            type="checkbox"
                            name="remember"
                            v-model="form.remember"
                            class="rounded border-slate-350 text-blue-600 focus:ring-blue-500 cursor-pointer w-4 h-4 mr-2"
                        />
                        Recordarme
                    </label>
                    <Link
                        v-if="canResetPassword"
                        :href="route('password.request')"
                        class="text-blue-600 hover:text-blue-700 hover:underline font-bold"
                    >
                        ¿Olvidaste tu contraseña?
                    </Link>
                </div>

                <!-- Botón de Envío -->
                <div>
                    <button
                        type="submit"
                        class="w-full py-3.5 bg-gradient-to-r from-blue-600 to-indigo-650 hover:from-blue-750 hover:to-indigo-750 text-white rounded-xl text-sm font-bold shadow-lg shadow-blue-500/25 active:scale-[0.98] transition duration-150 flex items-center justify-center gap-2 cursor-pointer disabled:opacity-50 disabled:cursor-not-allowed"
                        :disabled="form.processing"
                    >
                        <span v-if="form.processing">Procesando...</span>
                        <span v-else>Iniciar Sesión</span>
                    </button>
                </div>
            </form>

            <!-- Bottom Registration Link -->
            <div class="mt-8 text-center border-t border-slate-100 pt-6">
                <p class="text-xs text-slate-500">
                    ¿No tienes una cuenta de paciente?
                    <Link :href="route('register')" class="text-blue-600 hover:text-blue-700 hover:underline font-extrabold ml-1">Regístrate gratis</Link>
                </p>
            </div>
        </div>
    </div>
</template>
