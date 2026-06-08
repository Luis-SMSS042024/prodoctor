<script setup>
import InputError from '@/Components/InputError.vue';
import Modal from '@/Components/Modal.vue';
import { useForm } from '@inertiajs/vue3';
import { nextTick, ref } from 'vue';

const confirmingUserDeletion = ref(false);
const passwordInput = ref(null);

const form = useForm({
    contrasena: '',
});

const showPassword = ref(false);

const confirmUserDeletion = () => {
    confirmingUserDeletion.value = true;
    nextTick(() => passwordInput.value?.focus());
};

const deleteUser = () => {
    form.delete(route('profile.destroy'), {
        preserveScroll: true,
        onSuccess: () => closeModal(),
        onError: () => passwordInput.value?.focus(),
        onFinish: () => form.reset(),
    });
};

const closeModal = () => {
    confirmingUserDeletion.value = false;
    form.clearErrors();
    form.reset();
};
</script>

<template>
    <section class="space-y-6">
        <header class="border-b border-rose-950/30 pb-3">
            <h2 class="text-sm font-black text-rose-500 uppercase tracking-wider">
                Eliminar Cuenta
            </h2>
            <p class="text-xs text-slate-400 mt-1">
                Una vez que tu cuenta sea eliminada, todos sus recursos y datos serán eliminados permanentemente. Antes de eliminar tu cuenta, descarga cualquier dato o información que desees conservar.
            </p>
        </header>

        <button
            @click="confirmUserDeletion"
            class="px-5 py-2.5 bg-rose-650 hover:bg-rose-700 text-white text-xs font-black rounded-xl shadow-md transition cursor-pointer"
        >
            Eliminar Cuenta
        </button>

        <Modal :show="confirmingUserDeletion" @close="closeModal">
            <!-- Modal wrapper with dark background to override standard white modal -->
            <div class="bg-[#07111e] border border-[#13283f] p-6 text-left text-slate-300">
                <h2 class="text-base font-extrabold text-white">
                    ¿Estás seguro de que deseas eliminar tu cuenta?
                </h2>

                <p class="mt-2 text-xs text-slate-400 leading-relaxed">
                    Una vez que tu cuenta sea eliminada, todos sus recursos y datos serán eliminados de manera permanente. Por favor, ingresa tu contraseña para confirmar la eliminación de la cuenta.
                </p>

                <div class="mt-5 space-y-1.5">
                    <label for="contrasena" class="text-[10px] font-bold text-[#00dfb2] uppercase tracking-widest block">Contraseña para Confirmar</label>
                    <div class="relative">
                        <input
                            id="contrasena"
                            ref="passwordInput"
                            v-model="form.contrasena"
                            :type="showPassword ? 'text' : 'password'"
                            placeholder="Ingresa tu contraseña"
                            class="block w-full pl-4 pr-10 py-3 bg-[#040a12] border border-[#162d4a] rounded-xl text-white text-xs focus:outline-none focus:border-[#00dfb2] transition"
                            @keyup.enter="deleteUser"
                            required
                        />
                        <button
                            type="button"
                            @click="showPassword = !showPassword"
                            class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-slate-500 hover:text-[#00dfb2] transition cursor-pointer"
                        >
                            <svg v-if="!showPassword" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" />
                                <circle cx="12" cy="12" r="3" />
                            </svg>
                            <svg v-else xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 001.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.45 10.45 0 0112 4.5c4.756 0 8.773 3.162 10.065 7.498a10.523 10.523 0 01-4.293 5.774M6.228 6.228L3 3m3.228 3.228l3.65 3.65m7.895 7.895L21 21m-3.228-3.228l-3.65-3.65m0 0a3 3 0 10-4.243-4.243m4.242 4.242L9.88 9.88" />
                            </svg>
                        </button>
                    </div>
                    <InputError :message="form.errors.contrasena" class="mt-2" />
                </div>

                <div class="mt-6 flex justify-end gap-3">
                    <button
                        type="button"
                        @click="closeModal"
                        class="px-4 py-2.5 bg-[#040a12] hover:bg-[#0c1a2e] border border-[#162d4a] text-slate-400 text-xs font-bold rounded-xl transition cursor-pointer"
                    >
                        Cancelar
                    </button>

                    <button
                        type="button"
                        :disabled="form.processing"
                        @click="deleteUser"
                        class="px-5 py-2.5 bg-rose-650 hover:bg-rose-700 text-white text-xs font-black rounded-xl shadow-md transition cursor-pointer disabled:opacity-50"
                    >
                        Eliminar Cuenta Permanentemente
                    </button>
                </div>
            </div>
        </Modal>
    </section>
</template>
