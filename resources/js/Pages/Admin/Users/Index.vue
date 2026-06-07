<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm, router, usePage } from '@inertiajs/vue3';
import { ref, computed } from 'vue';

const props = defineProps({
    users: {
        type: Array,
        required: true,
    },
});

const page = usePage();
const currentUser = computed(() => page.props.auth.user);

// Search User
const search = ref('');

const filteredUsers = computed(() => {
    if (!search.value) return props.users;
    const q = search.value.toLowerCase();
    return props.users.filter(u => 
        u.nombre_usuario.toLowerCase().includes(q) ||
        u.correo.toLowerCase().includes(q) ||
        u.rol.toLowerCase().includes(q)
    );
});

// Modals State
const showCreateModal = ref(false);
const showEditModal = ref(false);
const selectedUser = ref(null);

// Forms
const createForm = useForm({
    nombre_usuario: '',
    correo: '',
    clave: '',
    rol: 'paciente',
    foto: null,
});

const editForm = useForm({
    nombre_usuario: '',
    correo: '',
    rol: '',
    clave: '', // optional password update
    foto: null,
    _method: 'PUT',
});

const createPhotoPreview = ref(null);
const editPhotoPreview = ref(null);
const createFileInput = ref(null);
const editFileInput = ref(null);

const selectCreatePhoto = () => {
    createFileInput.value.click();
};

const selectEditPhoto = () => {
    editFileInput.value.click();
};

const updateCreatePhotoPreview = () => {
    const file = createFileInput.value.files[0];
    if (!file) return;
    createForm.foto = file;
    const reader = new FileReader();
    reader.onload = (e) => {
        createPhotoPreview.value = e.target.result;
    };
    reader.readAsDataURL(file);
};

const updateEditPhotoPreview = () => {
    const file = editFileInput.value.files[0];
    if (!file) return;
    editForm.foto = file;
    const reader = new FileReader();
    reader.onload = (e) => {
        editPhotoPreview.value = e.target.result;
    };
    reader.readAsDataURL(file);
};

// Actions
const openCreateModal = () => {
    createForm.reset();
    createPhotoPreview.value = null;
    showCreateModal.value = true;
};

const closeCreateModal = () => {
    showCreateModal.value = false;
    createPhotoPreview.value = null;
};

const openEditModal = (userItem) => {
    selectedUser.value = userItem;
    editForm.nombre_usuario = userItem.nombre_usuario;
    editForm.correo = userItem.correo;
    editForm.rol = userItem.rol;
    editForm.clave = ''; // Reset password field in edit
    editForm.foto = null;
    editPhotoPreview.value = userItem.foto ? '/' + userItem.foto : null;
    showEditModal.value = true;
};

const closeEditModal = () => {
    showEditModal.value = false;
    editPhotoPreview.value = null;
};

// Form Submits
const submitCreate = () => {
    createForm.post(route('admin.users.store'), {
        forceFormData: true,
        onSuccess: () => {
            closeCreateModal();
            createForm.reset();
        }
    });
};

const submitEdit = () => {
    editForm.post(route('admin.users.update', selectedUser.value.id_usuario), {
        forceFormData: true,
        onSuccess: () => {
            closeEditModal();
            selectedUser.value = null;
        }
    });
};

const deleteUser = (userItem) => {
    if (userItem.id_usuario === currentUser.value.id_usuario) {
        alert('No puedes eliminar tu propio usuario de administrador.');
        return;
    }
    
    if (confirm(`¿Está seguro de que desea eliminar al usuario "${userItem.nombre_usuario}" (${userItem.rol})? Esta acción no se puede deshacer.`)) {
        router.delete(route('admin.users.destroy', userItem.id_usuario), {
            onSuccess: () => {
                alert('Usuario eliminado correctamente.');
            }
        });
    }
};

// Role display badges
const getRoleBadgeClasses = (role) => {
    switch (role) {
        case 'admin':
            return 'bg-fuchsia-50 text-fuchsia-700 border-fuchsia-200';
        case 'doctor':
            return 'bg-blue-50 text-blue-700 border-blue-200';
        case 'paciente':
            return 'bg-teal-50 text-teal-700 border-teal-200';
        default:
            return 'bg-slate-50 text-slate-700 border-slate-200';
    }
};

