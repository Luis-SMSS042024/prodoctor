<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import DeleteUserForm from './Partials/DeleteUserForm.vue';
import UpdatePasswordForm from './Partials/UpdatePasswordForm.vue';
import UpdateProfileInformationForm from './Partials/UpdateProfileInformationForm.vue';
import { Head } from '@inertiajs/vue3';

defineProps({
    mustVerifyEmail: {
        type: Boolean,
    },
    status: {
        type: String,
    },
});
</script>

<template>
    <Head title="Mi Perfil - ProDoctor" />

    <AuthenticatedLayout>
        <template #header>
            <div>
                <h2 class="text-xl font-extrabold text-[#00dfb2] tracking-tight">Mi Perfil y Ajustes</h2>
                <p class="text-xs text-slate-400 mt-0.5">Administra tus datos personales, foto de perfil y seguridad de la cuenta.</p>
            </div>
        </template>

        <div class="py-6 space-y-6 max-w-4xl mx-auto select-none">
            <!-- Sección: Datos del Perfil -->
            <div class="bg-[#07111e] border border-[#13283f] p-8 shadow-lg rounded-2xl">
                <UpdateProfileInformationForm
                    :must-verify-email="mustVerifyEmail"
                    :status="status"
                    class="w-full"
                />
            </div>

            <!-- Sección: Cambiar Contraseña -->
            <div class="bg-[#07111e] border border-[#13283f] p-8 shadow-lg rounded-2xl">
                <UpdatePasswordForm class="w-full" />
            </div>

            <!-- Sección: Eliminar Cuenta -->
            <div class="bg-[#07111e]/60 border border-[#2a131a]/80 p-8 shadow-lg rounded-2xl">
                <DeleteUserForm class="w-full" />
            </div>
        </div>
    </AuthenticatedLayout>
</template>
