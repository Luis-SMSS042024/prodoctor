<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, useForm, router } from '@inertiajs/vue3';
import { ref, computed } from 'vue';

const props = defineProps({
    followups: {
        type: Array,
        required: true,
    },
    appointments: {
        type: Array,
        required: true,
    },
});

// Search and Filter
const searchQuery = ref('');

const filteredFollowups = computed(() => {
    if (!searchQuery.value) return props.followups;
    const q = searchQuery.value.toLowerCase();
    return props.followups.filter(f => 
        f.paciente_nombre.toLowerCase().includes(q) ||
        f.observaciones.toLowerCase().includes(q) ||
        f.tratamiento.toLowerCase().includes(q) ||
        f.motivo_cita.toLowerCase().includes(q)
    );
});

// Modals State
const showCreateModal = ref(false);
const showDetailsModal = ref(false);
const showEditModal = ref(false);
const selectedFollowup = ref(null);

// Forms
const createForm = useForm({
    id_cita: '',
    observaciones: '',
    tratamiento: '',
    fecha_seguimiento: '',
    proxima_revision: '',
});

const editForm = useForm({
    observaciones: '',
    tratamiento: '',
    fecha_seguimiento: '',
    proxima_revision: '',
});

// Appointment Autocomplete
const appointmentSearch = ref('');
const isAppointmentDropdownOpen = ref(false);
const filteredAppointments = computed(() => {
    if (!appointmentSearch.value) return props.appointments;
    const term = appointmentSearch.value.toLowerCase();
    return props.appointments.filter(a => a.label.toLowerCase().includes(term));
});

const selectAppointment = (app) => {
    createForm.id_cita = app.id_cita;
    appointmentSearch.value = app.label;
    isAppointmentDropdownOpen.value = false;
};

const closeAppointmentDropdown = () => {
    setTimeout(() => {
        isAppointmentDropdownOpen.value = false;
    }, 200);
};

// Actions
const openCreateModal = () => {
    createForm.reset();
    appointmentSearch.value = '';
    createForm.fecha_seguimiento = new Date().toISOString().split('T')[0];
    showCreateModal.value = true;
};

const closeCreateModal = () => {
    showCreateModal.value = false;
};

const openDetailsModal = (follow) => {
    selectedFollowup.value = follow;
    showDetailsModal.value = true;
};

const closeDetailsModal = () => {
    showDetailsModal.value = false;
};

const openEditModal = () => {
    if (!selectedFollowup.value) return;
    
    editForm.observaciones = selectedFollowup.value.observaciones;
    editForm.tratamiento = selectedFollowup.value.tratamiento;
    editForm.fecha_seguimiento = selectedFollowup.value.fecha_seguimiento;
    editForm.proxima_revision = selectedFollowup.value.proxima_revision || '';
    
    showDetailsModal.value = false;
    showEditModal.value = true;
};

const closeEditModal = () => {
    showEditModal.value = false;
};

// Form Submits
const submitCreate = () => {
    createForm.post(route('doctor.followups.store'), {
        onSuccess: () => {
            closeCreateModal();
            createForm.reset();
        }
    });
};

const submitEdit = () => {
    editForm.put(route('doctor.followups.update', selectedFollowup.value.id_seguimiento), {
        onSuccess: () => {
            closeEditModal();
            selectedFollowup.value = null;
        }
    });
};

