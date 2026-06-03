<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

const form = useForm({
    nombre_usuario: '',
    correo: '',
    contrasena: '',
    contrasena_confirmation: '',
});

const submit = () => {
    form.post(route('register'), {
        onFinish: () => form.reset('contrasena', 'contrasena_confirmation'),
    });
};
</script>

<template>
    <GuestLayout>
        <Head title="Registrarse" />

        <form @submit.prevent="submit">
            <div>
                <InputLabel for="nombre_usuario" value="Nombre de Usuario" />

                <TextInput
                    id="nombre_usuario"
                    type="text"
                    class="mt-1 block w-full"
                    v-model="form.nombre_usuario"
                    required
                    autofocus
                    autocomplete="name"
                />

                <InputError class="mt-2" :message="form.errors.nombre_usuario" />
            </div>

            <div class="mt-4">
                <InputLabel for="correo" value="Correo Electrónico" />

                <TextInput
                    id="correo"
                    type="email"
                    class="mt-1 block w-full"
                    v-model="form.correo"
                    required
                    autocomplete="username"
                />

                <InputError class="mt-2" :message="form.errors.correo" />
            </div>

            <div class="mt-4">
                <InputLabel for="contrasena" value="Contraseña" />

                <TextInput
                    id="contrasena"
                    type="password"
                    class="mt-1 block w-full"
                    v-model="form.contrasena"
                    required
                    autocomplete="new-password"
                />

                <InputError class="mt-2" :message="form.errors.contrasena" />
            </div>

            <div class="mt-4">
                <InputLabel
                    for="contrasena_confirmation"
                    value="Confirmar Contraseña"
                />

                <TextInput
                    id="contrasena_confirmation"
                    type="password"
                    class="mt-1 block w-full"
                    v-model="form.contrasena_confirmation"
                    required
                    autocomplete="new-password"
                />

                <InputError
                    class="mt-2"
                    :message="form.errors.contrasena_confirmation"
                />
            </div>

            <div class="mt-4 flex items-center justify-end">
                <Link
                    :href="route('login')"
                    class="rounded-md text-sm text-gray-600 underline hover:text-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
                >
                    ¿Ya estás registrado?
                </Link>

                <PrimaryButton
                    class="ms-4"
                    :class="{ 'opacity-25': form.processing }"
                    :disabled="form.processing"
                >
                    Registrarse
                </PrimaryButton>
            </div>
        </form>
    </GuestLayout>
</template>
