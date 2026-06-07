<script setup>
import InputError from '@/Components/InputError.vue';
import { useForm, usePage } from '@inertiajs/vue3';
import { ref, watch } from 'vue';
import axios from 'axios';

const props = defineProps({
    mustVerifyEmail: {
        type: Boolean,
    },
    status: {
        type: String,
    },
});

const user = usePage().props.auth.user;

const form = useForm({
    nombre_usuario: user.nombre_usuario,
    correo: user.correo,
    foto: null,
    contrasena_actual: '',
    codigo_otp: '',
    login_2fa: !!user.login_2fa,
    _method: 'PATCH', // Spoofing PATCH method for multipart file uploads
});

const imagePreview = ref(user.foto ? '/' + user.foto : null);
const fileInput = ref(null);

const showEmailOtpFields = ref(false);
const isOtpSent = ref(false);
const isSendingOtp = ref(false);
const otpMessage = ref('');
const devOtp = ref('');

// Watch for email changes to reveal verification inputs
watch(() => form.correo, (newVal) => {
    if (newVal !== user.correo) {
        showEmailOtpFields.value = true;
    }
});

const selectNewPhoto = () => {
    fileInput.value.click();
};

const updatePhotoPreview = () => {
    const photo = fileInput.value.files[0];
    if (!photo) return;
    form.foto = photo;
    const reader = new FileReader();
    reader.onload = (e) => {
        imagePreview.value = e.target.result;
    };
    reader.readAsDataURL(photo);
};

