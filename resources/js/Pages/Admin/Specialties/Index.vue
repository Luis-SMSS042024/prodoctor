<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm, router } from '@inertiajs/vue3';
import { ref, computed } from 'vue';

const props = defineProps({
    specialties: {
        type: Array,
        required: true,
    },
});

// Search Specialty
const search = ref('');

const filteredSpecialties = computed(() => {
    if (!search.value) return props.specialties;
    const q = search.value.toLowerCase();
    return props.specialties.filter(s => 
        s.nombre_especialidad.toLowerCase().includes(q) ||
        s.descripcion.toLowerCase().includes(q)
    );
});

// Modals State
const showCreateModal = ref(false);
const showEditModal = ref(false);
const selectedSpecialty = ref(null);

// Forms
const createForm = useForm({
    nombre_especialidad: '',
    descripcion: '',
});

const editForm = useForm({
    nombre_especialidad: '',
    descripcion: '',
});

// Actions
const openCreateModal = () => {
    createForm.reset();
    showCreateModal.value = true;
};

const closeCreateModal = () => {
    showCreateModal.value = false;
};

const openEditModal = (spec) => {
    selectedSpecialty.value = spec;
    editForm.nombre_especialidad = spec.nombre_especialidad;
    editForm.descripcion = spec.descripcion;
    showEditModal.value = true;
};

const closeEditModal = () => {
    showEditModal.value = false;
};

// Form Submits
const submitCreate = () => {
    createForm.post(route('admin.specialties.store'), {
        onSuccess: () => {
            closeCreateModal();
            createForm.reset();
        }
    });
};

const submitEdit = () => {
    editForm.put(route('admin.specialties.update', selectedSpecialty.value.id_especialidad), {
        onSuccess: () => {
            closeEditModal();
            selectedSpecialty.value = null;
        }
    });
};

