<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ref, computed, watch } from 'vue';

const form = useForm({
    nombres: '',
    apellidos: '',
    correo: '',
    telefono: '',
    contrasena: '',
    contrasena_confirmation: '',
    terms: false,
});

const countryPrefix = ref('+503');
const phoneNumber = ref('');

const showPassword = ref(false);
const showConfirmPassword = ref(false);

const hasMinLength = computed(() => form.contrasena.length >= 8);
const hasUppercase = computed(() => /[A-Z]/.test(form.contrasena));
const hasLowercase = computed(() => /[a-z]/.test(form.contrasena));
const hasNumber = computed(() => /[0-9]/.test(form.contrasena));
const hasSpecialChar = computed(() => /[^A-Za-z0-9]/.test(form.contrasena));

const strengthScore = computed(() => {
    let score = 0;
    if (hasMinLength.value) score++;
    if (hasUppercase.value) score++;
    if (hasLowercase.value) score++;
    if (hasNumber.value) score++;
    if (hasSpecialChar.value) score++;
    return score;
});

const strengthText = computed(() => {
    if (form.contrasena.length === 0) return '';
    const score = strengthScore.value;
    if (score <= 1) return 'Muy débil';
    if (score === 2) return 'Débil';
    if (score === 3) return 'Media';
    if (score === 4) return 'Segura';
    return 'Muy segura';
});

const strengthColorClass = computed(() => {
    const score = strengthScore.value;
    if (score <= 1) return 'text-rose-500';
    if (score === 2) return 'text-amber-500';
    if (score === 3) return 'text-yellow-400';
    if (score === 4) return 'text-emerald-400';
    return 'text-[#00dfb2]';
});

const getBarsFilled = (score) => {
    if (form.contrasena.length === 0) return 0;
    if (score <= 1) return 1;
    if (score === 2) return 2;
    if (score === 3) return 3;
    return 4;
};

const getBarColorClass = (score) => {
    if (score <= 1) return 'bg-rose-500';
    if (score === 2) return 'bg-amber-500';
    if (score === 3) return 'bg-yellow-400';
    if (score === 4) return 'bg-emerald-500';
    return 'bg-[#00dfb2]';
};

const formatPhoneNumber = (event) => {
    let raw = event.target.value.replace(/\D/g, ''); // Keep digits only
    raw = raw.substring(0, 8); // Cap at 8 digits

    let formatted = '';
    if (raw.length > 4) {
        formatted = `${raw.substring(0, 4)}-${raw.substring(4)}`;
    } else {
        formatted = raw;
    }
    
    phoneNumber.value = formatted;
    form.errors.telefono = null;
};

const submit = () => {
    // Reset previous client errors
    form.errors.nombres = null;
    form.errors.apellidos = null;
    form.errors.correo = null;
    form.errors.telefono = null;
    form.errors.contrasena = null;
    form.errors.contrasena_confirmation = null;

    let hasErrors = false;

    if (!form.nombres.trim()) {
        form.errors.nombres = 'El nombre es obligatorio.';
        hasErrors = true;
    } else if (!/^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]+$/.test(form.nombres)) {
        form.errors.nombres = 'El nombre solo debe contener letras.';
        hasErrors = true;
    }

    if (!form.apellidos.trim()) {
        form.errors.apellidos = 'El apellido es obligatorio.';
        hasErrors = true;
    } else if (!/^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]+$/.test(form.apellidos)) {
        form.errors.apellidos = 'El apellido solo debe contener letras.';
        hasErrors = true;
    }

    if (!form.correo.trim()) {
        form.errors.correo = 'El correo electrónico es obligatorio.';
        hasErrors = true;
    }

    const rawDigits = phoneNumber.value.replace(/\D/g, '');
    if (!phoneNumber.value) {
        form.errors.telefono = 'El teléfono es obligatorio.';
        hasErrors = true;
    } else if (rawDigits.length < 8) {
        form.errors.telefono = 'El teléfono debe tener exactamente 8 dígitos.';
        hasErrors = true;
    }

    if (!form.contrasena) {
        form.errors.contrasena = 'La contraseña es obligatoria.';
        hasErrors = true;
    } else if (form.contrasena.length < 8) {
        form.errors.contrasena = 'La contraseña debe tener al menos 8 caracteres.';
        hasErrors = true;
    }

    if (form.contrasena !== form.contrasena_confirmation) {
        form.errors.contrasena_confirmation = 'La confirmación de la contraseña no coincide.';
        hasErrors = true;
    }

    if (!form.terms) {
        hasErrors = true;
    }

    if (hasErrors) {
        return;
    }

    // Combine prefix and phone number
    form.telefono = `+503 ${phoneNumber.value}`;
    
    form.post(route('register'), {
        onFinish: () => form.reset('contrasena', 'contrasena_confirmation'),
    });
};
</script>