const deleteFollowup = () => {
    if (!selectedFollowup.value) return;
    
    if (confirm('¿Está seguro de que desea eliminar este seguimiento clínico?')) {
        router.delete(route('doctor.followups.destroy', selectedFollowup.value.id_seguimiento), {
            onSuccess: () => {
                closeDetailsModal();
                selectedFollowup.value = null;
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
                    <h2 class="text-xl font-extrabold text-slate-800 tracking-tight">Seguimiento Clínico</h2>
                    <p class="text-xs text-slate-500 mt-0.5">Control evolutivo, diagnósticos, observaciones y recetas médicas.</p>
                </div>
                
                <button
                    @click="openCreateModal"
                    class="inline-flex items-center gap-2 px-4 py-2.5 bg-gradient-to-r from-blue-600 to-indigo-650 hover:from-blue-700 hover:to-indigo-755 text-white rounded-xl text-xs font-bold shadow-md shadow-blue-500/20 active:scale-[0.98] transition cursor-pointer"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-4 h-4">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                    </svg>
                    Registrar Evolución
                </button>
            </div>
        </template>

        <Head title="Seguimiento Clínico - ProDoctor" />

        <div class="space-y-6 select-none">
            
            <!-- Buscador -->
            <div class="bg-white rounded-xl border border-slate-200 p-4 shadow-sm flex items-center justify-between gap-4">
                <div class="relative w-full md:w-96">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.637 10.637z" />
                        </svg>
                    </div>
                    <input
                        type="text"
                        placeholder="Buscar por paciente, motivo, observaciones o tratamiento..."
                        v-model="searchQuery"
                        class="block w-full pl-9 pr-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-slate-800 text-xs focus:outline-none focus:bg-white focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 transition"
                    />
                </div>
            </div>

            <!-- Evoluciones Clínicas (Timeline UI) -->
            <div class="space-y-4">
                <div
                    v-for="follow in filteredFollowups"
                    :key="follow.id_seguimiento"
                    @click="openDetailsModal(follow)"
                    class="bg-white rounded-xl border border-slate-200 p-6 shadow-sm hover:shadow-md transition cursor-pointer flex flex-col md:flex-row md:items-start gap-6 border-l-4 border-l-amber-500"
                >
                    <!-- Izquierda: Fechas -->
                    <div class="w-full md:w-44 shrink-0 space-y-1">
                        <div class="text-xs font-bold text-slate-400 uppercase tracking-wide">Fecha Seguimiento</div>
                        <div class="text-sm font-extrabold text-slate-800">
                            {{ new Date(follow.fecha_seguimiento + 'T00:00:00').toLocaleDateString('es-ES', { day: 'numeric', month: 'long', year: 'numeric' }) }}
                        </div>
                        
                        <div v-if="follow.proxima_revision" class="pt-2">
                            <span class="text-[9px] font-bold text-blue-600 bg-blue-50 border border-blue-150 px-2 py-0.5 rounded block w-max">
                                Prox. Revisión: {{ new Date(follow.proxima_revision + 'T00:00:00').toLocaleDateString('es-ES', { day: 'numeric', month: 'short' }) }}
                            </span>
                        </div>
                    </div>

                    <!-- Centro: Diagnóstico, observaciones, tratamiento -->
                    <div class="flex-1 space-y-3">
                        <div>
                            <span class="text-[9px] font-extrabold uppercase text-slate-400 block tracking-wider">Paciente</span>
                            <h4 class="font-extrabold text-slate-850 text-sm mt-0.5">{{ follow.paciente_nombre }}</h4>
                            <span class="text-[10px] text-slate-400 font-semibold block mt-0.5">Motivo Consulta: {{ follow.motivo_cita }}</span>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 pt-2 border-t border-slate-100">
                            <div>
                                <span class="text-[9px] font-bold text-slate-400 uppercase tracking-wider block">Observaciones Clínicas</span>
                                <p class="text-xs font-semibold text-slate-600 mt-1 line-clamp-3 leading-relaxed">
                                    {{ follow.observaciones }}
                                </p>
                            </div>
                            <div>
                                <span class="text-[9px] font-bold text-slate-400 uppercase tracking-wider block">Receta y Tratamiento</span>
                                <p class="text-xs font-semibold text-slate-600 mt-1 line-clamp-3 leading-relaxed">
                                    {{ follow.tratamiento }}
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Derecha: Doctor y Acciones -->
                    <div class="w-full md:w-auto flex flex-row md:flex-col items-center justify-between md:justify-start gap-4 shrink-0">
                        <div class="text-right">
                            <span class="text-[9px] font-bold text-slate-400 uppercase tracking-wider block">Médico</span>
                            <span class="text-xs font-bold text-slate-700 block mt-0.5">{{ follow.doctor_nombre }}</span>
                        </div>
                        <button
                            @click.stop="openDetailsModal(follow)"
                            class="px-3 py-1.5 bg-slate-50 hover:bg-slate-100 border border-slate-200 rounded-lg text-xs font-bold text-slate-700 transition cursor-pointer"
                        >
                            Ver Todo
                        </button>
                    </div>
                </div>

                <div v-if="filteredFollowups.length === 0" class="bg-white rounded-xl border border-slate-200 p-12 text-center text-slate-400 font-medium">
                    No se encontraron registros de seguimiento clínico.
                </div>
            </div>

        </div>

        <!-- MODAL: DETALLES DE SEGUIMIENTO -->
        <div v-if="showDetailsModal && selectedFollowup" class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity" @click="closeDetailsModal"></div>
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
                
                <div class="relative inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full border border-slate-200">
                    <div class="p-6 border-b border-slate-150 flex items-center justify-between">
                        <div>
                            <span class="text-[9px] font-extrabold uppercase text-slate-400 tracking-wider">HISTORIAL CLÍNICO #{{ String(selectedFollowup.id_seguimiento).padStart(4, '0') }}</span>
                            <h3 class="text-base font-extrabold text-slate-800 mt-0.5">Seguimiento de Consulta</h3>
                        </div>
                        <button @click="closeDetailsModal" class="text-slate-455 hover:text-slate-655 transition cursor-pointer">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-5 h-5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                    <div class="p-6 space-y-4">
                        <!-- Paciente -->
                        <div class="bg-slate-50 border border-slate-150 rounded-xl p-4 flex items-center gap-3">
                            <div class="w-9 h-9 rounded-full bg-gradient-to-tr from-blue-500/10 to-indigo-500/10 border border-blue-200 flex items-center justify-center font-bold text-xs text-blue-700">
                                {{ selectedFollowup.paciente_nombre.split(' ').map(n => n[0]).join('').substring(0, 2).toUpperCase() }}
                            </div>
                            <div>
                                <span class="text-[9px] text-slate-400 font-bold uppercase tracking-wider block">PACIENTE</span>
                                <span class="text-xs font-bold text-slate-800 block mt-0.5">{{ selectedFollowup.paciente_nombre }}</span>
                            </div>
                        </div>

                        <!-- Cita de Referencia -->
                        <div class="bg-slate-50 border border-slate-150 rounded-xl p-3.5">
                            <span class="text-[9px] text-slate-400 font-bold uppercase tracking-wider block">CITA DE REFERENCIA</span>
                            <span class="text-xs font-bold text-slate-700 block mt-1">
                                Consulta el {{ new Date(selectedFollowup.fecha_cita + 'T00:00:00').toLocaleDateString('es-ES', { day: 'numeric', month: 'long', year: 'numeric' }) }} - {{ selectedFollowup.hora_cita }} hrs
                            </span>
                            <p class="text-[11px] text-slate-450 mt-1 italic">Motivo: {{ selectedFollowup.motivo_cita }}</p>
                        </div>

                        <!-- Observaciones -->
                        <div class="space-y-1">
                            <span class="text-[10px] text-slate-400 font-bold uppercase tracking-wider block">OBSERVACIONES MÉDICAS</span>
                            <div class="bg-slate-50 border border-slate-150 rounded-xl p-4 text-xs font-semibold text-slate-650 leading-relaxed whitespace-pre-line">
                                {{ selectedFollowup.observaciones }}
                            </div>
                        </div>

                        <!-- Tratamiento -->
                        <div class="space-y-1">
                            <span class="text-[10px] text-slate-400 font-bold uppercase tracking-wider block">RECETAS Y TRATAMIENTO</span>
                            <div class="bg-slate-50 border border-slate-150 rounded-xl p-4 text-xs font-semibold text-slate-650 leading-relaxed whitespace-pre-line">
                                {{ selectedFollowup.tratamiento }}
                            </div>
                        </div>

                        <!-- Fechas de Control -->
                        <div class="grid grid-cols-2 gap-4 pt-2">
                            <div class="bg-slate-50 border border-slate-150 rounded-xl p-3">
                                <span class="text-[9px] text-slate-400 font-bold uppercase tracking-wider block">FECHA SEGUIMIENTO</span>
                                <span class="text-xs font-bold text-slate-700 mt-1 block">
                                    {{ new Date(selectedFollowup.fecha_seguimiento + 'T00:00:00').toLocaleDateString('es-ES', { day: 'numeric', month: 'short', year: 'numeric' }) }}
                                </span>
                            </div>
                            <div class="bg-slate-50 border border-slate-150 rounded-xl p-3">
                                <span class="text-[9px] text-slate-400 font-bold uppercase tracking-wider block">PRÓXIMA REVISIÓN</span>
                                <span class="text-xs font-bold text-slate-700 mt-1 block">
                                    {{ selectedFollowup.proxima_revision ? new Date(selectedFollowup.proxima_revision + 'T00:00:00').toLocaleDateString('es-ES', { day: 'numeric', month: 'short', year: 'numeric' }) : 'No requerida' }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <div class="p-6 bg-slate-50 border-t border-slate-200 flex items-center justify-between">
                        <button
                            @click="openEditModal"
                            class="inline-flex items-center gap-1.5 px-3 py-2 bg-white hover:bg-slate-100 border border-slate-200 rounded-xl text-xs font-bold text-slate-700 transition cursor-pointer"
                        >
                            Editar
                        </button>
                        
                        <div class="space-x-2">
                            <button
                                @click="deleteFollowup"
                                class="inline-flex items-center gap-1.5 px-3 py-2 text-rose-600 hover:bg-rose-50 rounded-xl text-xs font-bold transition cursor-pointer"
                            >
                                Eliminar
                            </button>
                            <button
                                type="button"
                                @click="closeDetailsModal"
                                class="px-4 py-2 bg-white border border-slate-200 hover:bg-slate-50 text-slate-655 text-xs font-bold rounded-xl transition cursor-pointer"
                            >
                                Cerrar
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- MODAL: REGISTRAR EVOLUCIÓN (CREATE) -->
        <div v-if="showCreateModal" class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity" @click="closeCreateModal"></div>
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
                
                <div class="relative inline-block align-bottom bg-white rounded-2xl text-left overflow-visible shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full border border-slate-200">
                    <div class="p-6 border-b border-slate-150 flex items-center justify-between">
                        <div>
                            <h3 class="text-base font-extrabold text-slate-800">Registrar Evolución Clínica</h3>
                            <p class="text-xs text-slate-500 mt-0.5">Asigna observaciones y recetas vinculadas a una cita programada.</p>
                        </div>
                        <button @click="closeCreateModal" class="text-slate-455 hover:text-slate-600 transition cursor-pointer">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-5 h-5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                    <form @submit.prevent="submitCreate">
                        <div class="p-6 space-y-4">
                            
                            <!-- Cita Selector -->
                            <div class="space-y-1.5 relative">
                                <label class="text-xs font-bold text-slate-550 uppercase tracking-wider block">SELECCIONAR CITA</label>
                                <div class="relative">
                                    <input
                                        type="text"
                                        placeholder="Buscar cita por paciente o motivo..."
                                        v-model="appointmentSearch"
                                        @focus="isAppointmentDropdownOpen = true"
                                        @blur="closeAppointmentDropdown"
                                        class="block w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-slate-800 text-xs focus:outline-none focus:bg-white focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 transition"
                                    />
                                    <button
                                        v-if="appointmentSearch || createForm.id_cita"
                                        type="button"
                                        @click="createForm.id_cita = ''; appointmentSearch = '';"
                                        class="absolute inset-y-0 right-0 pr-3 flex items-center text-slate-450 hover:text-slate-655 cursor-pointer"
                                    >
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-4 h-4">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                                        </svg>
                                    </button>
                                </div>
                                <div v-if="createForm.errors.id_cita" class="text-xs text-rose-600 font-semibold mt-1">{{ createForm.errors.id_cita }}</div>
                                
                                <!-- Dropdown Resultados -->
                                <div
                                    v-if="isAppointmentDropdownOpen && filteredAppointments.length > 0"
                                    class="absolute z-50 left-0 right-0 top-full mt-1 max-h-48 overflow-y-auto bg-white border border-slate-200 rounded-xl shadow-xl divide-y divide-slate-100"
                                >
                                    <div
                                        v-for="a in filteredAppointments"
                                        :key="a.id_cita"
                                        @click="selectAppointment(a)"
                                        class="px-4 py-2 text-xs text-slate-700 hover:bg-blue-50 hover:text-blue-700 font-bold transition cursor-pointer"
                                    >
                                        {{ a.label }}
                                    </div>
                                </div>
                            </div>

                            <!-- Fechas -->
                            <div class="grid grid-cols-2 gap-4">
                                <div class="space-y-1.5">
                                    <label class="text-xs font-bold text-slate-550 uppercase tracking-wider block">FECHA EVOLUCIÓN</label>
                                    <input
                                        type="date"
                                        v-model="createForm.fecha_seguimiento"
                                        class="block w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-slate-800 text-xs focus:outline-none focus:bg-white focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 transition"
                                    />
                                    <div v-if="createForm.errors.fecha_seguimiento" class="text-xs text-rose-600 font-semibold mt-1">{{ createForm.errors.fecha_seguimiento }}</div>
                                </div>
                                <div class="space-y-1.5">
                                    <label class="text-xs font-bold text-slate-550 uppercase tracking-wider block">PRÓXIMA REVISIÓN (OPCIONAL)</label>
                                    <input
                                        type="date"
                                        v-model="createForm.proxima_revision"
                                        class="block w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-slate-800 text-xs focus:outline-none focus:bg-white focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 transition"
                                    />
                                    <div v-if="createForm.errors.proxima_revision" class="text-xs text-rose-600 font-semibold mt-1">{{ createForm.errors.proxima_revision }}</div>
                                </div>
                            </div>

                            <!-- Observaciones -->
                            <div class="space-y-1.5">
                                <label class="text-xs font-bold text-slate-550 uppercase tracking-wider block">OBSERVACIONES MÉDICAS</label>
                                <textarea
                                    rows="3"
                                    placeholder="Detalles sobre el estado del paciente, diagnóstico..."
                                    v-model="createForm.observaciones"
                                    class="block w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-slate-800 text-xs focus:outline-none focus:bg-white focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 transition resize-none"
                                ></textarea>
                                <div v-if="createForm.errors.observaciones" class="text-xs text-rose-600 font-semibold mt-1">{{ createForm.errors.observaciones }}</div>
                            </div>

                            <!-- Tratamiento -->
                            <div class="space-y-1.5">
                                <label class="text-xs font-bold text-slate-550 uppercase tracking-wider block">RECETAS Y TRATAMIENTO</label>
                                <textarea
                                    rows="3"
                                    placeholder="Medicamentos recetados, indicaciones de dosis y cuidados..."
                                    v-model="createForm.tratamiento"
                                    class="block w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-slate-800 text-xs focus:outline-none focus:bg-white focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 transition resize-none"
                                ></textarea>
                                <div v-if="createForm.errors.tratamiento" class="text-xs text-rose-600 font-semibold mt-1">{{ createForm.errors.tratamiento }}</div>
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
                                :disabled="createForm.processing || !createForm.id_cita"
                                class="px-5 py-2.5 bg-gradient-to-r from-blue-600 to-indigo-650 hover:from-blue-700 hover:to-indigo-755 text-white text-xs font-bold rounded-xl shadow-md active:scale-[0.98] transition cursor-pointer disabled:opacity-50"
                            >
                                Guardar Seguimiento
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- MODAL: EDITAR SEGUIMIENTO -->
        <div v-if="showEditModal && selectedFollowup" class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity" @click="closeEditModal"></div>
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
                
                <div class="relative inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full border border-slate-200">
                    <div class="p-6 border-b border-slate-150 flex items-center justify-between">
                        <div>
                            <h3 class="text-base font-extrabold text-slate-800">Modificar Evolución Clínica</h3>
                            <p class="text-xs text-slate-500 mt-0.5">Corrige las anotaciones o recetas registradas.</p>
                        </div>
                        <button @click="closeEditModal" class="text-slate-455 hover:text-slate-655 transition cursor-pointer">
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
                                <h4 class="font-bold text-slate-800 text-sm mt-0.5">{{ selectedFollowup.paciente_nombre }}</h4>
                                <span class="text-[9px] text-slate-450 block mt-0.5">Consulta: {{ selectedFollowup.fecha_cita }} ({{ selectedFollowup.motivo_cita }})</span>
                            </div>

                            <!-- Fechas -->
                            <div class="grid grid-cols-2 gap-4">
                                <div class="space-y-1.5">
                                    <label class="text-xs font-bold text-slate-550 uppercase tracking-wider block">FECHA EVOLUCIÓN</label>
                                    <input
                                        type="date"
                                        v-model="editForm.fecha_seguimiento"
                                        class="block w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-slate-800 text-xs focus:outline-none focus:bg-white focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 transition"
                                    />
                                    <div v-if="editForm.errors.fecha_seguimiento" class="text-xs text-rose-600 font-semibold mt-1">{{ editForm.errors.fecha_seguimiento }}</div>
                                </div>
                                <div class="space-y-1.5">
                                    <label class="text-xs font-bold text-slate-550 uppercase tracking-wider block">PRÓXIMA REVISIÓN (OPCIONAL)</label>
                                    <input
                                        type="date"
                                        v-model="editForm.proxima_revision"
                                        class="block w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-slate-800 text-xs focus:outline-none focus:bg-white focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 transition"
                                    />
                                    <div v-if="editForm.errors.proxima_revision" class="text-xs text-rose-600 font-semibold mt-1">{{ editForm.errors.proxima_revision }}</div>
                                </div>
                            </div>

                            <!-- Observaciones -->
                            <div class="space-y-1.5">
                                <label class="text-xs font-bold text-slate-550 uppercase tracking-wider block">OBSERVACIONES MÉDICAS</label>
                                <textarea
                                    rows="3"
                                    v-model="editForm.observaciones"
                                    class="block w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-slate-800 text-xs focus:outline-none focus:bg-white focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 transition resize-none"
                                ></textarea>
                                <div v-if="editForm.errors.observaciones" class="text-xs text-rose-600 font-semibold mt-1">{{ editForm.errors.observaciones }}</div>
                            </div>

                            <!-- Tratamiento -->
                            <div class="space-y-1.5">
                                <label class="text-xs font-bold text-slate-550 uppercase tracking-wider block">RECETAS Y TRATAMIENTO</label>
                                <textarea
                                    rows="3"
                                    v-model="editForm.tratamiento"
                                    class="block w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-slate-800 text-xs focus:outline-none focus:bg-white focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 transition resize-none"
                                ></textarea>
                                <div v-if="editForm.errors.tratamiento" class="text-xs text-rose-600 font-semibold mt-1">{{ editForm.errors.tratamiento }}</div>
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