const deleteSpecialty = (spec) => {
    if (confirm(`¿Está seguro de que desea eliminar la especialidad "${spec.nombre_especialidad}"? Todos los doctores vinculados quedarán sin especialidad asociada.`)) {
        router.delete(route('admin.specialties.destroy', spec.id_especialidad), {
            onSuccess: () => {
                alert('Especialidad eliminada correctamente.');
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
                    <h2 class="text-xl font-extrabold text-slate-800 tracking-tight">Especialidades Médicas</h2>
                    <p class="text-xs text-slate-500 mt-0.5">Gestión de áreas y especialidades del gabinete ProDoctor.</p>
                </div>
                
                <button
                    @click="openCreateModal"
                    class="inline-flex items-center gap-2 px-4 py-2.5 bg-gradient-to-r from-fuchsia-600 to-indigo-650 hover:from-fuchsia-700 hover:to-indigo-755 text-white rounded-xl text-xs font-bold shadow-md shadow-fuchsia-500/20 active:scale-[0.98] transition cursor-pointer"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-4 h-4">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                    </svg>
                    Nueva Especialidad
                </button>
            </div>
        </template>

        <Head title="Especialidades - Admin ProDoctor" />

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
                        placeholder="Buscar por nombre o descripción..."
                        v-model="search"
                        class="block w-full pl-9 pr-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-slate-800 text-xs focus:outline-none focus:bg-white focus:border-fuchsia-500 focus:ring-4 focus:ring-fuchsia-500/10 transition"
                    />
                </div>
            </div>

            <!-- Tabla de Especialidades -->
            <div class="bg-white rounded-xl border-y border-r border-l-4 border-l-fuchsia-500 border-slate-200 overflow-hidden shadow-md">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-slate-50 border-b border-slate-200">
                                <th class="px-6 py-4 text-xs font-bold uppercase tracking-wider text-slate-400">ID</th>
                                <th class="px-6 py-4 text-xs font-bold uppercase tracking-wider text-slate-400">Nombre de la Especialidad</th>
                                <th class="px-6 py-4 text-xs font-bold uppercase tracking-wider text-slate-400">Descripción</th>
                                <th class="px-6 py-4 text-xs font-bold uppercase tracking-wider text-slate-400 text-right">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            <tr v-for="spec in filteredSpecialties" :key="spec.id_especialidad" class="hover:bg-slate-50/50 transition">
                                <td class="px-6 py-4 font-extrabold text-fuchsia-600 text-xs">
                                    #ESP-{{ String(spec.id_especialidad).padStart(3, '0') }}
                                </td>
                                <td class="px-6 py-4 font-bold text-slate-800 text-sm">
                                    {{ spec.nombre_especialidad }}
                                </td>
                                <td class="px-6 py-4 text-slate-655 text-xs font-medium max-w-sm truncate leading-relaxed">
                                    {{ spec.descripcion }}
                                </td>
                                <td class="px-6 py-4 text-right space-x-2">
                                    <button
                                        @click="openEditModal(spec)"
                                        class="inline-flex items-center px-3 py-1.5 bg-slate-50 hover:bg-slate-100 border border-slate-200 rounded-lg text-[11px] font-bold text-slate-700 transition cursor-pointer"
                                    >
                                        Editar
                                    </button>
                                    <button
                                        @click="deleteSpecialty(spec)"
                                        class="inline-flex items-center px-3 py-1.5 bg-rose-50 hover:bg-rose-100 border border-rose-200 rounded-lg text-[11px] font-bold text-rose-700 transition cursor-pointer"
                                    >
                                        Eliminar
                                    </button>
                                </td>
                            </tr>
                            <tr v-if="filteredSpecialties.length === 0">
                                <td colspan="4" class="px-6 py-12 text-center text-slate-400 font-medium">No se encontraron especialidades médicas.</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

        </div>

        <!-- MODAL: CREAR ESPECIALIDAD -->
        <div v-if="showCreateModal" class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity" @click="closeCreateModal"></div>
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
                
                <div class="relative inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full border border-slate-200">
                    <div class="p-6 border-b border-slate-150 flex items-center justify-between">
                        <div>
                            <h3 class="text-base font-extrabold text-slate-800">Nueva Especialidad Médica</h3>
                            <p class="text-xs text-slate-500 mt-0.5">Registra una nueva área clínica en el gabinete.</p>
                        </div>
                        <button @click="closeCreateModal" class="text-slate-455 hover:text-slate-600 transition cursor-pointer">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-5 h-5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                    <form @submit.prevent="submitCreate">
                        <div class="p-6 space-y-4">
                            <!-- Nombre Especialidad -->
                            <div class="space-y-1.5">
                                <label class="text-xs font-bold text-slate-550 uppercase tracking-wider block">NOMBRE DE ESPECIALIDAD</label>
                                <input
                                    type="text"
                                    placeholder="Ej. Dermatología, Ginecología..."
                                    v-model="createForm.nombre_especialidad"
                                    class="block w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-slate-800 text-xs focus:outline-none focus:bg-white focus:border-fuchsia-500 focus:ring-4 focus:ring-fuchsia-500/10 transition"
                                />
                                <div v-if="createForm.errors.nombre_especialidad" class="text-xs text-rose-600 font-semibold mt-1">{{ createForm.errors.nombre_especialidad }}</div>
                            </div>

                            <!-- Descripción -->
                            <div class="space-y-1.5">
                                <label class="text-xs font-bold text-slate-550 uppercase tracking-wider block">DESCRIPCIÓN</label>
                                <textarea
                                    rows="3"
                                    placeholder="Describe brevemente el área de atención..."
                                    v-model="createForm.descripcion"
                                    class="block w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-slate-800 text-xs focus:outline-none focus:bg-white focus:border-fuchsia-500 focus:ring-4 focus:ring-fuchsia-500/10 transition resize-none"
                                ></textarea>
                                <div v-if="createForm.errors.descripcion" class="text-xs text-rose-600 font-semibold mt-1">{{ createForm.errors.descripcion }}</div>
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
                                Registrar Especialidad
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- MODAL: EDITAR ESPECIALIDAD -->
        <div v-if="showEditModal && selectedSpecialty" class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity" @click="closeEditModal"></div>
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
                
                <div class="relative inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full border border-slate-200">
                    <div class="p-6 border-b border-slate-150 flex items-center justify-between">
                        <div>
                            <h3 class="text-base font-extrabold text-slate-800">Modificar Especialidad</h3>
                            <p class="text-xs text-slate-500 mt-0.5">Corrige la información de la especialidad.</p>
                        </div>
                        <button @click="closeEditModal" class="text-slate-455 hover:text-slate-655 transition cursor-pointer">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-5 h-5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                    <form @submit.prevent="submitEdit">
                        <div class="p-6 space-y-4">
                            <!-- Nombre Especialidad -->
                            <div class="space-y-1.5">
                                <label class="text-xs font-bold text-slate-550 uppercase tracking-wider block">NOMBRE DE ESPECIALIDAD</label>
                                <input
                                    type="text"
                                    v-model="editForm.nombre_especialidad"
                                    class="block w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-slate-800 text-xs focus:outline-none focus:bg-white focus:border-fuchsia-500 focus:ring-4 focus:ring-fuchsia-500/10 transition"
                                />
                                <div v-if="editForm.errors.nombre_especialidad" class="text-xs text-rose-600 font-semibold mt-1">{{ editForm.errors.nombre_especialidad }}</div>
                            </div>

                            <!-- Descripción -->
                            <div class="space-y-1.5">
                                <label class="text-xs font-bold text-slate-550 uppercase tracking-wider block">DESCRIPCIÓN</label>
                                <textarea
                                    rows="3"
                                    v-model="editForm.descripcion"
                                    class="block w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-slate-800 text-xs focus:outline-none focus:bg-white focus:border-fuchsia-500 focus:ring-4 focus:ring-fuchsia-500/10 transition resize-none"
                                ></textarea>
                                <div v-if="editForm.errors.descripcion" class="text-xs text-rose-600 font-semibold mt-1">{{ editForm.errors.descripcion }}</div>
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
