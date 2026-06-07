<script setup>
import { Head, useForm } from '@inertiajs/vue3';

defineProps({
    status: {
        type: String,
    },
});

const form = useForm({
    code: '',
});

const submit = () => {
    form.post(route('otp.check'));
};
</script>

<template>
    <Head title="Verificación de Código - ProDoctor" />

    <div class="relative min-h-screen bg-gradient-to-br from-[#040A18] via-[#091526] to-[#11233B] flex items-center justify-center p-6 overflow-hidden select-none font-sans">
        
        <!-- Glowing background blobs -->
        <div class="absolute -top-40 -left-40 w-[500px] h-[500px] rounded-full bg-blue-600/10 blur-[150px] pointer-events-none"></div>
        <div class="absolute -bottom-40 -right-40 w-[500px] h-[500px] rounded-full bg-emerald-600/5 blur-[150px] pointer-events-none"></div>

        <div class="relative w-full max-w-md bg-white rounded-2xl shadow-[0_20px_50px_rgba(0,0,0,0.5)] border border-slate-100 p-8">
            
            <!-- Logo & Brand Header -->
            <div class="text-center mb-8">
                <div class="inline-flex w-14 h-14 rounded-2xl bg-gradient-to-tr from-blue-600 to-indigo-650 items-center justify-center shadow-lg shadow-blue-500/30 mb-4">
                    <!-- Lock Icon -->
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-7 h-7 text-white">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 1 0-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 0 0 2.25-2.25v-6.75a2.25 2.25 0 0 0-2.25-2.25H6.75a2.25 2.25 0 0 0-2.25 2.25v6.75a2.25 2.25 0 0 0 2.25 2.25Z" />
                    </svg>
                </div>
                <h1 class="text-2xl font-extrabold text-slate-800 tracking-tight">Verificación 2FA</h1>
                <p class="text-[10px] text-slate-400 font-bold uppercase tracking-widest mt-1.5">PACIENTE - CÓDIGO DE ACCESO</p>
                <p class="text-xs text-slate-500 mt-3 px-4">Ingresa el código de 6 dígitos que enviamos a tu correo electrónico registrado.</p>
            </div>

            <!-- Validation Status (contains OTP code for dev convenience) -->
            <div v-if="status" class="mb-6 p-4 rounded-xl bg-amber-50 border border-amber-200 text-xs font-semibold text-amber-800 text-center leading-relaxed">
                {{ status }}
            </div>

            <form @submit.prevent="submit" class="space-y-6">
                <!-- Code Field -->
                <div class="space-y-2">
                    <label class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block">CÓDIGO DE VERIFICACIÓN</label>
                    <input
                        id="code"
                        type="text"
                        placeholder="Ej. 123456"
                        v-model="form.code"
                        required
                        autofocus
                        maxlength="6"
                        class="block w-full px-4 py-3 bg-slate-50 border-2 border-slate-200 rounded-xl text-slate-800 text-center font-bold text-lg tracking-widest focus:outline-none focus:bg-white focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 transition"
                    />
                    <div v-if="form.errors.code" class="text-xs text-rose-600 font-semibold mt-1 text-center">{{ form.errors.code }}</div>
                    <div v-if="form.errors.correo" class="text-xs text-rose-600 font-semibold mt-1 text-center">{{ form.errors.correo }}</div>
                </div>

                <!-- Action Button -->
                <button
                    type="submit"
                    :disabled="form.processing"
                    class="w-full py-3.5 bg-gradient-to-r from-blue-600 to-indigo-650 hover:from-blue-700 hover:to-indigo-755 text-white text-sm font-bold rounded-xl shadow-lg shadow-blue-500/10 active:scale-[0.98] transition cursor-pointer disabled:opacity-50"
                >
                    <span v-if="form.processing">Verificando...</span>
                    <span v-else>Acceder al Portal</span>
                </button>

                <!-- Back to login link -->
                <div class="text-center pt-2">
                    <Link
                        :href="route('login')"
                        class="text-xs font-bold text-slate-400 hover:text-slate-600 transition underline"
                    >
                        Volver al inicio de sesión
                    </Link>
                </div>
            </form>
        </div>
    </div>
</template>
