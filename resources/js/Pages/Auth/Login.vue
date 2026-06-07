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
    login_2fa: false,
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

    <div class="relative min-h-screen bg-[#040A18] flex flex-col items-center justify-center p-6 overflow-hidden select-none font-sans text-slate-300">
        
        <!-- Glowing background blobs -->
        <div class="absolute -top-40 -left-40 w-[500px] h-[500px] rounded-full bg-blue-600/10 blur-[150px] pointer-events-none"></div>
        <div class="absolute -bottom-40 -right-40 w-[500px] h-[500px] rounded-full bg-[#00dfb2]/5 blur-[150px] pointer-events-none"></div>

        <!-- Card Container -->
        <div class="relative w-full max-w-md bg-[#07111e] border border-[#13283f] rounded-2xl shadow-[0_20px_50px_rgba(0,0,0,0.6)] p-8 z-10">
            
            <!-- Logo & Brand Header -->
            <div class="text-center mb-6 flex flex-col items-center">
                <div class="inline-flex w-14 h-14 rounded-2xl bg-gradient-to-tr from-emerald-500/20 to-teal-500/10 border border-[#00dfb2]/20 items-center justify-center shadow-lg shadow-emerald-500/5 mb-3">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-7 h-7 text-[#00dfb2]">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 12h3.5l1.5-3.5 2.5 7 2-4.5 1.5 1H20" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v2m0 12v2M8 12H6m12 0h-2" />
                    </svg>
                </div>
                <h1 class="text-2xl font-bold text-white tracking-tight">ProDoctor</h1>
                <p class="text-[10px] text-slate-400 font-bold uppercase tracking-widest mt-1">SISTEMA DE GESTIÓN MÉDICA</p>
            </div>

            <!-- Validation Status -->
            <div v-if="status" class="mb-4 p-3.5 bg-emerald-500/10 border border-emerald-500/20 rounded-xl text-xs font-semibold text-emerald-400 text-center leading-relaxed">
                {{ status }}
            </div>

            <form @submit.prevent="submit" class="space-y-5">
                <!-- Selector de Tipo de Usuario (Botonera Estilo Mockup) -->
                <div class="space-y-2">
                    <label class="text-[10px] font-bold text-[#00dfb2] uppercase tracking-widest block">Tipo de Usuario</label>
                    <div class="grid grid-cols-2 gap-4">
                        <!-- Botón Doctor -->
                        <button
                            type="button"
                            @click="form.rol = 'doctor'"
                            class="flex flex-col items-center gap-2.5 py-4 px-4 rounded-xl border text-xs font-bold transition-all duration-200 relative overflow-hidden focus:outline-none cursor-pointer"
                            :class="form.rol === 'doctor'
                                ? 'bg-[#040a12] border-[#00dfb2] text-[#00dfb2] shadow-md shadow-[#00dfb2]/5 scale-[1.02]'
                                : 'bg-[#040a12] border-[#162d4a] text-slate-500 hover:text-slate-450 hover:border-slate-700'"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-6 h-6">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75a3.75 3.75 0 117.5 0 3.75 3.75 0 01-7.5 0zM5.5 20.118a7.5 7.5 0 0113 0A17.933 17.933 0 0112 21.75c-2.327 0-4.545-.445-6.5-1.632z" />
                            </svg>
                            Doctor
                            
                            <span v-if="form.rol === 'doctor'" class="absolute top-2 right-2 w-1.5 h-1.5 rounded-full bg-[#00dfb2]"></span>
                        </button>

                        <!-- Botón Paciente -->
                        <button
                            type="button"
                            @click="form.rol = 'paciente'"
                            class="flex flex-col items-center gap-2.5 py-4 px-4 rounded-xl border text-xs font-bold transition-all duration-200 relative overflow-hidden focus:outline-none cursor-pointer"
                            :class="form.rol === 'paciente'
                                ? 'bg-[#040a12] border-[#00dfb2] text-[#00dfb2] shadow-md shadow-[#00dfb2]/5 scale-[1.02]'
                                : 'bg-[#040a12] border-[#162d4a] text-slate-500 hover:text-slate-450 hover:border-slate-700'"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-6 h-6">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" />
                            </svg>
                            Paciente
                            
                            <span v-if="form.rol === 'paciente'" class="absolute top-2 right-2 w-1.5 h-1.5 rounded-full bg-[#00dfb2]"></span>
                        </button>
                    </div>
                </div>

                <!-- Input Correo Electrónico con Icono -->
                <div class="space-y-1.5">
                    <label for="correo" class="text-[10px] font-bold text-[#00dfb2] uppercase tracking-widest block">Correo Electrónico</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-500">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0l-7.5-4.615a2.25 2.25 0 01-1.07-1.916V6.75" />
                            </svg>
                        </div>
                        <input
                            id="correo"
                            type="email"
                            class="block w-full pl-11 pr-4 py-3 bg-[#040a12] border border-[#162d4a] rounded-lg text-white text-sm placeholder-slate-650 focus:outline-none focus:border-[#00dfb2] focus:ring-1 focus:ring-[#00dfb2]/30 transition"
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
                    <label for="contrasena" class="text-[10px] font-bold text-[#00dfb2] uppercase tracking-widest block">Contraseña</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-500">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z" />
                            </svg>
                        </div>
                        <input
                            id="contrasena"
                            type="password"
                            class="block w-full pl-11 pr-4 py-3 bg-[#040a12] border border-[#162d4a] rounded-lg text-white text-sm placeholder-slate-650 focus:outline-none focus:border-[#00dfb2] focus:ring-1 focus:ring-[#00dfb2]/30 transition"
                            v-model="form.contrasena"
                            required
                            autocomplete="current-password"
                            placeholder="••••••••"
                        />
                    </div>
                    <span v-if="form.errors.contrasena" class="text-xs text-rose-500 font-medium block mt-1">{{ form.errors.contrasena }}</span>
                </div>

                <!-- Recordarme & Forgot Links -->
                <div class="flex flex-col gap-3.5 text-xs select-none">
                    <div class="flex items-center justify-between">
                        <label class="flex items-center text-slate-450 cursor-pointer">
                            <input
                                type="checkbox"
                                name="remember"
                                v-model="form.remember"
                                class="sr-only peer"
                            />
                            <div class="w-4 h-4 mr-2 rounded border border-[#162d4a] bg-[#040a12] flex items-center justify-center text-transparent peer-checked:text-[#00dfb2] peer-checked:border-[#00dfb2] transition-all duration-200">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-3.5 h-3.5">
                                    <path fill-rule="evenodd" d="M16.704 4.153a.75.75 0 0 1 .143 1.052l-8 10.5a.75.75 0 0 1-1.127.075l-4.5-4.5a.75.75 0 0 1 1.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 0 1 1.05-.143Z" clip-rule="evenodd" />
                                </svg>
                            </div>
                            Recordarme
                        </label>
                        <Link
                            v-if="canResetPassword"
                            :href="route('password.request')"
                            class="text-[#00dfb2] hover:text-[#00ffd5] hover:underline font-bold"
                        >
                            ¿Olvidaste tu contraseña?
                        </Link>
                    </div>


                </div>

                <!-- Submit Button -->
                <div class="pt-2">
                    <button
                        type="submit"
                        class="w-full py-3.5 bg-gradient-to-r from-[#00dfb2] to-[#00a887] hover:from-[#00ffd5] hover:to-[#00c79f] text-slate-950 font-extrabold rounded-lg shadow-lg shadow-[#00dfb2]/10 active:scale-[0.99] transition duration-150 text-center flex items-center justify-center gap-1.5 disabled:opacity-50 disabled:cursor-not-allowed text-sm cursor-pointer"
                        :disabled="form.processing"
                    >
                        <span v-if="form.processing">Procesando...</span>
                        <span v-else>Iniciar Sesión</span>
                    </button>
                </div>
            </form>

            <!-- Bottom Registration Link -->
            <div class="mt-8 text-center border-t border-[#12253b] pt-6">
                <p class="text-xs text-slate-500">
                    ¿No tienes una cuenta de paciente?
                    <Link :href="route('register')" class="text-[#00dfb2] hover:text-[#00ffd5] hover:underline font-extrabold ml-1">Regístrate gratis</Link>
                </p>
            </div>
        </div>
    </div>
</template>
