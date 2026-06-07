<script setup>
import InputError from '@/Components/InputError.vue';
import { useForm } from '@inertiajs/vue3';
import { ref } from 'vue';
import axios from 'axios';

const passwordInput = ref(null);
const currentPasswordInput = ref(null);

const form = useForm({
    contrasena_actual: '',
    contrasena: '',
    contrasena_confirmation: '',
    codigo_otp: '',
});

const isOtpSent = ref(false);
const otpMessage = ref('');
const devOtp = ref('');
const isSendingOtp = ref(false);

const sendOtp = () => {
    isSendingOtp.value = true;
    axios.post(route('profile.send-otp'), { type: 'password' })
        .then(response => {
            isOtpSent.value = true;
            otpMessage.value = response.data.message;
            if (response.data.otp) {
                devOtp.value = response.data.otp;
            }
        })
        .catch(error => {
            alert(error.response?.data?.message || 'Error al enviar el código.');
        })
        .finally(() => {
            isSendingOtp.value = false;
        });
};

const updatePassword = () => {
    form.put(route('password.update'), {
        preserveScroll: true,
        onSuccess: () => {
            form.reset();
            isOtpSent.value = false;
            devOtp.value = '';
            otpMessage.value = '';
        },
        onError: () => {
            if (form.errors.contrasena) {
                form.reset('contrasena', 'contrasena_confirmation');
                passwordInput.value?.focus();
            }
            if (form.errors.contrasena_actual) {
                form.reset('contrasena_actual');
                currentPasswordInput.value?.focus();
            }
        },
    });
};
</script>

<template>
    <section class="space-y-6">
        <header class="border-b border-[#13283f] pb-3">
            <h2 class="text-sm font-black text-[#00dfb2] uppercase tracking-wider">
                Actualizar Contraseña
            </h2>
            <p class="text-xs text-slate-400 mt-1">
                Asegúrate de que tu cuenta esté utilizando una contraseña larga y aleatoria para mantenerla segura.
            </p>
        </header>

        <form @submit.prevent="updatePassword" class="space-y-5 mt-4">
            <!-- Contraseña Actual -->
            <div class="space-y-1.5">
                <label for="contrasena_actual" class="text-[10px] font-bold text-[#00dfb2] uppercase tracking-widest block">Contraseña Actual</label>
                <input
                    id="contrasena_actual"
                    ref="currentPasswordInput"
                    v-model="form.contrasena_actual"
                    type="password"
                    class="block w-full px-4 py-3 bg-[#040a12] border border-[#162d4a] rounded-xl text-white text-xs focus:outline-none focus:border-[#00dfb2] transition"
                    autocomplete="current-password"
                    placeholder="Ingresa tu contraseña actual"
                    required
                />
                <InputError :message="form.errors.contrasena_actual" class="mt-2" />
            </div>

            <!-- Nueva Contraseña -->
            <div class="space-y-1.5">
                <label for="contrasena" class="text-[10px] font-bold text-[#00dfb2] uppercase tracking-widest block">Nueva Contraseña</label>
                <input
                    id="contrasena"
                    ref="passwordInput"
                    v-model="form.contrasena"
                    type="password"
                    class="block w-full px-4 py-3 bg-[#040a12] border border-[#162d4a] rounded-xl text-white text-xs focus:outline-none focus:border-[#00dfb2] transition"
                    autocomplete="new-password"
                    placeholder="Ingresa la nueva contraseña"
                    required
                />
                <InputError :message="form.errors.contrasena" class="mt-2" />
            </div>

            <!-- Confirmar Nueva Contraseña -->
            <div class="space-y-1.5">
                <label for="contrasena_confirmation" class="text-[10px] font-bold text-[#00dfb2] uppercase tracking-widest block">Confirmar Nueva Contraseña</label>
                <input
                    id="contrasena_confirmation"
                    v-model="form.contrasena_confirmation"
                    type="password"
                    class="block w-full px-4 py-3 bg-[#040a12] border border-[#162d4a] rounded-xl text-white text-xs focus:outline-none focus:border-[#00dfb2] transition"
                    autocomplete="new-password"
                    placeholder="Confirma la nueva contraseña"
                    required
                />
                <InputError :message="form.errors.contrasena_confirmation" class="mt-2" />
            </div>

            <!-- OTP Verification Field (All roles) -->
            <div class="space-y-4 pt-4 border-t border-[#13283f]">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-[#040a12] p-4 border border-[#162d4a] rounded-xl">
                    <div>
                        <span class="text-xs font-bold text-white block">Código de Verificación (Requerido)</span>
                        <span class="text-[10px] text-slate-550 font-medium block mt-0.5">Presiona el botón para recibir un código de 6 dígitos en tu correo.</span>
                    </div>
                    <button
                        type="button"
                        @click="sendOtp"
                        :disabled="isSendingOtp"
                        class="px-4 py-2 border border-[#162d4a] hover:border-slate-505 bg-[#07111e] hover:bg-[#0c1a2e] text-xs font-bold rounded-xl transition cursor-pointer text-slate-300 disabled:opacity-50 shadow-sm"
                    >
                        <span v-if="isSendingOtp">Enviando...</span>
                        <span v-else-if="isOtpSent">Reenviar Código</span>
                        <span v-else>Solicitar Código</span>
                    </button>
                </div>

                <div class="space-y-1.5">
                    <label for="codigo_otp" class="text-[10px] font-bold text-[#00dfb2] uppercase tracking-widest block">Ingresa el Código Recibido</label>
                    <input
                        id="codigo_otp"
                        v-model="form.codigo_otp"
                        type="text"
                        placeholder="Ingresa el código de 6 dígitos"
                        class="block w-full px-4 py-3 bg-[#040a12] border border-[#162d4a] rounded-xl text-white text-xs font-bold text-center tracking-widest focus:outline-none focus:border-[#00dfb2] transition"
                        maxlength="6"
                        required
                    />
                    <InputError :message="form.errors.codigo_otp" class="mt-2 text-center" />
                    
                    <div v-if="devOtp" class="p-2.5 rounded-lg bg-amber-500/10 border border-amber-500/25 text-[11px] font-semibold text-amber-400 text-center">
                        Código enviado. (Desarrollo: <span class="font-extrabold text-[#00dfb2]">{{ devOtp }}</span>)
                    </div>
                </div>
            </div>

            <!-- Submit Button -->
            <div class="flex items-center gap-4 pt-2">
                <button
                    type="submit"
                    :disabled="form.processing"
                    class="px-5 py-2.5 bg-gradient-to-r from-[#00dfb2] to-[#00a887] hover:from-[#00ffd5] hover:to-[#00c79f] text-slate-950 text-xs font-black rounded-xl shadow-md transition cursor-pointer disabled:opacity-50"
                >
                    Actualizar Contraseña
                </button>

                <Transition
                    enter-active-class="transition ease-in-out"
                    enter-from-class="opacity-0"
                    leave-active-class="transition ease-in-out"
                    leave-to-class="opacity-0"
                >
                    <p
                        v-if="form.recentlySuccessful"
                        class="text-xs text-emerald-400 font-bold"
                    >
                        ✓ Contraseña actualizada correctamente.
                    </p>
                </Transition>
            </div>
        </form>
    </section>
</template>