const sendEmailOtp = () => {
    if (!form.correo || form.correo === user.correo) {
        alert('Por favor ingresa un correo electrónico diferente para verificar.');
        return;
    }
    isSendingOtp.value = true;
    axios.post(route('profile.send-otp'), {
        type: 'email',
        email: form.correo
    })
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

const submitForm = () => {
    form.post(route('profile.update'), {
        forceFormData: true,
        preserveScroll: true,
        onSuccess: () => {
            form.foto = null;
            form.contrasena_actual = '';
            form.codigo_otp = '';
            isOtpSent.value = false;
            devOtp.value = '';
            otpMessage.value = '';
            showEmailOtpFields.value = false;
        }
    });
};
</script>

<template>
    <section class="space-y-6">
        <header class="border-b border-[#13283f] pb-3">
            <h2 class="text-sm font-black text-[#00dfb2] uppercase tracking-wider">
                Información del Perfil
            </h2>
            <p class="text-xs text-slate-400 mt-1">
                Actualiza la información de tu perfil, dirección de correo electrónico y foto de avatar.
            </p>
        </header>

        <form
            @submit.prevent="submitForm"
            class="space-y-6 mt-4"
            enctype="multipart/form-data"
        >
            <!-- Foto de Perfil -->
            <div class="space-y-2">
                <label class="text-[10px] font-bold text-[#00dfb2] uppercase tracking-widest block">Foto de Perfil</label>
                
                <input
                    type="file"
                    ref="fileInput"
                    class="hidden"
                    accept="image/*"
                    @change="updatePhotoPreview"
                />

                <div class="flex items-center gap-4">
                    <div class="relative w-20 h-20 rounded-full overflow-hidden border border-[#162d4a] bg-[#040a12] flex items-center justify-center shadow-inner">
                        <img
                            v-if="imagePreview"
                            :src="imagePreview"
                            alt="Avatar"
                            class="w-full h-full object-cover"
                        />
                        <span v-else class="text-xl font-bold text-slate-500 uppercase">
                            {{ user.nombre_usuario ? user.nombre_usuario.split(' ').map(n => n[0]).join('').substring(0, 2).toUpperCase() : 'US' }}
                        </span>
                    </div>

                    <button
                        type="button"
                        @click="selectNewPhoto"
                        class="px-4 py-2 bg-[#040a12] hover:bg-[#0c1a2e] border border-[#162d4a] hover:border-slate-500 rounded-xl text-xs font-bold transition text-slate-350"
                    >
                        Seleccionar Foto
                    </button>
                </div>
                
                <InputError class="mt-2" :message="form.errors.foto" />
            </div>

            <!-- Nombre de Usuario -->
            <div class="space-y-1.5">
                <label for="nombre_usuario" class="text-[10px] font-bold text-[#00dfb2] uppercase tracking-widest block">Nombre de Usuario</label>
                <input
                    id="nombre_usuario"
                    type="text"
                    class="block w-full px-4 py-3 bg-[#040a12] border border-[#162d4a] rounded-xl text-white text-xs focus:outline-none focus:border-[#00dfb2] focus:ring-1 focus:ring-[#00dfb2]/30 transition"
                    v-model="form.nombre_usuario"
                    required
                    autocomplete="name"
                    placeholder="Tu nombre de usuario"
                />
                <InputError class="mt-2" :message="form.errors.nombre_usuario" />
            </div>

            <!-- Correo Electrónico -->
            <div class="space-y-1.5">
                <label for="correo" class="text-[10px] font-bold text-[#00dfb2] uppercase tracking-widest block">Correo Electrónico</label>
                <input
                    id="correo"
                    type="email"
                    class="block w-full px-4 py-3 bg-[#040a12] border border-[#162d4a] rounded-xl text-white text-xs focus:outline-none focus:border-[#00dfb2] focus:ring-1 focus:ring-[#00dfb2]/30 transition"
                    v-model="form.correo"
                    required
                    autocomplete="username"
                    placeholder="correo@ejemplo.com"
                />
                <InputError class="mt-2" :message="form.errors.correo" />
            </div>

            <!-- Ajustes de Seguridad: Doble Factor (2FA) -->
            <div class="pt-4 border-t border-[#13283f]">
                <h3 class="text-[10px] font-bold text-[#00dfb2] uppercase tracking-wider mb-2.5">Ajustes de Seguridad</h3>
                <label class="flex items-start text-slate-400 cursor-pointer select-none">
                    <input
                        type="checkbox"
                        v-model="form.login_2fa"
                        class="sr-only peer"
                    />
                    <div class="w-5 h-5 mr-3 rounded border border-[#162d4a] bg-[#040a12] flex items-center justify-center text-transparent peer-checked:text-[#00dfb2] peer-checked:border-[#00dfb2] transition-all duration-200 flex-shrink-0">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-3.5 h-3.5">
                            <path fill-rule="evenodd" d="M16.704 4.153a.75.75 0 0 1 .143 1.052l-8 10.5a.75.75 0 0 1-1.127.075l-4.5-4.5a.75.75 0 0 1 1.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 0 1 1.05-.143Z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div>
                        <span class="text-xs font-bold text-slate-350">Activar verificación en dos pasos (2FA)</span>
                        <p class="text-[10px] text-slate-500 font-medium mt-0.5">Se solicitará un código OTP enviado a tu correo cada vez que inicies sesión en tu cuenta.</p>
                    </div>
                </label>
            </div>

            <!-- OTP and Password confirmation for Email Update (All roles) -->
            <div v-if="showEmailOtpFields" class="space-y-4 pt-4 border-t border-[#13283f] animate-fade-in">
                <div class="p-3.5 bg-amber-500/5 border border-amber-500/20 rounded-xl text-xs text-amber-400 font-medium leading-relaxed">
                    💡 Has modificado tu dirección de correo electrónico. Para confirmar este cambio, debes solicitar un código de verificación que se enviará al nuevo correo y proporcionar tu contraseña actual.
                </div>

                <!-- Send OTP Button -->
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-[#040a12] p-4 border border-[#162d4a] rounded-xl">
                    <div>
                        <span class="text-xs font-bold text-white block">1. Obtener código de verificación</span>
                        <span class="text-[10px] text-slate-500 font-medium block mt-0.5">Se enviará al nuevo correo ingresado.</span>
                    </div>
                    <button
                        type="button"
                        @click="sendEmailOtp"
                        :disabled="isSendingOtp"
                        class="px-4 py-2 border border-[#162d4a] hover:border-slate-500 bg-[#07111e] hover:bg-[#0c1a2e] text-xs font-bold rounded-xl transition cursor-pointer text-slate-300 disabled:opacity-50"
                    >
                        <span v-if="isSendingOtp">Enviando...</span>
                        <span v-else-if="isOtpSent">Reenviar Código</span>
                        <span v-else>Solicitar Código</span>
                    </button>
                </div>

                <!-- OTP Input -->
                <div class="space-y-1.5">
                    <label for="codigo_otp_email" class="text-[10px] font-bold text-[#00dfb2] uppercase tracking-widest block">Código OTP de Confirmación</label>
                    <input
                        id="codigo_otp_email"
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

                <!-- Password Confirmation Input -->
                <div class="space-y-1.5">
                    <label for="contrasena_actual_email" class="text-[10px] font-bold text-[#00dfb2] uppercase tracking-widest block">2. Confirma tu Contraseña Actual</label>
                    <input
                        id="contrasena_actual_email"
                        v-model="form.contrasena_actual"
                        type="password"
                        placeholder="Ingresa tu contraseña actual"
                        class="block w-full px-4 py-3 bg-[#040a12] border border-[#162d4a] rounded-xl text-white text-xs focus:outline-none focus:border-[#00dfb2] transition"
                        required
                    />
                    <InputError :message="form.errors.contrasena_actual" class="mt-2" />
                </div>
            </div>

            <!-- Guardar Button -->
            <div class="flex items-center gap-4 pt-2">
                <button
                    type="submit"
                    :disabled="form.processing"
                    class="px-5 py-2.5 bg-gradient-to-r from-[#00dfb2] to-[#00a887] hover:from-[#00ffd5] hover:to-[#00c79f] text-slate-950 text-xs font-black rounded-xl shadow-md transition cursor-pointer disabled:opacity-50"
                >
                    Guardar Cambios
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
                        ✓ Cambios guardados correctamente.
                    </p>
                </Transition>
            </div>
        </form>
    </section>
</template>
