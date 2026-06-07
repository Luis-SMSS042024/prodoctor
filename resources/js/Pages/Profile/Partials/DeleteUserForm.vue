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
                    <input
                        id="contrasena"
                        ref="passwordInput"
                        v-model="form.contrasena"
                        type="password"
                        placeholder="Ingresa tu contraseña"
                        class="block w-full px-4 py-3 bg-[#040a12] border border-[#162d4a] rounded-xl text-white text-xs focus:outline-none focus:border-[#00dfb2] transition"
                        @keyup.enter="deleteUser"
                        required
                    />
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