const getRoleInitialsClasses = (role) => {
    switch (role) {
        case 'admin':
            return 'bg-gradient-to-tr from-fuchsia-500 to-indigo-650';
        case 'doctor':
            return 'bg-gradient-to-tr from-blue-500 to-indigo-500';
        case 'paciente':
            return 'bg-gradient-to-tr from-teal-500 to-emerald-600';
        default:
            return 'bg-gradient-to-tr from-slate-400 to-slate-600';
    }
};
</script>

<template>
    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between w-full">
                <div>
                    <h2 class="text-xl font-extrabold text-slate-800 tracking-tight">Cuentas de Usuarios</h2>
                    <p class="text-xs text-slate-500 mt-0.5">Gestión y control de accesos de Administradores, Médicos y Pacientes.</p>
                </div>
                
                <button
                    @click="openCreateModal"
                    class="inline-flex items-center gap-2 px-4 py-2.5 bg-gradient-to-r from-fuchsia-600 to-indigo-650 hover:from-fuchsia-700 hover:to-indigo-755 text-white rounded-xl text-xs font-bold shadow-md shadow-fuchsia-500/20 active:scale-[0.98] transition cursor-pointer"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-4 h-4">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                    </svg>
                    Nuevo Usuario
                </button>
            </div>
        </template>

        <Head title="Usuarios - Admin ProDoctor" />

        <div class="space-y-6 select-none">
            
            <!-- Barra de Filtro -->
            <div class="bg-white rounded-xl border border-slate-200 p-4 shadow-sm flex items-center justify-between gap-4">
                <div class="relative w-full md:w-96">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.637 10.637z" />
                        </svg>
                    </div>
                    <input
                        type="text"
                        placeholder="Buscar por nombre, correo o rol..."
                        v-model="search"
                        class="block w-full pl-9 pr-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-slate-800 text-xs focus:outline-none focus:bg-white focus:border-fuchsia-500 focus:ring-4 focus:ring-fuchsia-500/10 transition"
                    />
                </div>
            </div>

            <!-- Tabla de Usuarios -->
            <div class="bg-white rounded-xl border-y border-r border-l-4 border-l-fuchsia-500 border-slate-200 overflow-hidden shadow-md">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-slate-50 border-b border-slate-200">
                                <th class="px-6 py-4 text-xs font-bold uppercase tracking-wider text-slate-400">ID</th>
                                <th class="px-6 py-4 text-xs font-bold uppercase tracking-wider text-slate-400">Nombre de Usuario</th>
                                <th class="px-6 py-4 text-xs font-bold uppercase tracking-wider text-slate-400">Correo Electrónico</th>
                                <th class="px-6 py-4 text-xs font-bold uppercase tracking-wider text-slate-400">Rol</th>
                                <th class="px-6 py-4 text-xs font-bold uppercase tracking-wider text-slate-400 text-right">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            <tr v-for="userItem in filteredUsers" :key="userItem.id_usuario" class="hover:bg-slate-50/50 transition">
                                <!-- ID -->
                                <td class="px-6 py-4 font-extrabold text-fuchsia-600 text-xs">
                                    #USR-{{ String(userItem.id_usuario).padStart(3, '0') }}
                                </td>
                                <!-- Nombre con Avatar -->
                                <td class="px-6 py-4 font-bold text-slate-800 text-sm">
                                    <div class="flex items-center gap-3">
                                        <div
                                            class="w-8 h-8 rounded-full text-white flex items-center justify-center font-bold text-xs shrink-0 shadow-sm overflow-hidden"
                                            :class="userItem.foto ? '' : getRoleInitialsClasses(userItem.rol)"
                                        >
                                            <img v-if="userItem.foto" :src="'/' + userItem.foto" alt="Avatar" class="w-full h-full object-cover" />
                                            <span v-else>
                                                {{ userItem.nombre_usuario ? userItem.nombre_usuario.split(' ').map(n => n[0]).join('').substring(0, 2).toUpperCase() : 'US' }}
                                            </span>
                                        </div>
                                        <div>
                                            <span class="text-slate-800 font-bold block">{{ userItem.nombre_usuario }}</span>
                                            <span v-if="userItem.id_usuario === currentUser.id_usuario" class="inline-flex items-center gap-1 mt-0.5 px-1.5 py-0.5 bg-slate-100 text-[9px] text-slate-550 border border-slate-200 rounded font-extrabold uppercase">Tú (Sesión Activa)</span>
                                        </div>
                                    </div>
                                </td>
                                <!-- Correo -->
                                <td class="px-6 py-4 text-slate-655 text-xs font-semibold">
                                    {{ userItem.correo }}
                                </td>
                                <!-- Rol -->
                                <td class="px-6 py-4">
                                    <span
                                        class="px-2.5 py-1 rounded-lg text-[10px] font-extrabold border uppercase tracking-wider"
                                        :class="getRoleBadgeClasses(userItem.rol)"
                                    >
                                        {{ userItem.rol }}
                                    </span>
                                </td>
                                <!-- Acciones -->
                                <td class="px-6 py-4 text-right space-x-2">
                                    <button
                                        @click="openEditModal(userItem)"
                                        class="inline-flex items-center px-3 py-1.5 bg-slate-50 hover:bg-slate-100 border border-slate-200 rounded-lg text-[11px] font-bold text-slate-700 transition cursor-pointer"
                                    >
                                        Editar
                                    </button>
                                    <button
                                        v-if="userItem.id_usuario !== currentUser.id_usuario"
                                        @click="deleteUser(userItem)"
                                        class="inline-flex items-center px-3 py-1.5 bg-rose-50 hover:bg-rose-100 border border-rose-200 rounded-lg text-[11px] font-bold text-rose-700 transition cursor-pointer"
                                    >
                                        Eliminar
                                    </button>
                                    <span v-else class="text-[10px] text-slate-400 font-bold italic pr-2">Protegido</span>
                                </td>
                            </tr>
                            <tr v-if="filteredUsers.length === 0">
                                <td colspan="5" class="px-6 py-12 text-center text-slate-400 font-medium">No se encontraron usuarios registrados.</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

        </div>

        <!-- MODAL: CREAR USUARIO -->
        <div v-if="showCreateModal" class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity" @click="closeCreateModal"></div>
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
                
                <div class="relative inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full border border-slate-200">
                    <div class="p-6 border-b border-slate-150 flex items-center justify-between">
                        <div>
                            <h3 class="text-base font-extrabold text-slate-800">Registrar Nuevo Usuario</h3>
                            <p class="text-xs text-slate-500 mt-0.5">Agrega una nueva cuenta de acceso al sistema.</p>
                        </div>
                        <button @click="closeCreateModal" class="text-slate-455 hover:text-slate-600 transition cursor-pointer">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-5 h-5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                    <form @submit.prevent="submitCreate">
                        <div class="p-6 space-y-4">
                            <!-- Foto de Perfil -->
                            <div class="space-y-1.5">
                                <label class="text-xs font-bold text-slate-550 uppercase tracking-wider block">FOTO DE PERFIL</label>
                                <input
                                    type="file"
                                    ref="createFileInput"
                                    class="hidden"
                                    accept="image/*"
                                    @change="updateCreatePhotoPreview"
                                />
                                <div class="flex items-center gap-3">
                                    <div class="relative w-12 h-12 rounded-full overflow-hidden border border-slate-200 bg-slate-50 flex items-center justify-center">
                                        <img
                                            v-if="createPhotoPreview"
                                            :src="createPhotoPreview"
                                            alt="Avatar"
                                            class="w-full h-full object-cover"
                                        />
                                        <svg v-else xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6 text-slate-400">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75" />
                                        </svg>
                                    </div>
                                    <button
                                        type="button"
                                        @click="selectCreatePhoto"
                                        class="px-3 py-1.5 border border-slate-200 hover:bg-slate-50 rounded-lg text-xs font-bold transition cursor-pointer"
                                    >
                                        Subir Foto
                                    </button>
                                </div>
                                <div v-if="createForm.errors.foto" class="text-xs text-rose-600 font-semibold mt-1">{{ createForm.errors.foto }}</div>
                            </div>

                            <!-- Nombre Usuario -->
                            <div class="space-y-1.5">
                                <label class="text-xs font-bold text-slate-550 uppercase tracking-wider block">NOMBRE DE USUARIO</label>
                                <input
                                    type="text"
                                    placeholder="Ej. Juan Pérez, Administrador Principal..."
                                    v-model="createForm.nombre_usuario"
                                    class="block w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-slate-800 text-xs focus:outline-none focus:bg-white focus:border-fuchsia-500 focus:ring-4 focus:ring-fuchsia-500/10 transition"
                                />
                                <div v-if="createForm.errors.nombre_usuario" class="text-xs text-rose-600 font-semibold mt-1">{{ createForm.errors.nombre_usuario }}</div>
                            </div>

                            <!-- Correo -->
                            <div class="space-y-1.5">
                                <label class="text-xs font-bold text-slate-550 uppercase tracking-wider block">CORREO ELECTRÓNICO</label>
                                <input
                                    type="email"
                                    placeholder="Ej. usuario@prodoctor.com"
                                    v-model="createForm.correo"
                                    class="block w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-slate-800 text-xs focus:outline-none focus:bg-white focus:border-fuchsia-500 focus:ring-4 focus:ring-fuchsia-500/10 transition"
                                />
                                <div v-if="createForm.errors.correo" class="text-xs text-rose-600 font-semibold mt-1">{{ createForm.errors.correo }}</div>
                            </div>

                            <!-- Rol -->
                            <div class="space-y-1.5">
                                <label class="text-xs font-bold text-slate-550 uppercase tracking-wider block">ROL DEL USUARIO</label>
                                <select
                                    v-model="createForm.rol"
                                    class="block w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-slate-800 text-xs focus:outline-none focus:bg-white focus:border-fuchsia-500 focus:ring-4 focus:ring-fuchsia-500/10 transition"
                                >
                                    <option value="admin">Administrador</option>
                                    <option value="doctor">Doctor / Especialista</option>
                                    <option value="paciente">Paciente</option>
                                </select>
                                <div v-if="createForm.errors.rol" class="text-xs text-rose-600 font-semibold mt-1">{{ createForm.errors.rol }}</div>
                            </div>

                            <!-- Clave -->
                            <div class="space-y-1.5">
                                <label class="text-xs font-bold text-slate-550 uppercase tracking-wider block">CONTRASEÑA</label>
                                <input
                                    type="password"
                                    placeholder="Mínimo 6 caracteres"
                                    v-model="createForm.clave"
                                    class="block w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-slate-800 text-xs focus:outline-none focus:bg-white focus:border-fuchsia-500 focus:ring-4 focus:ring-fuchsia-500/10 transition"
                                />
                                <div v-if="createForm.errors.clave" class="text-xs text-rose-600 font-semibold mt-1">{{ createForm.errors.clave }}</div>
                            </div>
                        </div>

                        <div class="p-6 bg-slate-50 border-t border-slate-200 text-right space-x-3 rounded-b-2xl">
                            <button
                                type="button"
                                @click="closeCreateModal"
                                class="px-4 py-2.5 bg-white border border-slate-200 hover:bg-slate-50 text-slate-655 text-xs font-bold rounded-xl transition cursor-pointer"
                            >
                                Cancelar
                            </button>
                            <button
                                type="submit"
                                :disabled="createForm.processing"
                                class="px-5 py-2.5 bg-fuchsia-600 hover:bg-fuchsia-700 text-white text-xs font-bold rounded-xl shadow-md transition cursor-pointer disabled:opacity-50"
                            >
                                Registrar Usuario
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- MODAL: EDITAR USUARIO -->
        <div v-if="showEditModal && selectedUser" class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity" @click="closeEditModal"></div>
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
                
                <div class="relative inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full border border-slate-200">
                    <div class="p-6 border-b border-slate-150 flex items-center justify-between">
                        <div>
                            <h3 class="text-base font-extrabold text-slate-800">Modificar Cuenta de Usuario</h3>
                            <p class="text-xs text-slate-500 mt-0.5">Corrige la información de acceso de la cuenta.</p>
                        </div>
                        <button @click="closeEditModal" class="text-slate-455 hover:text-slate-655 transition cursor-pointer">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-5 h-5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                    <form @submit.prevent="submitEdit">
                        <div class="p-6 space-y-4">
                            <!-- Foto de Perfil -->
                            <div class="space-y-1.5">
                                <label class="text-xs font-bold text-slate-550 uppercase tracking-wider block">FOTO DE PERFIL</label>
                                <input
                                    type="file"
                                    ref="editFileInput"
                                    class="hidden"
                                    accept="image/*"
                                    @change="updateEditPhotoPreview"
                                />
                                <div class="flex items-center gap-3">
                                    <div class="relative w-12 h-12 rounded-full overflow-hidden border border-slate-200 bg-slate-50 flex items-center justify-center">
                                        <img
                                            v-if="editPhotoPreview"
                                            :src="editPhotoPreview"
                                            alt="Avatar"
                                            class="w-full h-full object-cover"
                                        />
                                        <svg v-else xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6 text-slate-400">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75" />
                                        </svg>
                                    </div>
                                    <button
                                        type="button"
                                        @click="selectEditPhoto"
                                        class="px-3 py-1.5 border border-slate-200 hover:bg-slate-50 rounded-lg text-xs font-bold transition cursor-pointer"
                                    >
                                        Cambiar Foto
                                    </button>
                                </div>
                                <div v-if="editForm.errors.foto" class="text-xs text-rose-600 font-semibold mt-1">{{ editForm.errors.foto }}</div>
                            </div>

                            <!-- Nombre Usuario -->
                            <div class="space-y-1.5">
                                <label class="text-xs font-bold text-slate-550 uppercase tracking-wider block">NOMBRE DE USUARIO</label>
                                <input
                                    type="text"
                                    v-model="editForm.nombre_usuario"
                                    class="block w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-slate-800 text-xs focus:outline-none focus:bg-white focus:border-fuchsia-500 focus:ring-4 focus:ring-fuchsia-500/10 transition"
                                />
                                <div v-if="editForm.errors.nombre_usuario" class="text-xs text-rose-600 font-semibold mt-1">{{ editForm.errors.nombre_usuario }}</div>
                            </div>

                            <!-- Correo -->
                            <div class="space-y-1.5">
                                <label class="text-xs font-bold text-slate-550 uppercase tracking-wider block">CORREO ELECTRÓNICO</label>
                                <input
                                    type="email"
                                    v-model="editForm.correo"
                                    class="block w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-slate-800 text-xs focus:outline-none focus:bg-white focus:border-fuchsia-500 focus:ring-4 focus:ring-fuchsia-500/10 transition"
                                />
                                <div v-if="editForm.errors.correo" class="text-xs text-rose-600 font-semibold mt-1">{{ editForm.errors.correo }}</div>
                            </div>

                            <!-- Rol -->
                            <div class="space-y-1.5">
                                <label class="text-xs font-bold text-slate-550 uppercase tracking-wider block">ROL DEL USUARIO</label>
                                <select
                                    v-model="editForm.rol"
                                    :disabled="selectedUser.id_usuario === currentUser.id_usuario"
                                    class="block w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-slate-800 text-xs focus:outline-none focus:bg-white focus:border-fuchsia-500 focus:ring-4 focus:ring-fuchsia-500/10 transition disabled:opacity-60"
                                >
                                    <option value="admin">Administrador</option>
                                    <option value="doctor">Doctor / Especialista</option>
                                    <option value="paciente">Paciente</option>
                                </select>
                                <span v-if="selectedUser.id_usuario === currentUser.id_usuario" class="text-[10px] text-slate-400 font-medium mt-1 block">No puedes cambiar tu propio rol para evitar bloquear tu acceso.</span>
                                <div v-if="editForm.errors.rol" class="text-xs text-rose-600 font-semibold mt-1">{{ editForm.errors.rol }}</div>
                            </div>

                            <!-- Clave (Opcional) -->
                            <div class="space-y-1.5">
                                <label class="text-xs font-bold text-slate-550 uppercase tracking-wider block">CONTRASEÑA NUEVA</label>
                                <input
                                    type="password"
                                    placeholder="Dejar en blanco para no modificar"
                                    v-model="editForm.clave"
                                    class="block w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-slate-800 text-xs focus:outline-none focus:bg-white focus:border-fuchsia-500 focus:ring-4 focus:ring-fuchsia-500/10 transition"
                                />
                                <div v-if="editForm.errors.clave" class="text-xs text-rose-600 font-semibold mt-1">{{ editForm.errors.clave }}</div>
                            </div>
                        </div>

                        <div class="p-6 bg-slate-50 border-t border-slate-200 text-right space-x-3 rounded-b-2xl">
                            <button
                                type="button"
                                @click="closeEditModal"
                                class="px-4 py-2.5 bg-white border border-slate-200 hover:bg-slate-50 text-slate-655 text-xs font-bold rounded-xl transition cursor-pointer"
                            >
                                Cancelar
                            </button>
                            <button
                                type="submit"
                                :disabled="editForm.processing"
                                class="px-5 py-2.5 bg-fuchsia-600 hover:bg-fuchsia-700 text-white text-xs font-bold rounded-xl shadow-md transition cursor-pointer"
                            >
                                Guardar Cambios
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

    </AuthenticatedLayout>
</template>
