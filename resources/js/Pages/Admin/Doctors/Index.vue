<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm, router } from '@inertiajs/vue3';
import { ref, computed } from 'vue';

const props = defineProps({
    doctors: {
        type: Array,
        required: true,
    },
    specialties: {
        type: Array,
        required: true,
    },
});

// Search
const search = ref('');

const filteredDoctors = computed(() => {
    if (!search.value) return props.doctors;
    const q = search.value.toLowerCase();
    return props.doctors.filter(d => 
        (d.nombres || '').toLowerCase().includes(q) ||
        (d.apellidos || '').toLowerCase().includes(q) ||
        (d.nombre_especialidad || '').toLowerCase().includes(q) ||
        (d.correo || '').toLowerCase().includes(q) ||
        (d.junta_vigilancia || '').toLowerCase().includes(q)
    );
});

// Modals State
const showCreateModal = ref(false);
const showEditModal = ref(false);
const selectedDoctor = ref(null);
const showClave = ref(false);

// Forms
const createForm = useForm({
    nombre_usuario: '',
    correo: '',
    clave: '',
    id_especialidad: '',
    nombres: '',
    apellidos: '',
    telefono: '',
    junta_vigilancia: '',
    foto: null,
});

const editForm = useForm({
    nombre_usuario: '',
    correo: '',
    id_especialidad: '',
    nombres: '',
    apellidos: '',
    telefono: '',
    junta_vigilancia: '',
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

const openEditModal = (doc) => {
    selectedDoctor.value = doc;
    editForm.nombre_usuario = doc.nombre_usuario;
    editForm.correo = doc.correo;
    editForm.id_especialidad = doc.id_especialidad;
    editForm.nombres = doc.nombres;
    editForm.apellidos = doc.apellidos;
    editForm.telefono = doc.telefono;
    editForm.junta_vigilancia = doc.junta_vigilancia;
    editForm.foto = null;
    editPhotoPreview.value = doc.foto ? '/' + doc.foto : null;
    showEditModal.value = true;
};

const closeEditModal = () => {
    showEditModal.value = false;
    editPhotoPreview.value = null;
};

// Submits
const submitCreate = () => {
    createForm.post(route('admin.doctors.store'), {
        forceFormData: true,
        onSuccess: () => {
            closeCreateModal();
            createForm.reset();
        }
    });
};

const submitEdit = () => {
    editForm.post(route('admin.doctors.update', selectedDoctor.value.id_doctor), {
        forceFormData: true,
        onSuccess: () => {
            closeEditModal();
            selectedDoctor.value = null;
        }
    });
};

const deleteDoctor = (doc) => {
    if (confirm(`¿Está seguro de que desea eliminar al Dr. ${doc.nombres} ${doc.apellidos}? Esta acción también eliminará su cuenta de usuario y no se podrá deshacer.`)) {
        router.delete(route('admin.doctors.destroy', doc.id_doctor), {
            onSuccess: () => {
                alert('Médico y cuenta eliminados.');
            }
        });
    }
};
</script>

<template>
    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between w-full">
                <div>
                    <h2 class="text-xl font-extrabold text-slate-800 tracking-tight">Doctores / Especialistas</h2>
                    <p class="text-xs text-slate-500 mt-0.5">Gestión de profesionales médicos del gabinete ProDoctor.</p>
                </div>
                
                <button
                    @click="openCreateModal"
                    class="inline-flex items-center gap-2 px-4 py-2.5 bg-gradient-to-r from-fuchsia-600 to-indigo-650 hover:from-fuchsia-700 hover:to-indigo-755 text-white rounded-xl text-xs font-bold shadow-md shadow-fuchsia-500/20 active:scale-[0.98] transition cursor-pointer"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-4 h-4">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                    </svg>
                    Nuevo Médico
                </button>
            </div>
        </template>

        <Head title="Doctores - Admin ProDoctor" />

        <div class="space-y-6 select-none">
            
            <!-- Filtro -->
            <div class="bg-white rounded-xl border border-slate-200 p-4 shadow-sm flex items-center justify-between gap-4">
                <div class="relative w-full md:w-96">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.637 10.637z" />
                        </svg>
                    </div>
                    <input
                        type="text"
                        placeholder="Buscar por nombre, especialidad, correo o junta..."
                        v-model="search"
                        class="block w-full pl-9 pr-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-slate-800 text-xs focus:outline-none focus:bg-white focus:border-fuchsia-500 focus:ring-4 focus:ring-fuchsia-500/10 transition"
                    />
                </div>
            </div>

            <!-- Tabla de Doctores -->
            <div class="bg-white rounded-xl border-y border-r border-l-4 border-l-purple-500 border-slate-200 overflow-hidden shadow-md">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-slate-50 border-b border-slate-200">
                                <th class="px-6 py-4 text-xs font-bold uppercase tracking-wider text-slate-400">Doctor</th>
                                <th class="px-6 py-4 text-xs font-bold uppercase tracking-wider text-slate-400">Especialidad</th>
                                <th class="px-6 py-4 text-xs font-bold uppercase tracking-wider text-slate-400">Acceso / Correo</th>
                                <th class="px-6 py-4 text-xs font-bold uppercase tracking-wider text-slate-400">Teléfono</th>
                                <th class="px-6 py-4 text-xs font-bold uppercase tracking-wider text-slate-400">Junta Vigilancia</th>
                                <th class="px-6 py-4 text-xs font-bold uppercase tracking-wider text-slate-400 text-right">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            <tr v-for="doc in filteredDoctors" :key="doc.id_doctor" class="hover:bg-slate-50/50 transition">
                                <!-- Nombre -->
                                <td class="px-6 py-4 font-bold text-slate-800 text-sm">
                                    <div class="flex items-center gap-3">
                                        <div
                                            class="w-8 h-8 rounded-full text-white flex items-center justify-center font-bold text-xs shrink-0 shadow-sm overflow-hidden"
                                            :class="doc.foto ? '' : 'bg-gradient-to-tr from-blue-500 to-indigo-500'"
                                        >
                                            <img v-if="doc.foto" :src="'/' + doc.foto" alt="Avatar" class="w-full h-full object-cover" />
                                            <span v-else>
                                                {{ doc.nombres ? doc.nombres[0] + (doc.apellidos ? doc.apellidos[0] : '') : 'DR' }}
                                            </span>
                                        </div>
                                        <div>
                                            <span class="block">Dr. {{ doc.nombres }} {{ doc.apellidos }}</span>
                                        </div>
                                    </div>
                                </td>
                                <!-- Especialidad -->
                                <td class="px-6 py-4">
                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold border bg-purple-50 text-purple-700 border-purple-200">
                                        {{ doc.nombre_especialidad }}
                                    </span>
                                </td>
                                <!-- Usuario / Correo -->
                                <td class="px-6 py-4 text-xs font-semibold text-slate-600">
                                    <div>{{ doc.nombre_usuario }}</div>
                                    <div class="text-[10px] text-slate-400 font-medium mt-0.5">{{ doc.correo }}</div>
                                </td>
                                <!-- Teléfono -->
                                <td class="px-6 py-4 text-slate-655 text-xs font-semibold">
                                    {{ doc.telefono }}
                                </td>
                                <!-- Junta de Vigilancia -->
                                <td class="px-6 py-4 text-slate-600 text-xs font-bold uppercase">
                                    {{ doc.junta_vigilancia }}
                                </td>
                                <!-- Acciones -->
                                <td class="px-6 py-4 text-right space-x-2">
                                    <button
                                        @click="openEditModal(doc)"
                                        class="inline-flex items-center px-3 py-1.5 bg-slate-50 hover:bg-slate-100 border border-slate-200 rounded-lg text-[11px] font-bold text-slate-700 transition cursor-pointer"
                                    >
                                        Editar
                                    </button>
                                    <button
                                        @click="deleteDoctor(doc)"
                                        class="inline-flex items-center px-3 py-1.5 bg-rose-50 hover:bg-rose-100 border border-rose-200 rounded-lg text-[11px] font-bold text-rose-700 transition cursor-pointer"
                                    >
                                        Eliminar
                                    </button>
                                </td>
                            </tr>
                            <tr v-if="filteredDoctors.length === 0">
                                <td colspan="6" class="px-6 py-12 text-center text-slate-400 font-medium">No se encontraron médicos registrados.</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

        </div>

        <!-- MODAL: REGISTRAR DOCTOR (CREATE) -->
        <div v-if="showCreateModal" class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity" @click="closeCreateModal"></div>
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
                
                <div class="relative inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full border border-slate-200">
                    <div class="p-6 border-b border-slate-150 flex items-center justify-between">
                        <div>
                            <h3 class="text-base font-extrabold text-slate-800">Registrar Nuevo Médico</h3>
                            <p class="text-xs text-slate-500 mt-0.5">Crea su perfil clínico y cuenta de usuario en un paso.</p>
                        </div>
                        <button @click="closeCreateModal" class="text-slate-455 hover:text-slate-600 transition cursor-pointer">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-5 h-5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                    <form @submit.prevent="submitCreate">
                        <div class="p-6 space-y-4 max-h-[420px] overflow-y-auto">
                            <!-- Foto de Perfil -->
                            <div class="space-y-1">
                                <label class="text-[10px] font-bold text-slate-455 uppercase block">FOTO DE PERFIL</label>
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
                                <div v-if="createForm.errors.foto" class="text-[10px] text-rose-600 font-semibold">{{ createForm.errors.foto }}</div>
                            </div>

                            <!-- SECCIÓN: DATOS DE ACCESO -->
                            <div class="border-b border-slate-100 pb-2">
                                <h4 class="text-xs font-black text-fuchsia-700 uppercase tracking-wider">1. Cuenta de Acceso</h4>
                            </div>
                            <div class="grid grid-cols-2 gap-3">
                                <div class="space-y-1">
                                    <label class="text-[10px] font-bold text-slate-450 uppercase block">Nombre de Usuario</label>
                                    <input
                                        type="text"
                                        placeholder="Ej. dr.mendez"
                                        v-model="createForm.nombre_usuario"
                                        class="block w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-slate-800 text-xs focus:outline-none focus:border-fuchsia-500 focus:ring-4 focus:ring-fuchsia-500/10 transition"
                                    />
                                    <div v-if="createForm.errors.nombre_usuario" class="text-[10px] text-rose-600 font-semibold">{{ createForm.errors.nombre_usuario }}</div>
                                </div>
                                <div class="space-y-1">
                                    <label class="text-[10px] font-bold text-slate-455 uppercase block">Contraseña</label>
                                    <div class="relative">
                                        <input
                                            :type="showClave ? 'text' : 'password'"
                                            placeholder="Mínimo 6 caracteres"
                                            v-model="createForm.clave"
                                            class="block w-full pl-3 pr-10 py-2 bg-slate-50 border border-slate-200 rounded-xl text-slate-800 text-xs focus:outline-none focus:border-fuchsia-500 focus:ring-4 focus:ring-fuchsia-500/10 transition"
                                        />
                                        <button
                                            type="button"
                                            @click="showClave = !showClave"
                                            class="absolute inset-y-0 right-0 pr-3 flex items-center text-slate-400 hover:text-fuchsia-600 transition cursor-pointer"
                                        >
                                            <svg v-if="!showClave" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" />
                                                <circle cx="12" cy="12" r="3" />
                                            </svg>
                                            <svg v-else xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 001.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.45 10.45 0 0112 4.5c4.756 0 8.773 3.162 10.065 7.498a10.523 10.523 0 01-4.293 5.774M6.228 6.228L3 3m3.228 3.228l3.65 3.65m7.895 7.895L21 21m-3.228-3.228l-3.65-3.65m0 0a3 3 0 10-4.243-4.243m4.242 4.242L9.88 9.88" />
                                            </svg>
                                        </button>
                                    </div>
                                    <div v-if="createForm.errors.clave" class="text-[10px] text-rose-600 font-semibold">{{ createForm.errors.clave }}</div>
                                </div>
                            </div>
                            <div class="space-y-1">
                                <label class="text-[10px] font-bold text-slate-455 uppercase block">Correo Electrónico</label>
                                <input
                                    type="email"
                                    placeholder="correo@ejemplo.com"
                                    v-model="createForm.correo"
                                    class="block w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-slate-800 text-xs focus:outline-none focus:border-fuchsia-500 focus:ring-4 focus:ring-fuchsia-500/10 transition"
                                />
                                <div v-if="createForm.errors.correo" class="text-[10px] text-rose-600 font-semibold">{{ createForm.errors.correo }}</div>
                            </div>

                            <!-- SECCIÓN: PERFIL CLÍNICO -->
                            <div class="border-b border-slate-100 pb-2 pt-2">
                                <h4 class="text-xs font-black text-fuchsia-700 uppercase tracking-wider">2. Datos Clínicos</h4>
                            </div>
                            <div class="grid grid-cols-2 gap-3">
                                <div class="space-y-1">
                                    <label class="text-[10px] font-bold text-slate-455 uppercase block">Nombres</label>
                                    <input
                                        type="text"
                                        v-model="createForm.nombres"
                                        class="block w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-slate-800 text-xs focus:outline-none focus:border-fuchsia-500 focus:ring-4 focus:ring-fuchsia-500/10 transition"
                                    />
                                    <div v-if="createForm.errors.nombres" class="text-[10px] text-rose-600 font-semibold">{{ createForm.errors.nombres }}</div>
                                </div>
                                <div class="space-y-1">
                                    <label class="text-[10px] font-bold text-slate-455 uppercase block">Apellidos</label>
                                    <input
                                        type="text"
                                        v-model="createForm.apellidos"
                                        class="block w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-slate-800 text-xs focus:outline-none focus:border-fuchsia-500 focus:ring-4 focus:ring-fuchsia-500/10 transition"
                                    />
                                    <div v-if="createForm.errors.apellidos" class="text-[10px] text-rose-600 font-semibold">{{ createForm.errors.apellidos }}</div>
                                </div>
                            </div>

                            <div class="space-y-1">
                                <label class="text-[10px] font-bold text-slate-455 uppercase block">Especialidad</label>
                                <select
                                    v-model="createForm.id_especialidad"
                                    class="block w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-slate-800 text-xs focus:outline-none focus:border-fuchsia-500 focus:ring-4 focus:ring-fuchsia-500/10 transition"
                                >
                                    <option value="" disabled>Selecciona la especialidad...</option>
                                    <option
                                        v-for="esp in specialties"
                                        :key="esp.id_especialidad"
                                        :value="esp.id_especialidad"
                                    >
                                        {{ esp.nombre_especialidad }}
                                    </option>
                                </select>
                                <div v-if="createForm.errors.id_especialidad" class="text-[10px] text-rose-600 font-semibold">{{ createForm.errors.id_especialidad }}</div>
                            </div>

                            <div class="grid grid-cols-2 gap-3">
                                <div class="space-y-1">
                                    <label class="text-[10px] font-bold text-slate-455 uppercase block">Teléfono</label>
                                    <input
                                        type="text"
                                        v-model="createForm.telefono"
                                        class="block w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-slate-800 text-xs focus:outline-none focus:border-fuchsia-500 focus:ring-4 focus:ring-fuchsia-500/10 transition"
                                    />
                                    <div v-if="createForm.errors.telefono" class="text-[10px] text-rose-600 font-semibold">{{ createForm.errors.telefono }}</div>
                                </div>
                                <div class="space-y-1">
                                    <label class="text-[10px] font-bold text-slate-455 uppercase block">Junta Vigilancia</label>
                                    <input
                                        type="text"
                                        placeholder="Ej. JV-12345"
                                        v-model="createForm.junta_vigilancia"
                                        class="block w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-slate-800 text-xs focus:outline-none focus:border-fuchsia-500 focus:ring-4 focus:ring-fuchsia-500/10 transition"
                                    />
                                    <div v-if="createForm.errors.junta_vigilancia" class="text-[10px] text-rose-600 font-semibold">{{ createForm.errors.junta_vigilancia }}</div>
                                </div>
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
                                Registrar Médico
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- MODAL: MODIFICAR DOCTOR (EDIT) -->
        <div v-if="showEditModal && selectedDoctor" class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity" @click="closeEditModal"></div>
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
                
                <div class="relative inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full border border-slate-200">
                    <div class="p-6 border-b border-slate-150 flex items-center justify-between">
                        <div>
                            <h3 class="text-base font-extrabold text-slate-800">Modificar Médico</h3>
                            <p class="text-xs text-slate-500 mt-0.5">Actualiza el perfil y datos de acceso del doctor.</p>
                        </div>
                        <button @click="closeEditModal" class="text-slate-455 hover:text-slate-655 transition cursor-pointer">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-5 h-5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                    <form @submit.prevent="submitEdit">
                        <div class="p-6 space-y-4 max-h-[420px] overflow-y-auto">
                            <!-- Foto de Perfil -->
                            <div class="space-y-1">
                                <label class="text-[10px] font-bold text-slate-455 uppercase block">FOTO DE PERFIL</label>
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
                                <div v-if="editForm.errors.foto" class="text-[10px] text-rose-600 font-semibold">{{ editForm.errors.foto }}</div>
                            </div>

                            <!-- CUENTA ACCESO -->
                            <div class="border-b border-slate-100 pb-2">
                                <h4 class="text-xs font-black text-fuchsia-700 uppercase tracking-wider">1. Cuenta de Acceso</h4>
                            </div>
                            <div class="grid grid-cols-2 gap-3">
                                <div class="space-y-1">
                                    <label class="text-[10px] font-bold text-slate-455 uppercase block">Nombre de Usuario</label>
                                    <input
                                        type="text"
                                        v-model="editForm.nombre_usuario"
                                        class="block w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-slate-800 text-xs focus:outline-none focus:border-fuchsia-500 focus:ring-4 focus:ring-fuchsia-500/10 transition"
                                    />
                                    <div v-if="editForm.errors.nombre_usuario" class="text-[10px] text-rose-600 font-semibold">{{ editForm.errors.nombre_usuario }}</div>
                                </div>
                                <div class="space-y-1">
                                    <label class="text-[10px] font-bold text-slate-455 uppercase block">Correo Electrónico</label>
                                    <input
                                        type="email"
                                        v-model="editForm.correo"
                                        class="block w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-slate-800 text-xs focus:outline-none focus:border-fuchsia-500 focus:ring-4 focus:ring-fuchsia-500/10 transition"
                                    />
                                    <div v-if="editForm.errors.correo" class="text-[10px] text-rose-600 font-semibold">{{ editForm.errors.correo }}</div>
                                </div>
                            </div>

                            <!-- DATOS CLÍNICOS -->
                            <div class="border-b border-slate-100 pb-2 pt-2">
                                <h4 class="text-xs font-black text-fuchsia-700 uppercase tracking-wider">2. Datos Clínicos</h4>
                            </div>
                            <div class="grid grid-cols-2 gap-3">
                                <div class="space-y-1">
                                    <label class="text-[10px] font-bold text-slate-455 uppercase block">Nombres</label>
                                    <input
                                        type="text"
                                        v-model="editForm.nombres"
                                        class="block w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-slate-800 text-xs focus:outline-none focus:border-fuchsia-500 focus:ring-4 focus:ring-fuchsia-500/10 transition"
                                    />
                                    <div v-if="editForm.errors.nombres" class="text-[10px] text-rose-600 font-semibold">{{ editForm.errors.nombres }}</div>
                                </div>
                                <div class="space-y-1">
                                    <label class="text-[10px] font-bold text-slate-455 uppercase block">Apellidos</label>
                                    <input
                                        type="text"
                                        v-model="editForm.apellidos"
                                        class="block w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-slate-800 text-xs focus:outline-none focus:border-fuchsia-500 focus:ring-4 focus:ring-fuchsia-500/10 transition"
                                    />
                                    <div v-if="editForm.errors.apellidos" class="text-[10px] text-rose-600 font-semibold">{{ editForm.errors.apellidos }}</div>
                                </div>
                            </div>

                            <div class="space-y-1">
                                <label class="text-[10px] font-bold text-slate-455 uppercase block">Especialidad</label>
                                <select
                                    v-model="editForm.id_especialidad"
                                    class="block w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-slate-800 text-xs focus:outline-none focus:border-fuchsia-500 focus:ring-4 focus:ring-fuchsia-500/10 transition"
                                >
                                    <option
                                        v-for="esp in specialties"
                                        :key="esp.id_especialidad"
                                        :value="esp.id_especialidad"
                                    >
                                        {{ esp.nombre_especialidad }}
                                    </option>
                                </select>
                                <div v-if="editForm.errors.id_especialidad" class="text-[10px] text-rose-600 font-semibold">{{ editForm.errors.id_especialidad }}</div>
                            </div>

                            <div class="grid grid-cols-2 gap-3">
                                <div class="space-y-1">
                                    <label class="text-[10px] font-bold text-slate-455 uppercase block">Teléfono</label>
                                    <input
                                        type="text"
                                        v-model="editForm.telefono"
                                        class="block w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-slate-800 text-xs focus:outline-none focus:border-fuchsia-500 focus:ring-4 focus:ring-fuchsia-500/10 transition"
                                    />
                                    <div v-if="editForm.errors.telefono" class="text-[10px] text-rose-600 font-semibold">{{ editForm.errors.telefono }}</div>
                                </div>
                                <div class="space-y-1">
                                    <label class="text-[10px] font-bold text-slate-455 uppercase block">Junta Vigilancia</label>
                                    <input
                                        type="text"
                                        v-model="editForm.junta_vigilancia"
                                        class="block w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-slate-800 text-xs focus:outline-none focus:border-fuchsia-500 focus:ring-4 focus:ring-fuchsia-500/10 transition"
                                    />
                                    <div v-if="editForm.errors.junta_vigilancia" class="text-[10px] text-rose-600 font-semibold">{{ editForm.errors.junta_vigilancia }}</div>
                                </div>
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
