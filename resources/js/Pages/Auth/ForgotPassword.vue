<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';

defineProps({
    status: {
        type: String,
    },
});

const form = useForm({
    email: '',
});

const submit = () => {
    form.post(route('password.email'));
};
</script>

<template>
    <Head title="Recuperar Contraseña - ProDoctor" />

    <div class="relative min-h-screen bg-gradient-to-br from-[#040A18] via-[#091526] to-[#11233B] flex items-center justify-center p-6 overflow-hidden select-none font-sans">
        
        <!-- Glowing background blobs -->
        <div class="absolute -top-40 -left-40 w-[500px] h-[500px] rounded-full bg-blue-600/10 blur-[150px] pointer-events-none"></div>
        <div class="absolute -bottom-40 -right-40 w-[500px] h-[500px] rounded-full bg-emerald-600/5 blur-[150px] pointer-events-none"></div>

        <!-- Glassmorphism Card -->
        <div class="relative w-full max-w-md bg-white/90 backdrop-blur-md rounded-2xl shadow-[0_20px_50px_rgba(0,0,0,0.4)] border border-white/20 p-8">
            
            <!-- Logo & Brand Header -->
            <div class="text-center mb-6">
                <div class="inline-flex w-14 h-14 rounded-2xl bg-gradient-to-tr from-blue-600 to-indigo-650 items-center justify-center shadow-lg shadow-blue-500/30 mb-4">
                    <!-- ECG Heartbeat + Medical Cross Icon -->
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-8 h-8 text-white">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 12h3.5l1.5-3.5 2.5 7 2-4.5 1.5 1H20" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v2m0 12v2M8 12H6m12 0h-2" />
                    </svg>
                </div>
                <h1 class="text-2xl font-extrabold text-slate-800 tracking-tight">Recuperar Contraseña</h1>
                <p class="text-[10px] text-slate-400 font-bold uppercase tracking-widest mt-1.5">Restablece tu acceso a ProDoctor</p>
            </div>

            <!-- Description -->
            <div class="mb-6 text-xs text-slate-500 text-center leading-relaxed">
                ¿Olvidaste tu contraseña? No te preocupes. Escribe tu correo electrónico de registro y te enviaremos un enlace de restauración para elegir una nueva.
            </div>

            <!-- Validation Status Alert -->
            <div v-if="status" class="mb-4 p-3 rounded-lg bg-emerald-50 border border-emerald-100 text-xs font-semibold text-emerald-600 text-center">
                {{ status }}
            </div>

            <form @submit.prevent="submit" class="space-y-4">
                <!-- Input Correo Electrónico con Icono -->
                <div class="space-y-1.5">
                    <label for="email" class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block">CORREO ELECTRÓNICO</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0l-7.5-4.615a2.25 2.25 0 01-1.07-1.916V6.75" />
                            </svg>
                        </div>
                        <input
                            id="email"
                            type="email"
                            class="block w-full pl-11 pr-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-slate-800 text-sm placeholder-slate-400 focus:outline-none focus:bg-white focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 transition duration-150"
                            v-model="form.email"
                            required
                            autofocus
                            autocomplete="username"
                            placeholder="ejemplo@prodoctor.com"
                        />
                    </div>
                    <span v-if="form.errors.email" class="text-xs text-rose-500 font-medium block mt-1">{{ form.errors.email }}</span>
                </div>

                <!-- Botón de Envío -->
                <div class="pt-2">
                    <button
                        type="submit"
                        class="w-full py-3.5 bg-gradient-to-r from-blue-600 to-indigo-650 hover:from-blue-750 hover:to-indigo-750 text-white rounded-xl text-sm font-bold shadow-lg shadow-blue-500/25 active:scale-[0.98] transition duration-150 flex items-center justify-center gap-2 cursor-pointer disabled:opacity-50 disabled:cursor-not-allowed"
                        :disabled="form.processing"
                    >
                        <span v-if="form.processing">Enviando enlace...</span>
                        <span v-else>Enviar Enlace de Recuperación</span>
                    </button>
                </div>
            </form>

            <!-- Bottom Back to Login Link -->
            <div class="mt-6 text-center border-t border-slate-100 pt-5">
                <Link :href="route('login')" class="text-xs text-blue-600 hover:text-blue-700 hover:underline font-extrabold flex items-center justify-center gap-1.5 cursor-pointer">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-3.5 h-3.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5" />
                    </svg>
                    Volver al Inicio de Sesión
                </Link>
            </div>
        </div>
    </div>
</template>
