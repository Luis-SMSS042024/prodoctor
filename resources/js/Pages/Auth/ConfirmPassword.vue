<script setup>
import { Head, useForm, Link } from '@inertiajs/vue3';
import { ref } from 'vue';

const form = useForm({
    password: '',
});

const showPassword = ref(false);

const submit = () => {
    form.post(route('password.confirm'), {
        onFinish: () => form.reset(),
    });
};
</script>

<template>
    <Head title="Confirmar Contraseña - ProDoctor" />

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
                <h1 class="text-2xl font-bold text-white tracking-tight">Área Segura</h1>
                <p class="text-[10px] text-slate-400 font-bold uppercase tracking-widest mt-1">CONFIRMA TU CONTRASEÑA PARA CONTINUAR</p>
            </div>

            <!-- Description -->
            <div class="mb-6 text-xs text-slate-400 text-center leading-relaxed">
                Esta es un área segura de la aplicación. Por favor, confirma tu contraseña antes de continuar.
            </div>

            <form @submit.prevent="submit" class="space-y-5">
                <!-- Input Contraseña con Icono y Toggle de Visibilidad -->
                <div class="space-y-1.5">
                    <label for="password" class="text-[10px] font-bold text-[#00dfb2] uppercase tracking-widest block">Contraseña</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-500">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z" />
                            </svg>
                        </div>
                        <input
                            id="password"
                            :type="showPassword ? 'text' : 'password'"
                            class="block w-full pl-11 pr-10 py-3 bg-[#040a12] border border-[#162d4a] rounded-lg text-white text-sm placeholder-slate-650 focus:outline-none focus:border-[#00dfb2] focus:ring-1 focus:ring-[#00dfb2]/30 transition"
                            v-model="form.password"
                            required
                            autocomplete="current-password"
                            autofocus
                            placeholder="••••••••"
                        />
                        <button
                            type="button"
                            @click="showPassword = !showPassword"
                            class="absolute inset-y-0 right-0 pr-3 flex items-center text-slate-500 hover:text-[#00dfb2] transition cursor-pointer"
                        >
                            <svg v-if="!showPassword" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" />
                                <circle cx="12" cy="12" r="3" />
                            </svg>
                            <svg v-else xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 001.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.45 10.45 0 0112 4.5c4.756 0 8.773 3.162 10.065 7.498a10.523 10.523 0 01-4.293 5.774M6.228 6.228L3 3m3.228 3.228l3.65 3.65m7.895 7.895L21 21m-3.228-3.228l-3.65-3.65m0 0a3 3 0 10-4.243-4.243m4.242 4.242L9.88 9.88" />
                            </svg>
                        </button>
                    </div>
                    <span v-if="form.errors.password" class="text-xs text-rose-500 font-medium block mt-1">{{ form.errors.password }}</span>
                </div>

                <!-- Submit Button -->
                <div class="pt-2">
                    <button
                        type="submit"
                        class="w-full py-3.5 bg-gradient-to-r from-[#00dfb2] to-[#00a887] hover:from-[#00ffd5] hover:to-[#00c79f] text-slate-950 font-extrabold rounded-lg shadow-lg shadow-[#00dfb2]/10 active:scale-[0.99] transition duration-150 text-center flex items-center justify-center gap-1.5 disabled:opacity-50 disabled:cursor-not-allowed text-sm cursor-pointer"
                        :disabled="form.processing"
                    >
                        <span v-if="form.processing">Confirmando...</span>
                        <span v-else>Confirmar Contraseña</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</template>
