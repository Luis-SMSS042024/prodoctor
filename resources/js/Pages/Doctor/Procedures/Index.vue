<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, useForm, router } from '@inertiajs/vue3';
import { ref, computed } from 'vue';

const props = defineProps({
    procedures: {
        type: Array,
        required: true,
    },
    patients: {
        type: Array,
        required: true,
    },
});

// Search & Filter
const searchQuery = ref('');

// Computed stats
const stats = computed(() => {
    const total = props.procedures.length;
    const finalizados = props.procedures.filter(p => p.estado === 'Finalizado').length;
    const enProceso = props.procedures.filter(p => p.estado === 'En proceso').length;
    const pendientes = props.procedures.filter(p => p.estado === 'Pendiente').length;
    
    return { total, finalizados, enProceso, pendientes };
});

// Filtered procedures
const filteredProcedures = computed(() => {
    if (!searchQuery.value) return props.procedures;
    const q = searchQuery.value.toLowerCase();
    return props.procedures.filter(p => 
        p.nombre_procedimiento.toLowerCase().includes(q) ||
        p.paciente_nombre.toLowerCase().includes(q) ||
        p.descripcion.toLowerCase().includes(q)
    );
});

// Helper for status classes
const getStatusClass = (status) => {
    switch (status) {
        case 'Finalizado':
            return 'bg-emerald-50 text-emerald-700 border-emerald-200';
        case 'En proceso':
            return 'bg-blue-50 text-blue-700 border-blue-200';
        case 'Pendiente':
            return 'bg-orange-50 text-orange-700 border-orange-200';
        default:
            return 'bg-slate-50 text-slate-700 border-slate-200';
    }
};

const getStatusBorderClass = (status) => {
    switch (status) {
        case 'Finalizado':
            return 'border-l-4 border-l-emerald-500';
        case 'En proceso':
            return 'border-l-4 border-l-blue-500';
        case 'Pendiente':
            return 'border-l-4 border-l-orange-500';
        default:
            return 'border-l-4 border-l-slate-400';
    }
};

// Modals State
const showCreateModal = ref(false);
const showDetailsModal = ref(false);
const showEditModal = ref(false);
const selectedProcedure = ref(null);

// Forms
const createForm = useForm({
    id_paciente: '',
    nombre_procedimiento: '',
    descripcion: '',
    fecha_procedimiento: '',
    estado: 'Pendiente',
});

const editForm = useForm({
    nombre_procedimiento: '',
    descripcion: '',
    fecha_procedimiento: '',
    estado: 'Pendiente',
});

const statusForm = useForm({
    estado: '',
});

// Patient selection search
const patientSearch = ref('');
const isPatientDropdownOpen = ref(false);
const filteredPatients = computed(() => {
    if (!patientSearch.value) return props.patients;
    const term = patientSearch.value.toLowerCase();
    return props.patients.filter(p => p.nombre_completo.toLowerCase().includes(term));
});

const selectPatient = (patient) => {
    createForm.id_paciente = patient.id_paciente;
    patientSearch.value = patient.nombre_completo;
    isPatientDropdownOpen.value = false;
};

const closePatientDropdown = () => {
    setTimeout(() => {
        isPatientDropdownOpen.value = false;
    }, 200);
};

// Actions
const openCreateModal = () => {
    createForm.reset();
    patientSearch.value = '';
    createForm.fecha_procedimiento = new Date().toISOString().split('T')[0];
    showCreateModal.value = true;
};

const closeCreateModal = () => {
    showCreateModal.value = false;
};

const openDetailsModal = (proc) => {
    selectedProcedure.value = proc;
    showDetailsModal.value = true;
};

const closeDetailsModal = () => {
    showDetailsModal.value = false;
};

const openEditModal = () => {
    if (!selectedProcedure.value) return;
    
    editForm.nombre_procedimiento = selectedProcedure.value.nombre_procedimiento;
    editForm.descripcion = selectedProcedure.value.descripcion;
    editForm.fecha_procedimiento = selectedProcedure.value.fecha;
    editForm.estado = selectedProcedure.value.estado;
    
    showDetailsModal.value = false;
    showEditModal.value = true;
};

const closeEditModal = () => {
    showEditModal.value = false;
};

// Submits
const submitCreate = () => {
    createForm.post(route('doctor.procedures.store'), {
        onSuccess: () => {
            closeCreateModal();
            createForm.reset();
        }
    });
};