<template>
    <Head title="Crear Cuenta - ProDoctor" />

    <div class="relative min-h-screen bg-[#040A18] flex flex-col items-center justify-center p-6 overflow-hidden select-none font-sans text-slate-300">
        
        <!-- Glowing background blobs -->
        <div class="absolute -top-40 -left-40 w-[500px] h-[500px] rounded-full bg-blue-600/10 blur-[150px] pointer-events-none"></div>
        <div class="absolute -bottom-40 -right-40 w-[500px] h-[500px] rounded-full bg-[#00dfb2]/5 blur-[150px] pointer-events-none"></div>

        <!-- Wizard progress header (outside the card) -->
        <div class="flex items-center justify-center gap-2 mb-6 text-xs font-semibold select-none z-10">
            <div class="flex flex-col items-center">
                <div class="w-8 h-8 rounded-full bg-[#00dfb2] text-slate-950 flex items-center justify-center font-bold shadow-[0_0_15px_rgba(0,223,178,0.4)]">1</div>
                <span class="mt-1.5 text-[#00dfb2] tracking-wider uppercase text-[10px] font-bold">Datos</span>
            </div>
            <div class="h-[1px] w-12 bg-slate-800 self-center -mt-4"></div>
            <div class="flex flex-col items-center">
                <div class="w-8 h-8 rounded-full border border-slate-700 text-slate-500 flex items-center justify-center font-bold bg-[#040A18]">2</div>
                <span class="mt-1.5 text-slate-500 tracking-wider uppercase text-[10px] font-bold">Verificar</span>
            </div>
            <div class="h-[1px] w-12 bg-slate-800 self-center -mt-4"></div>
            <div class="flex flex-col items-center">
                <div class="w-8 h-8 rounded-full border border-slate-700 text-slate-500 flex items-center justify-center font-bold bg-[#040A18]">3</div>
                <span class="mt-1.5 text-slate-500 tracking-wider uppercase text-[10px] font-bold">Listo</span>
            </div>
        </div>

        <!-- Card Container -->
        <div class="relative w-full max-w-lg bg-[#07111e] border border-[#13283f] rounded-2xl shadow-[0_20px_50px_rgba(0,0,0,0.6)] p-8 z-10">
            
            <!-- Logo & Title Header -->
            <div class="text-center mb-6">
                <div class="inline-flex w-14 h-14 rounded-2xl bg-gradient-to-tr from-emerald-500/20 to-teal-500/10 border border-[#00dfb2]/20 items-center justify-center shadow-lg shadow-emerald-500/5 mb-3">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-7 h-7 text-[#00dfb2]">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 12h3.5l1.5-3.5 2.5 7 2-4.5 1.5 1H20" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v2m0 12v2M8 12H6m12 0h-2" />
                    </svg>
                </div>
                <h1 class="text-2xl font-bold text-white tracking-tight">Crear cuenta</h1>
                <p class="text-[11px] text-slate-400 font-semibold tracking-wider mt-1">Registro seguro · ProDoctor</p>
            </div>

            <!-- Form -->
            <form @submit.prevent="submit" class="space-y-4">
                
                <!-- Nombres y Apellidos en una Sola Fila -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- Nombres -->
                    <div class="space-y-1.5">
                        <label for="nombres" class="text-[10px] font-bold text-[#00dfb2] uppercase tracking-widest block">Nombres</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-500">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" />
                                </svg>
                            </div>
                            <input
                                id="nombres"
                                type="text"
                                class="block w-full pl-11 pr-4 py-3 bg-[#040a12] border border-[#162d4a] rounded-lg text-white text-sm placeholder-slate-600 focus:outline-none focus:border-[#00dfb2] focus:ring-1 focus:ring-[#00dfb2]/30 transition"
                                v-model="form.nombres"
                                required
                                autofocus
                                placeholder="Ej. Luis Alexander"
                            />
                        </div>
                        <span v-if="form.errors.nombres" class="text-xs text-rose-500 font-medium block mt-1">{{ form.errors.nombres }}</span>
                    </div>

                    <!-- Apellidos -->
                    <div class="space-y-1.5">
                        <label for="apellidos" class="text-[10px] font-bold text-[#00dfb2] uppercase tracking-widest block">Apellidos</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-500">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" />
                                </svg>
                            </div>
                            <input
                                id="apellidos"
                                type="text"
                                class="block w-full pl-11 pr-4 py-3 bg-[#040a12] border border-[#162d4a] rounded-lg text-white text-sm placeholder-slate-600 focus:outline-none focus:border-[#00dfb2] focus:ring-1 focus:ring-[#00dfb2]/30 transition"
                                v-model="form.apellidos"
                                required
                                placeholder="Ej. Rivera Alvarez"
                            />
                        </div>
                        <span v-if="form.errors.apellidos" class="text-xs text-rose-500 font-medium block mt-1">{{ form.errors.apellidos }}</span>
                    </div>
                </div>

                <!-- Correo Electrónico -->
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
                            class="block w-full pl-11 pr-4 py-3 bg-[#040a12] border border-[#162d4a] rounded-lg text-white text-sm placeholder-slate-600 focus:outline-none focus:border-[#00dfb2] focus:ring-1 focus:ring-[#00dfb2]/30 transition"
                            v-model="form.correo"
                            required
                            autocomplete="username"
                            placeholder="correo@ejemplo.com"
                        />
                    </div>
                    <span v-if="form.errors.correo" class="text-xs text-rose-500 font-medium block mt-1">{{ form.errors.correo }}</span>
                </div>

                <!-- Teléfono (El Salvador) -->
                <div class="space-y-1.5">
                    <label for="telefono" class="text-[10px] font-bold text-[#00dfb2] uppercase tracking-widest block">Teléfono (El Salvador)</label>
                    <div class="flex gap-2">
                        <!-- Prefix Static Label -->
                        <div class="flex items-center justify-center px-4 bg-[#040a12] border border-[#162d4a] rounded-lg text-white text-xs font-bold select-none">
                            🇸🇻 +503
                        </div>
                        
                        <!-- Phone Input -->
                        <div class="relative flex-1">
                            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-500">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 6.75c0 8.284 6.716 15 15 15h2.25a2.25 2.25 0 0 0 2.25-2.25v-1.372c0-.516-.351-.966-.852-1.091l-4.423-1.106c-.44-.11-.902.055-1.173.417l-.97 1.293c-2.824-1.557-5.127-3.86-6.684-6.684l1.293-.97c.363-.271.527-.734.417-1.173L6.963 3.102a1.125 1.125 0 0 0-1.091-.852H4.5A2.25 2.25 0 0 0 2.25 4.5v2.25Z" />
                                </svg>
                            </div>
                            <input
                                id="telefono"
                                type="text"
                                class="block w-full pl-11 pr-4 py-3 bg-[#040a12] border border-[#162d4a] rounded-lg text-white text-sm placeholder-slate-605 focus:outline-none focus:border-[#00dfb2] focus:ring-1 focus:ring-[#00dfb2]/30 transition"
                                v-model="phoneNumber"
                                @input="formatPhoneNumber"
                                required
                                placeholder="7654-3210"
                            />
                        </div>
                    </div>
                    <span v-if="form.errors.telefono" class="text-xs text-rose-500 font-medium block mt-1">{{ form.errors.telefono }}</span>
                </div>

                <!-- Contraseñas en una sola fila (flex o grid) -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- Contraseña -->
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
                                :type="showPassword ? 'text' : 'password'"
                                class="block w-full pl-11 pr-10 py-3 bg-[#040a12] border border-[#162d4a] rounded-lg text-white text-sm placeholder-slate-600 focus:outline-none focus:border-[#00dfb2] focus:ring-1 focus:ring-[#00dfb2]/30 transition"
                                v-model="form.contrasena"
                                required
                                autocomplete="new-password"
                                placeholder="Contraseña"
                            />
                            <button
                                type="button"
                                @click="showPassword = !showPassword"
                                class="absolute inset-y-0 right-0 pr-3 flex items-center text-slate-500 hover:text-slate-300"
                            >
                                <svg v-if="showPassword" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 0 0 1.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.451 10.451 0 0 1 12 4.5c4.756 0 8.773 3.162 10.065 7.498a10.522 10.522 0 0 1-4.293 5.774M6.228 6.228 3 3m3.228 3.228 3.65 3.65m7.894 7.894L21 21m-3.228-3.228-3.65-3.65m0 0a3 3 0 1 0-4.243-4.243m4.242 4.242L9.88 9.88" />
                                </svg>
                                <svg v-else xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                                </svg>
                            </button>
                        </div>
                    </div>

                    <!-- Confirmar Contraseña -->
                    <div class="space-y-1.5">
                        <label for="contrasena_confirmation" class="text-[10px] font-bold text-[#00dfb2] uppercase tracking-widest block">Confirmar Contraseña</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-500">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z" />
                                </svg>
                            </div>
                            <input
                                id="contrasena_confirmation"
                                :type="showConfirmPassword ? 'text' : 'password'"
                                class="block w-full pl-11 pr-10 py-3 bg-[#040a12] border border-[#162d4a] rounded-lg text-white text-sm placeholder-slate-600 focus:outline-none focus:border-[#00dfb2] focus:ring-1 focus:ring-[#00dfb2]/30 transition"
                                v-model="form.contrasena_confirmation"
                                required
                                autocomplete="new-password"
                                placeholder="Confirmar"
                            />
                            <button
                                type="button"
                                @click="showConfirmPassword = !showConfirmPassword"
                                class="absolute inset-y-0 right-0 pr-3 flex items-center text-slate-500 hover:text-slate-300"
                            >
                                <svg v-if="showConfirmPassword" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 0 0 1.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.451 10.451 0 0 1 12 4.5c4.756 0 8.773 3.162 10.065 7.498a10.522 10.522 0 0 1-4.293 5.774M6.228 6.228 3 3m3.228 3.228 3.65 3.65m7.894 7.894L21 21m-3.228-3.228-3.65-3.65m0 0a3 3 0 1 0-4.243-4.243m4.242 4.242L9.88 9.88" />
                                </svg>
                                <svg v-else xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>

                <div v-if="form.errors.contrasena" class="text-xs text-rose-500 font-medium block mt-1">{{ form.errors.contrasena }}</div>
                <div v-if="form.errors.contrasena_confirmation" class="text-xs text-rose-500 font-medium block mt-1">{{ form.errors.contrasena_confirmation }}</div>

                <!-- Password Strength Visual Meter -->
                <div v-if="form.contrasena.length > 0" class="space-y-2 pt-1">
                    <div class="flex items-center justify-between text-[11px] font-bold">
                        <span class="text-slate-400">Seguridad de la contraseña</span>
                        <span :class="strengthColorClass">{{ strengthText }}</span>
                    </div>
                    
                    <!-- 4 segments progress bar -->
                    <div class="flex gap-1.5">
                        <div
                            v-for="i in 4"
                            :key="i"
                            class="h-1.5 flex-1 rounded-full transition-all duration-300"
                            :class="[
                                i <= getBarsFilled(strengthScore)
                                    ? getBarColorClass(strengthScore)
                                    : 'bg-[#101b2b]'
                            ]"
                        ></div>
                    </div>

                    <!-- Constraints checklist -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-x-4 gap-y-1.5 pt-1.5 text-xs text-slate-400 select-none">
                        <!-- Min 8 chars -->
                        <div class="flex items-center gap-2 transition duration-200" :class="{'text-[#00dfb2]': hasMinLength}">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-4 h-4 flex-shrink-0" :class="hasMinLength ? 'text-[#00dfb2]' : 'text-slate-600'">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 1 0 0-16 8 8 0 0 0 0 16Zm3.857-9.809a.75.75 0 0 0-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 1 0-1.06 1.061l2.5 2.5a.75.75 0 0 0 1.137-.089l4-5.5Z" clip-rule="evenodd" />
                            </svg>
                            <span>Mínimo 8 caracteres</span>
                        </div>
                        
                        <!-- Uppercase -->
                        <div class="flex items-center gap-2 transition duration-200" :class="{'text-[#00dfb2]': hasUppercase}">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-4 h-4 flex-shrink-0" :class="hasUppercase ? 'text-[#00dfb2]' : 'text-slate-600'">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 1 0 0-16 8 8 0 0 0 0 16Zm3.857-9.809a.75.75 0 0 0-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 1 0-1.06 1.061l2.5 2.5a.75.75 0 0 0 1.137-.089l4-5.5Z" clip-rule="evenodd" />
                            </svg>
                            <span>Al menos una mayúscula</span>
                        </div>

                        <!-- Lowercase -->
                        <div class="flex items-center gap-2 transition duration-200" :class="{'text-[#00dfb2]': hasLowercase}">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-4 h-4 flex-shrink-0" :class="hasLowercase ? 'text-[#00dfb2]' : 'text-slate-600'">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 1 0 0-16 8 8 0 0 0 0 16Zm3.857-9.809a.75.75 0 0 0-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 1 0-1.06 1.061l2.5 2.5a.75.75 0 0 0 1.137-.089l4-5.5Z" clip-rule="evenodd" />
                            </svg>
                            <span>Al menos una minúscula</span>
                        </div>

                        <!-- Number -->
                        <div class="flex items-center gap-2 transition duration-200" :class="{'text-[#00dfb2]': hasNumber}">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-4 h-4 flex-shrink-0" :class="hasNumber ? 'text-[#00dfb2]' : 'text-slate-600'">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 1 0 0-16 8 8 0 0 0 0 16Zm3.857-9.809a.75.75 0 0 0-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 1 0-1.06 1.061l2.5 2.5a.75.75 0 0 0 1.137-.089l4-5.5Z" clip-rule="evenodd" />
                            </svg>
                            <span>Al menos un número</span>
                        </div>

                        <!-- Special Char -->
                        <div class="flex items-center gap-2 transition duration-200 md:col-span-2" :class="{'text-[#00dfb2]': hasSpecialChar}">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-4 h-4 flex-shrink-0" :class="hasSpecialChar ? 'text-[#00dfb2]' : 'text-slate-600'">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 1 0 0-16 8 8 0 0 0 0 16Zm3.857-9.809a.75.75 0 0 0-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 1 0-1.06 1.061l2.5 2.5a.75.75 0 0 0 1.137-.089l4-5.5Z" clip-rule="evenodd" />
                            </svg>
                            <span>Al menos un carácter especial (!@#$...)</span>
                        </div>
                    </div>
                </div>

                <!-- Terms and Conditions Checkbox -->
                <div class="pt-2 select-none">
                    <label class="flex items-start gap-2.5 cursor-pointer text-xs">
                        <input type="checkbox" v-model="form.terms" required class="sr-only peer" />
                        <div class="w-5 h-5 rounded border border-[#162d4a] bg-[#040a12] flex items-center justify-center text-transparent peer-checked:text-[#00dfb2] peer-checked:border-[#00dfb2] transition-all duration-200 flex-shrink-0">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-4 h-4">
                                <path fill-rule="evenodd" d="M16.704 4.153a.75.75 0 0 1 .143 1.052l-8 10.5a.75.75 0 0 1-1.127.075l-4.5-4.5a.75.75 0 0 1 1.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 0 1 1.05-.143Z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <span class="text-slate-400 leading-tight">
                            Acepto los <span class="text-[#00dfb2] hover:underline font-semibold cursor-pointer">Términos de uso</span> y la <span class="text-[#00dfb2] hover:underline font-semibold cursor-pointer">Política de privacidad</span> de ProDoctor
                        </span>
                    </label>
                    <span v-if="form.errors.terms" class="text-xs text-rose-500 font-medium block mt-1">{{ form.errors.terms }}</span>
                </div>

                <!-- Submit Button -->
                <div class="pt-3">
                    <button
                        type="submit"
                        class="w-full py-3.5 bg-gradient-to-r from-[#00dfb2] to-[#00a887] hover:from-[#00ffd5] hover:to-[#00c79f] text-slate-950 font-extrabold rounded-lg shadow-lg shadow-[#00dfb2]/10 active:scale-[0.99] transition duration-150 text-center flex items-center justify-center gap-1.5 disabled:opacity-50 disabled:cursor-not-allowed text-sm"
                        :disabled="form.processing || !form.terms"
                    >
                        <span>{{ form.processing ? 'Procesando...' : 'Crear cuenta' }}</span>
                        <span v-if="!form.processing">→</span>
                    </button>
                </div>
            </form>

            <!-- Bottom Login Link -->
            <div class="mt-6 text-center border-t border-[#12253b] pt-5">
                <p class="text-xs text-slate-500">
                    ¿Ya tienes una cuenta de paciente?
                    <Link :href="route('login')" class="text-[#00dfb2] hover:text-[#00ffd5] hover:underline font-extrabold ml-1">Inicia Sesión</Link>
                </p>
            </div>
        </div>
    </div>
</template>
