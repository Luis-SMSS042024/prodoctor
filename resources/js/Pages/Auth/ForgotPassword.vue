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

    <div class="relative min-h-screen bg-[#040A18] flex flex-col items-center justify-center p-6 overflow-hidden select-none font-sans text-slate-300">
        
        <!-- Glowing background blobs -->
        <div class="absolute -top-40 -left-40 w-[500px] h-[500px] rounded-full bg-blue-600/10 blur-[150px] pointer-events-none"></div>
        <div class="absolute -bottom-40 -right-40 w-[500px] h-[500px] rounded-full bg-[#00dfb2]/5 blur-[150px] pointer-events-none"></div>

        <!-- Glassmorphism Card -->
        <div class="relative w-full max-w-md bg-[#07111e] border border-[#13283f] rounded-2xl shadow-[0_20px_50px_rgba(0,0,0,0.6)] p-8 z-10">
            
            <!-- Logo & Brand Header -->
            <div class="text-center mb-6 flex flex-col items-center">
                <div class="inline-flex w-14 h-14 rounded-2xl bg-gradient-to-tr from-emerald-500/20 to-teal-500/10 border border-[#00dfb2]/20 items-center justify-center shadow-lg shadow-emerald-500/5 mb-3">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-7 h-7 text-[#00dfb2]">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 12h3.5l1.5-3.5 2.5 7 2-4.5 1.5 1H20" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v2m0 12v2M8 12H6m12 0h-2" />
                    </svg>
                </div>
                <h1 class="text-2xl font-bold text-white tracking-tight">Recuperar Contraseña</h1>
                <p class="text-[10px] text-slate-400 font-bold uppercase tracking-widest mt-1">RESTABLECE TU ACCESO A PRODOCTOR</p>
            </div>

            <!-- Description -->
            <div class="mb-6 text-xs text-slate-400 text-center leading-relaxed">
                ¿Olvidaste tu contraseña? Escribe tu correo electrónico de registro y te enviaremos un código de recuperación de 6 dígitos.
            </div>

            <!-- Validation Status Alert -->
            <div v-if="status" class="mb-4 p-3.5 bg-emerald-500/10 border border-emerald-500/20 rounded-xl text-xs font-semibold text-emerald-400 text-center leading-relaxed">
                {{ status }}
            </div>

            <form @submit.prevent="submit" class="space-y-5">
                <!-- Input Correo Electrónico con Icono -->
                <div class="space-y-1.5">
                    <label for="email" class="text-[10px] font-bold text-[#00dfb2] uppercase tracking-widest block">CORREO ELECTRÓNICO</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-500">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0l-7.5-4.615a2.25 2.25 0 01-1.07-1.916V6.75" />
                            </svg>
                        </div>
                        <input
                            id="email"
                            type="email"
                            class="block w-full pl-11 pr-4 py-3 bg-[#040a12] border border-[#162d4a] rounded-lg text-white text-sm placeholder-slate-650 focus:outline-none focus:border-[#00dfb2] focus:ring-1 focus:ring-[#00dfb2]/30 transition"
                            v-model="form.email"
                            required
                            autofocus
                            autocomplete="username"
                            placeholder="correo@ejemplo.com"
                        />
                    </div>
                    <span v-if="form.errors.email" class="text-xs text-rose-500 font-medium block mt-1">{{ form.errors.email }}</span>
                </div>

                <!-- Botón de Envío -->
                <div class="pt-2">
                    <button
                        type="submit"
                        class="w-full py-3.5 bg-gradient-to-r from-[#00dfb2] to-[#00a887] hover:from-[#00ffd5] hover:to-[#00c79f] text-slate-950 font-extrabold rounded-lg shadow-lg shadow-[#00dfb2]/10 active:scale-[0.99] transition duration-150 text-center flex items-center justify-center gap-1.5 disabled:opacity-50 disabled:cursor-not-allowed text-sm cursor-pointer"
                        :disabled="form.processing"
                    >
                        <span v-if="form.processing">Procesando...</span>
                        <span v-else>Enviar Código de Recuperación</span>
                    </button>
                </div>
            </form>

            <!-- Bottom Back to Login Link -->
            <div class="mt-8 text-center border-t border-[#12253b] pt-6">
                <Link :href="route('login')" class="text-xs text-[#00dfb2] hover:text-[#00ffd5] hover:underline font-extrabold flex items-center justify-center gap-1.5 cursor-pointer">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-3.5 h-3.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5" />
                    </svg>
                    Volver al Inicio de Sesión
                </Link>
            </div>
        </div>
    </div>
</template>