const submitEdit = () => {
    editForm.put(route('doctor.procedures.update', selectedProcedure.value.id_procedimiento), {
        onSuccess: () => {
            closeEditModal();
            selectedProcedure.value = null;
        }
    });
};

const updateStatus = (status) => {
    if (!selectedProcedure.value) return;
    
    statusForm.estado = status;
    statusForm.patch(route('doctor.procedures.status', selectedProcedure.value.id_procedimiento), {
        onSuccess: () => {
            closeDetailsModal();
            selectedProcedure.value = null;
        }
    });
};

const deleteProcedure = () => {
    if (!selectedProcedure.value) return;
    
    if (confirm('¿Está seguro de que desea eliminar permanentemente este procedimiento?')) {
        router.delete(route('doctor.procedures.destroy', selectedProcedure.value.id_procedimiento), {
            onSuccess: () => {
                closeDetailsModal();
                selectedProcedure.value = null;
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
                    <h2 class="text-xl font-extrabold text-slate-800 tracking-tight">Procedimientos Médicos</h2>
                    <p class="text-xs text-slate-500 mt-0.5">Gestión y registro de tratamientos asignados a pacientes.</p>
                </div>
                
                <button
                    @click="openCreateModal"
                    class="inline-flex items-center gap-2 px-4 py-2.5 bg-gradient-to-r from-blue-600 to-indigo-650 hover:from-blue-700 hover:to-indigo-755 text-white rounded-xl text-xs font-bold shadow-md shadow-blue-500/20 active:scale-[0.98] transition cursor-pointer"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-4 h-4">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                    </svg>
                    Nuevo Procedimiento
                </button>
            </div>
        </template>

        <Head title="Procedimientos Médicos - ProDoctor" />

        <div class="space-y-6 select-none">
            
            <!-- Cards de Estadísticas -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                <!-- Total -->
                <div class="bg-white rounded-xl border border-slate-200 p-5 flex items-center justify-between shadow-sm">
                    <div>
                        <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Total Procedimientos</span>
                        <span class="text-2xl font-extrabold text-slate-800 mt-1 block">{{ stats.total }}</span>
                    </div>
                    <div class="w-10 h-10 rounded-lg bg-slate-50 text-slate-500 flex items-center justify-center">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9.75 3.104v17.792m0-17.792" />
                        </svg>
                    </div>
                </div>
                <!-- Finalizados -->
                <div class="bg-white rounded-xl border border-slate-200 p-5 flex items-center justify-between shadow-sm border-l-4 border-l-emerald-500">
                    <div>
                        <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Finalizados</span>
                        <span class="text-2xl font-extrabold text-emerald-600 mt-1 block">{{ stats.finalizados }}</span>
                    </div>
                    <div class="w-10 h-10 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                        </svg>
                    </div>
                </div>
                <!-- En Proceso -->
                <div class="bg-white rounded-xl border border-slate-200 p-5 flex items-center justify-between shadow-sm border-l-4 border-l-blue-500">
                    <div>
                        <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">En Proceso</span>
                        <span class="text-2xl font-extrabold text-blue-600 mt-1 block">{{ stats.enProceso }}</span>
                    </div>
                    <div class="w-10 h-10 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992" />
                        </svg>
                    </div>
                </div>
                <!-- Pendientes -->
                <div class="bg-white rounded-xl border border-slate-200 p-5 flex items-center justify-between shadow-sm border-l-4 border-l-orange-500">
                    <div>
                        <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Pendientes</span>
                        <span class="text-2xl font-extrabold text-orange-600 mt-1 block">{{ stats.pendientes }}</span>
                    </div>
                    <div class="w-10 h-10 rounded-lg bg-orange-50 text-orange-600 flex items-center justify-center">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Filtro de Búsqueda -->
            <div class="bg-white rounded-xl border border-slate-200 p-4 shadow-sm flex items-center justify-between gap-4">
                <div class="relative w-full md:w-96">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.637 10.637z" />
                        </svg>
                    </div>
                    <input
                        type="text"
                        placeholder="Buscar por procedimiento, paciente o descripción..."
                        v-model="searchQuery"
                        class="block w-full pl-9 pr-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-slate-800 text-xs focus:outline-none focus:bg-white focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 transition"
                    />
                </div>
            </div>

            <!-- Tabla de Procedimientos -->
            <div class="bg-white rounded-xl border border-slate-200 overflow-hidden shadow-sm">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-slate-50 border-b border-slate-200">
                                <th class="px-6 py-4 text-xs font-bold uppercase tracking-wider text-slate-400">Paciente</th>
                                <th class="px-6 py-4 text-xs font-bold uppercase tracking-wider text-slate-400">Procedimiento</th>
                                <th class="px-6 py-4 text-xs font-bold uppercase tracking-wider text-slate-400">Fecha</th>
                                <th class="px-6 py-4 text-xs font-bold uppercase tracking-wider text-slate-400">Doctor Asignado</th>
                                <th class="px-6 py-4 text-xs font-bold uppercase tracking-wider text-slate-400">Estado</th>
                                <th class="px-6 py-4 text-xs font-bold uppercase tracking-wider text-slate-400 text-right">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            <tr
                                v-for="proc in filteredProcedures"
                                :key="proc.id_procedimiento"
                                class="hover:bg-slate-50/50 transition cursor-pointer"
                                @click="openDetailsModal(proc)"
                            >
                                <!-- Paciente -->
                                <td class="px-6 py-4 font-bold text-slate-800 text-sm">
                                    {{ proc.paciente_nombre }}
                                </td>
                                <!-- Procedimiento -->
                                <td class="px-6 py-4" :class="getStatusBorderClass(proc.estado)">
                                    <div class="pl-2">
                                        <h4 class="font-bold text-slate-800 text-sm">{{ proc.nombre_procedimiento }}</h4>
                                        <p class="text-xs text-slate-450 truncate max-w-md mt-0.5">{{ proc.descripcion }}</p>
                                    </div>
                                </td>
                                <!-- Fecha -->
                                <td class="px-6 py-4 text-sm font-semibold text-slate-500">
                                    {{ new Date(proc.fecha + 'T00:00:00').toLocaleDateString('es-ES', { day: 'numeric', month: 'short', year: 'numeric' }) }}
                                </td>
                                <!-- Doctor -->
                                <td class="px-6 py-4 text-xs font-semibold text-slate-600">
                                    {{ proc.doctor_nombre }}
                                </td>
                                <!-- Estado -->
                                <td class="px-6 py-4" @click.stop>
                                    <span class="px-2.5 py-0.5 rounded text-[10px] font-bold uppercase border" :class="getStatusClass(proc.estado)">
                                        {{ proc.estado }}
                                    </span>
                                </td>
                                <!-- Acciones -->
                                <td class="px-6 py-4 text-right space-x-2" @click.stop>
                                    <button
                                        @click="openDetailsModal(proc)"
                                        class="inline-flex items-center px-3 py-1.5 bg-slate-50 hover:bg-slate-100 border border-slate-200 rounded-lg text-[11px] font-bold text-slate-700 transition cursor-pointer"
                                    >
                                        Detalles
                                    </button>
                                </td>
                            </tr>
                            <tr v-if="filteredProcedures.length === 0">
                                <td colspan="6" class="px-6 py-12 text-center text-slate-400 font-medium">No se encontraron procedimientos registrados.</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

        </div>

        <!-- MODAL: DETALLES DE PROCEDIMIENTO -->
        <div v-if="showDetailsModal && selectedProcedure" class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity" @click="closeDetailsModal"></div>
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
                
                <div class="relative inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full border border-slate-200">
                    <div class="p-6 border-b border-slate-150 flex items-center justify-between">
                        <div>
                            <span class="text-[9px] font-extrabold uppercase text-slate-400 tracking-wider">PROCEDIMIENTO CLÍNICO #{{ String(selectedProcedure.id_procedimiento).padStart(4, '0') }}</span>
                            <h3 class="text-base font-extrabold text-slate-800 mt-0.5">{{ selectedProcedure.nombre_procedimiento }}</h3>
                        </div>
                        <span class="px-3 py-1 rounded-full text-xs font-bold border" :class="getStatusClass(selectedProcedure.estado)">
                            {{ selectedProcedure.estado }}
                        </span>
                    </div>

                    <div class="p-6 space-y-4">
                        <!-- Paciente & Doctor -->
                        <div class="grid grid-cols-2 gap-4">
                            <div class="bg-slate-50 border border-slate-150 rounded-xl p-3.5">
                                <span class="text-[10px] text-slate-400 font-bold uppercase tracking-wider block">PACIENTE</span>
                                <span class="text-xs font-bold text-slate-700 mt-1 block">{{ selectedProcedure.paciente_nombre }}</span>
                            </div>
                            <div class="bg-slate-50 border border-slate-150 rounded-xl p-3.5">
                                <span class="text-[10px] text-slate-400 font-bold uppercase tracking-wider block">RESPONSABLE</span>
                                <span class="text-xs font-bold text-slate-700 mt-1 block">{{ selectedProcedure.doctor_nombre }}</span>
                            </div>
                        </div>

                        <!-- Fecha -->
                        <div class="bg-slate-50 border border-slate-150 rounded-xl p-3.5">
                            <span class="text-[10px] text-slate-400 font-bold uppercase tracking-wider block">FECHA DE REALIZACIÓN</span>
                            <span class="text-xs font-bold text-slate-700 mt-1 block">
                                {{ new Date(selectedProcedure.fecha + 'T00:00:00').toLocaleDateString('es-ES', { weekday: 'long', day: 'numeric', month: 'long', year: 'numeric' }) }}
                            </span>
                        </div>

                        <!-- Descripción -->
                        <div class="space-y-1">
                            <span class="text-[10px] text-slate-400 font-bold uppercase tracking-wider block">DESCRIPCIÓN / TRATAMIENTO</span>
                            <div class="bg-slate-50 border border-slate-150 rounded-xl p-4 text-xs font-semibold text-slate-650 leading-relaxed whitespace-pre-line">
                                {{ selectedProcedure.descripcion }}
                            </div>
                        </div>
                    </div>

                    <div class="p-6 bg-slate-50 border-t border-slate-200 flex flex-col gap-3">
                        <div class="flex flex-wrap items-center gap-2">
                            <!-- En proceso -->
                            <button
                                v-if="selectedProcedure.estado === 'Pendiente'"
                                @click="updateStatus('En proceso')"
                                class="flex-1 min-w-[125px] inline-flex items-center justify-center gap-1.5 px-3 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-xl text-xs font-bold shadow-sm transition cursor-pointer"
                            >
                                Iniciar Proceso
                            </button>
                            <!-- Finalizar -->
                            <button
                                v-if="selectedProcedure.estado !== 'Finalizado'"
                                @click="updateStatus('Finalizado')"
                                class="flex-1 min-w-[125px] inline-flex items-center justify-center gap-1.5 px-3 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-xs font-bold shadow-sm transition cursor-pointer"
                            >
                                Finalizar
                            </button>
                            <!-- Pendiente -->
                            <button
                                v-if="selectedProcedure.estado === 'En proceso' || selectedProcedure.estado === 'Finalizado'"
                                @click="updateStatus('Pendiente')"
                                class="flex-1 min-w-[125px] inline-flex items-center justify-center gap-1.5 px-3 py-2 bg-orange-50 border border-orange-200 hover:bg-orange-100 text-orange-700 rounded-xl text-xs font-bold transition cursor-pointer"
                            >
                                Marcar Pendiente
                            </button>
                        </div>

                        <div class="flex items-center justify-between border-t border-slate-250 pt-3">
                            <button
                                @click="openEditModal"
                                class="inline-flex items-center gap-1.5 px-3 py-2 bg-white hover:bg-slate-100 border border-slate-200 rounded-xl text-xs font-bold text-slate-700 transition cursor-pointer"
                            >
                                Editar Detalles
                            </button>
                            <button
                                @click="deleteProcedure"
                                class="inline-flex items-center gap-1.5 px-3 py-2 text-rose-600 hover:bg-rose-50 rounded-xl text-xs font-bold transition cursor-pointer"
                            >
                                Eliminar Registro
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- MODAL: REGISTRAR PROCEDIMIENTO -->
        <div v-if="showCreateModal" class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity" @click="closeCreateModal"></div>
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
                
                <div class="relative inline-block align-bottom bg-white rounded-2xl text-left overflow-visible shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full border border-slate-200">
                    <div class="p-6 border-b border-slate-150 flex items-center justify-between">
                        <div>
                            <h3 class="text-base font-extrabold text-slate-800">Registrar Procedimiento</h3>
                            <p class="text-xs text-slate-500 mt-0.5">Asigna y describe un procedimiento para un paciente.</p>
                        </div>
                        <button @click="closeCreateModal" class="text-slate-455 hover:text-slate-600 transition cursor-pointer">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-5 h-5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                    <form @submit.prevent="submitCreate">
                        <div class="p-6 space-y-4">
                            <!-- Paciente Selector -->
                            <div class="space-y-1.5 relative">
                                <label class="text-xs font-bold text-slate-550 uppercase tracking-wider block">PACIENTE</label>
                                <div class="relative">
                                    <input
                                        type="text"
                                        placeholder="Buscar por nombre, apellido o DUI..."
                                        v-model="patientSearch"
                                        @focus="isPatientDropdownOpen = true"
                                        @blur="closePatientDropdown"
                                        class="block w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-slate-800 text-xs focus:outline-none focus:bg-white focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 transition"
                                    />
                                    <button
                                        v-if="patientSearch || createForm.id_paciente"
                                        type="button"
                                        @click="createForm.id_paciente = ''; patientSearch = '';"
                                        class="absolute inset-y-0 right-0 pr-3 flex items-center text-slate-450 hover:text-slate-650 cursor-pointer"
                                    >
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-4 h-4">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                                        </svg>
                                    </button>
                                </div>
                                <div v-if="createForm.errors.id_paciente" class="text-xs text-rose-600 font-semibold mt-1">{{ createForm.errors.id_paciente }}</div>
                                
                                <!-- Dropdown Resultados -->
                                <div
                                    v-if="isPatientDropdownOpen && filteredPatients.length > 0"
                                    class="absolute z-50 left-0 right-0 top-full mt-1 max-h-52 overflow-y-auto bg-white border border-slate-200 rounded-xl shadow-xl divide-y divide-slate-100"
                                >
                                    <div
                                        v-for="p in filteredPatients"
                                        :key="p.id_paciente"
                                        @click="selectPatient(p)"
                                        class="px-4 py-2.5 text-xs text-slate-700 hover:bg-blue-50 hover:text-blue-700 font-bold transition cursor-pointer"
                                    >
                                        {{ p.nombre_completo }}
                                    </div>
                                </div>
                            </div>

                            <!-- Nombre del Procedimiento -->
                            <div class="space-y-1.5">
                                <label class="text-xs font-bold text-slate-550 uppercase tracking-wider block">NOMBRE DEL PROCEDIMIENTO</label>
                                <input
                                    type="text"
                                    placeholder="Ej. Limpieza Dental, Sutura, Extracción..."
                                    v-model="createForm.nombre_procedimiento"
                                    class="block w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-slate-800 text-xs focus:outline-none focus:bg-white focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 transition"
                                />
                                <div v-if="createForm.errors.nombre_procedimiento" class="text-xs text-rose-600 font-semibold mt-1">{{ createForm.errors.nombre_procedimiento }}</div>
                            </div>

                            <!-- Fecha y Estado -->
                            <div class="grid grid-cols-2 gap-4">
                                <div class="space-y-1.5">
                                    <label class="text-xs font-bold text-slate-550 uppercase tracking-wider block">FECHA</label>
                                    <input
                                        type="date"
                                        v-model="createForm.fecha_procedimiento"
                                        class="block w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-slate-800 text-xs focus:outline-none focus:bg-white focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 transition"
                                    />
                                    <div v-if="createForm.errors.fecha_procedimiento" class="text-xs text-rose-600 font-semibold mt-1">{{ createForm.errors.fecha_procedimiento }}</div>
                                </div>
                                <div class="space-y-1.5">
                                    <label class="text-xs font-bold text-slate-550 uppercase tracking-wider block">ESTADO INICIAL</label>
                                    <select
                                        v-model="createForm.estado"
                                        class="block w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-slate-800 text-xs focus:outline-none focus:bg-white focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 transition"
                                    >
                                        <option value="Pendiente">Pendiente</option>
                                        <option value="En proceso">En proceso</option>
                                        <option value="Finalizado">Finalizado</option>
                                    </select>
                                    <div v-if="createForm.errors.estado" class="text-xs text-rose-600 font-semibold mt-1">{{ createForm.errors.estado }}</div>
                                </div>
                            </div>

                            <!-- Descripción -->
                            <div class="space-y-1.5">
                                <label class="text-xs font-bold text-slate-550 uppercase tracking-wider block">DESCRIPCIÓN / TRATAMIENTO</label>
                                <textarea
                                    rows="4"
                                    placeholder="Describe los detalles del procedimiento médico..."
                                    v-model="createForm.descripcion"
                                    class="block w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-slate-800 text-xs focus:outline-none focus:bg-white focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 transition resize-none"
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
                                :disabled="createForm.processing || !createForm.id_paciente"
                                class="px-5 py-2.5 bg-gradient-to-r from-blue-600 to-indigo-650 hover:from-blue-700 hover:to-indigo-755 text-white text-xs font-bold rounded-xl shadow-md active:scale-[0.98] transition cursor-pointer disabled:opacity-50"
                            >
                                Guardar Procedimiento
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- MODAL: EDITAR PROCEDIMIENTO -->
        <div v-if="showEditModal && selectedProcedure" class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity" @click="closeEditModal"></div>
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
                
                <div class="relative inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full border border-slate-200">
                    <div class="p-6 border-b border-slate-150 flex items-center justify-between">
                        <div>
                            <h3 class="text-base font-extrabold text-slate-800">Modificar Procedimiento</h3>
                            <p class="text-xs text-slate-500 mt-0.5">Edita los detalles del procedimiento médico.</p>
                        </div>
                        <button @click="closeEditModal" class="text-slate-455 hover:text-slate-600 transition cursor-pointer">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-5 h-5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                    <form @submit.prevent="submitEdit">
                        <div class="p-6 space-y-4">
                            <!-- Info Paciente (Solo Lectura) -->
                            <div class="bg-slate-50 rounded-xl p-3.5 border border-slate-150">
                                <span class="text-[10px] text-slate-400 font-bold uppercase tracking-wider block">PACIENTE</span>
                                <h4 class="font-bold text-slate-800 text-sm mt-0.5">{{ selectedProcedure.paciente_nombre }}</h4>
                            </div>

                            <!-- Nombre del Procedimiento -->
                            <div class="space-y-1.5">
                                <label class="text-xs font-bold text-slate-550 uppercase tracking-wider block">NOMBRE DEL PROCEDIMIENTO</label>
                                <input
                                    type="text"
                                    v-model="editForm.nombre_procedimiento"
                                    class="block w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-slate-800 text-xs focus:outline-none focus:bg-white focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 transition"
                                />
                                <div v-if="editForm.errors.nombre_procedimiento" class="text-xs text-rose-600 font-semibold mt-1">{{ editForm.errors.nombre_procedimiento }}</div>
                            </div>

                            <!-- Fecha y Estado -->
                            <div class="grid grid-cols-2 gap-4">
                                <div class="space-y-1.5">
                                    <label class="text-xs font-bold text-slate-550 uppercase tracking-wider block">FECHA</label>
                                    <input
                                        type="date"
                                        v-model="editForm.fecha_procedimiento"
                                        class="block w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-slate-800 text-xs focus:outline-none focus:bg-white focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 transition"
                                    />
                                    <div v-if="editForm.errors.fecha_procedimiento" class="text-xs text-rose-600 font-semibold mt-1">{{ editForm.errors.fecha_procedimiento }}</div>
                                </div>
                                <div class="space-y-1.5">
                                    <label class="text-xs font-bold text-slate-550 uppercase tracking-wider block">ESTADO</label>
                                    <select
                                        v-model="editForm.estado"
                                        class="block w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-slate-800 text-xs focus:outline-none focus:bg-white focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 transition"
                                    >
                                        <option value="Pendiente">Pendiente</option>
                                        <option value="En proceso">En proceso</option>
                                        <option value="Finalizado">Finalizado</option>
                                    </select>
                                    <div v-if="editForm.errors.estado" class="text-xs text-rose-600 font-semibold mt-1">{{ editForm.errors.estado }}</div>
                                </div>
                            </div>

                            <!-- Descripción -->
                            <div class="space-y-1.5">
                                <label class="text-xs font-bold text-slate-550 uppercase tracking-wider block">DESCRIPCIÓN / TRATAMIENTO</label>
                                <textarea
                                    rows="4"
                                    v-model="editForm.descripcion"
                                    class="block w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-slate-800 text-xs focus:outline-none focus:bg-white focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 transition resize-none"
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
                                class="px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold rounded-xl shadow-md active:scale-[0.98] transition cursor-pointer"
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
