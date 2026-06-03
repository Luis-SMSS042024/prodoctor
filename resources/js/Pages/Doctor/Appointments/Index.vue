<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, useForm, router } from '@inertiajs/vue3';
import { ref, computed, watch } from 'vue';

const props = defineProps({
    appointments: {
        type: Array,
        required: true,
    },
    patients: {
        type: Array,
        required: true,
    },
});

// View Toggle: 'week' or 'month'
const selectedView = ref('week');

// Date Tracking
const currentWeekStart = ref(getStartOfWeek(new Date()));
const currentMonthDate = ref(new Date(new Date().getFullYear(), new Date().getMonth(), 1));

// Calendar helpers
const monthNames = [
    'Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio',
    'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'
];
const dayNamesShort = ['Lun', 'Mar', 'Mié', 'Jue', 'Vie', 'Sáb', 'Dom'];

function getStartOfWeek(date) {
    const d = new Date(date);
    const day = d.getDay();
    // Adjust when day is Sunday (0) to get Monday (1)
    const diff = d.getDate() - day + (day === 0 ? -6 : 1);
    const start = new Date(d.setDate(diff));
    start.setHours(0, 0, 0, 0);
    return start;
}

// Generate 7 days for the weekly view
const weekDays = computed(() => {
    const days = [];
    const start = new Date(currentWeekStart.value);
    for (let i = 0; i < 7; i++) {
        const d = new Date(start);
        d.setDate(start.getDate() + i);
        days.push(d);
    }
    return days;
});

// Format date to YYYY-MM-DD
const formatDateString = (date) => {
    const y = date.getFullYear();
    const m = String(date.getMonth() + 1).padStart(2, '0');
    const d = String(date.getDate()).padStart(2, '0');
    return `${y}-${m}-${d}`;
};

// Check if a date is "Today"
const isToday = (date) => {
    const today = new Date();
    return date.getDate() === today.getDate() &&
        date.getMonth() === today.getMonth() &&
        date.getFullYear() === today.getFullYear();
};

// Format date in Spanish (e.g. "Lun 12")
const formatDayHeader = (date) => {
    const options = { weekday: 'short', day: 'numeric' };
    const str = date.toLocaleDateString('es-ES', options);
    // Capitalize first letter
    return str.charAt(0).toUpperCase() + str.slice(1);
};

// Generate month calendar grid (42 days)
const monthDays = computed(() => {
    const year = currentMonthDate.value.getFullYear();
    const month = currentMonthDate.value.getMonth();
    
    // First day of current month
    const firstDayOfMonth = new Date(year, month, 1);
    // Day of the week for first day (0 = Sun, 1 = Mon, ..., 6 = Sat)
    let startDayOfWeek = firstDayOfMonth.getDay();
    // Adjust so week starts on Monday
    let paddingDays = startDayOfWeek === 0 ? 6 : startDayOfWeek - 1;
    
    // Last day of current month
    const lastDayOfMonth = new Date(year, month + 1, 0);
    const totalDaysInMonth = lastDayOfMonth.getDate();
    
    const days = [];
    
    // Previous month padding
    const prevMonthLastDay = new Date(year, month, 0).getDate();
    for (let i = paddingDays - 1; i >= 0; i--) {
        days.push({
            date: new Date(year, month - 1, prevMonthLastDay - i),
            isCurrentMonth: false,
        });
    }
    
    // Current month days
    for (let i = 1; i <= totalDaysInMonth; i++) {
        days.push({
            date: new Date(year, month, i),
            isCurrentMonth: true,
        });
    }
    
    // Next month padding (make standard 42 days grid)
    const remainingCells = 42 - days.length;
    for (let i = 1; i <= remainingCells; i++) {
        days.push({
            date: new Date(year, month + 1, i),
            isCurrentMonth: false,
        });
    }
    
    return days;
});

// Week View Header Text
const weekHeaderLabel = computed(() => {
    const days = weekDays.value;
    const start = days[0];
    const end = days[6];
    
    if (start.getFullYear() !== end.getFullYear()) {
        return `${start.getDate()} ${monthNames[start.getMonth()].substring(0, 3)} ${start.getFullYear()} - ${end.getDate()} ${monthNames[end.getMonth()].substring(0, 3)} ${end.getFullYear()}`;
    }
    if (start.getMonth() !== end.getMonth()) {
        return `${start.getDate()} ${monthNames[start.getMonth()].substring(0, 3)} - ${end.getDate()} ${monthNames[end.getMonth()].substring(0, 3)}, ${start.getFullYear()}`;
    }
    return `${start.getDate()} - ${end.getDate()} de ${monthNames[start.getMonth()]}, ${start.getFullYear()}`;
});

// Month View Header Text
const monthHeaderLabel = computed(() => {
    return `${monthNames[currentMonthDate.value.getMonth()]} ${currentMonthDate.value.getFullYear()}`;
});

// Filter appointments by date
const getAppointmentsForDate = (date) => {
    const formatted = formatDateString(date);
    return props.appointments.filter(a => a.fecha === formatted);
};

// Navigation Controllers
const navigatePrev = () => {
    if (selectedView.value === 'week') {
        const d = new Date(currentWeekStart.value);
        d.setDate(d.getDate() - 7);
        currentWeekStart.value = d;
    } else {
        const d = new Date(currentMonthDate.value);
        d.setMonth(d.getMonth() - 1);
        currentMonthDate.value = d;
    }
};

const navigateNext = () => {
    if (selectedView.value === 'week') {
        const d = new Date(currentWeekStart.value);
        d.setDate(d.getDate() + 7);
        currentWeekStart.value = d;
    } else {
        const d = new Date(currentMonthDate.value);
        d.setMonth(d.getMonth() + 1);
        currentMonthDate.value = d;
    }
};

const navigateToday = () => {
    const today = new Date();
    currentWeekStart.value = getStartOfWeek(today);
    currentMonthDate.value = new Date(today.getFullYear(), today.getMonth(), 1);
};

// Appointment Status Badges
const getStatusBadgeClass = (status) => {
    switch (status) {
        case 'Atendida':
            return 'bg-emerald-50 text-emerald-700 border-emerald-200';
        case 'Confirmada':
            return 'bg-blue-50 text-blue-700 border-blue-200';
        case 'Pendiente':
            return 'bg-amber-50 text-amber-700 border-amber-200';
        case 'Cancelada':
            return 'bg-rose-50 text-rose-700 border-rose-250 line-through opacity-70';
        default:
            return 'bg-slate-50 text-slate-700 border-slate-200';
    }
};

// Left border highlighting for appointment cards
const getStatusBorderClass = (status) => {
    switch (status) {
        case 'Atendida':
            return 'border-l-4 border-l-emerald-500 bg-emerald-50/10 border-slate-200';
        case 'Confirmada':
            return 'border-l-4 border-l-blue-500 bg-blue-50/10 border-slate-200';
        case 'Pendiente':
            return 'border-l-4 border-l-amber-500 bg-amber-50/10 border-slate-200';
        case 'Cancelada':
            return 'border-l-4 border-l-rose-450 bg-rose-50/10 border-slate-200 opacity-75';
        default:
            return 'border-l-4 border-l-slate-400 bg-slate-50/10 border-slate-200';
    }
};

// MODALS AND FORMS STATE
const showCreateModal = ref(false);
const showDetailsModal = ref(false);
const showEditModal = ref(false);
const selectedAppointment = ref(null);

// Forms
const createForm = useForm({
    id_paciente: '',
    fecha_cita: '',
    hora_cita: '',
    motivo: '',
    estado: 'Pendiente',
});

const editForm = useForm({
    fecha_cita: '',
    hora_cita: '',
    motivo: '',
});

const statusForm = useForm({
    estado: '',
});

// Patient search dropdown logic for creation modal
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

// Open Modals
const openCreateModal = (date = null, time = null) => {
    createForm.reset();
    patientSearch.value = '';
    
    if (date) {
        createForm.fecha_cita = formatDateString(date);
    } else {
        createForm.fecha_cita = formatDateString(new Date());
    }
    
    if (time) {
        createForm.hora_cita = time;
    } else {
        createForm.hora_cita = '08:00';
    }
    
    showCreateModal.value = true;
};

const closeCreateModal = () => {
    showCreateModal.value = false;
};

const openDetailsModal = (appointment) => {
    selectedAppointment.value = appointment;
    showDetailsModal.value = true;
};

const closeDetailsModal = () => {
    showDetailsModal.value = false;
};

const openEditModal = () => {
    if (!selectedAppointment.value) return;
    
    editForm.fecha_cita = selectedAppointment.value.fecha;
    editForm.hora_cita = selectedAppointment.value.hora;
    editForm.motivo = selectedAppointment.value.motivo;
    
    showDetailsModal.value = false;
    showEditModal.value = true;
};

const closeEditModal = () => {
    showEditModal.value = false;
};

// Submit Actions
const submitCreate = () => {
    createForm.post(route('doctor.appointments.store'), {
        onSuccess: () => {
            closeCreateModal();
            createForm.reset();
        },
    });
};

const submitEdit = () => {
    editForm.put(route('doctor.appointments.update', selectedAppointment.value.id_cita), {
        onSuccess: () => {
            closeEditModal();
            selectedAppointment.value = null;
        },
    });
};

const updateStatus = (status) => {
    if (!selectedAppointment.value) return;
    
    statusForm.estado = status;
    statusForm.patch(route('doctor.appointments.status', selectedAppointment.value.id_cita), {
        onSuccess: () => {
            closeDetailsModal();
            selectedAppointment.value = null;
        },
    });
};

const deleteAppointment = () => {
    if (!selectedAppointment.value) return;
    
    if (confirm('¿Está seguro de que desea eliminar permanentemente esta cita de la agenda?')) {
        router.delete(route('doctor.appointments.destroy', selectedAppointment.value.id_cita), {
            onSuccess: () => {
                closeDetailsModal();
                selectedAppointment.value = null;
            },
        });
    }
};
</script>

<template>
    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between w-full">
                <div>
                    <h2 class="text-xl font-extrabold text-slate-800 tracking-tight">Agenda de Citas</h2>
                    <p class="text-xs text-slate-500 mt-0.5">Control semanal y mensual de pacientes programados.</p>
                </div>
                
                <button
                    @click="openCreateModal()"
                    class="inline-flex items-center gap-2 px-4 py-2.5 bg-gradient-to-r from-blue-600 to-indigo-650 hover:from-blue-700 hover:to-indigo-755 text-white rounded-xl text-xs font-bold shadow-md shadow-blue-500/20 active:scale-[0.98] transition cursor-pointer"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-4 h-4">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                    </svg>
                    Nueva Cita
                </button>
            </div>
        </template>

        <Head title="Agenda de Citas - ProDoctor" />

        <div class="space-y-6 select-none">
            
            <!-- Barra de Controles del Calendario -->
            <div class="bg-white rounded-xl border border-slate-200 p-4 flex flex-col md:flex-row items-center justify-between gap-4 shadow-sm">
                <!-- Selector de Vista (Semanal/Mensual) -->
                <div class="flex bg-slate-100 p-1 rounded-xl w-full md:w-auto">
                    <button
                        @click="selectedView = 'week'"
                        class="flex-1 md:flex-none px-4 py-2 rounded-lg text-xs font-bold transition duration-150 cursor-pointer"
                        :class="selectedView === 'week' ? 'bg-white text-blue-700 shadow-sm' : 'text-slate-550 hover:text-slate-800'"
                    >
                        Vista Semanal
                    </button>
                    <button
                        @click="selectedView = 'month'"
                        class="flex-1 md:flex-none px-4 py-2 rounded-lg text-xs font-bold transition duration-150 cursor-pointer"
                        :class="selectedView === 'month' ? 'bg-white text-blue-700 shadow-sm' : 'text-slate-550 hover:text-slate-800'"
                    >
                        Vista Mensual
                    </button>
                </div>

                <!-- Controles de Navegación del Calendario -->
                <div class="flex items-center gap-3 w-full md:w-auto justify-between md:justify-end">
                    <div class="flex items-center gap-1.5">
                        <button
                            @click="navigatePrev"
                            class="p-2.5 bg-slate-50 border border-slate-200 hover:bg-slate-100 rounded-xl text-slate-600 hover:text-slate-800 transition cursor-pointer"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-4 h-4">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5" />
                            </svg>
                        </button>
                        
                        <button
                            @click="navigateToday"
                            class="px-4 py-2.5 bg-slate-50 border border-slate-200 hover:bg-slate-100 rounded-xl text-xs font-bold text-slate-655 hover:text-slate-800 transition cursor-pointer"
                        >
                            Hoy
                        </button>
                        
                        <button
                            @click="navigateNext"
                            class="p-2.5 bg-slate-50 border border-slate-200 hover:bg-slate-100 rounded-xl text-slate-600 hover:text-slate-800 transition cursor-pointer"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-4 h-4">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5" />
                            </svg>
                        </button>
                    </div>

                    <!-- Label del Periodo -->
                    <h3 class="text-sm font-extrabold text-slate-800 tracking-tight md:ml-4">
                        {{ selectedView === 'week' ? weekHeaderLabel : monthHeaderLabel }}
                    </h3>
                </div>
            </div>

            <!-- VISTA SEMANAL -->
            <div v-if="selectedView === 'week'" class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-7 gap-4">
                <div
                    v-for="day in weekDays"
                    :key="day.getTime()"
                    class="bg-white rounded-xl border border-slate-200 flex flex-col min-h-[380px] shadow-sm transition"
                    :class="isToday(day) ? 'ring-2 ring-blue-500 ring-offset-1 bg-gradient-to-b from-blue-50/10 to-white' : ''"
                >
                    <!-- Cabecera de la columna (Día) -->
                    <div
                        class="p-3 border-b border-slate-150 flex items-center justify-between"
                        :class="isToday(day) ? 'bg-blue-600/5' : 'bg-slate-50/50'"
                    >
                        <div>
                            <span class="text-xs font-extrabold block" :class="isToday(day) ? 'text-blue-600' : 'text-slate-700'">
                                {{ formatDayHeader(day).split(' ')[0] }}
                            </span>
                            <span class="text-[10px] font-bold text-slate-400">
                                {{ day.getDate() }} {{ monthNames[day.getMonth()].substring(0, 3) }}
                            </span>
                        </div>
                        
                        <button
                            @click="openCreateModal(day)"
                            class="p-1 hover:bg-slate-200 rounded-lg text-slate-450 hover:text-slate-700 transition cursor-pointer"
                            title="Agendar cita en este día"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-4 h-4">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                            </svg>
                        </button>
                    </div>

                    <!-- Listado de Citas en el Día -->
                    <div class="flex-1 p-3 space-y-3.5 overflow-y-auto max-h-[480px]">
                        <div
                            v-for="app in getAppointmentsForDate(day)"
                            :key="app.id_cita"
                            @click="openDetailsModal(app)"
                            class="p-3 rounded-xl border shadow-sm hover:shadow-md transition cursor-pointer"
                            :class="getStatusBorderClass(app.estado)"
                        >
                            <div class="flex items-center justify-between mb-1.5">
                                <span class="text-[10px] font-extrabold text-slate-650 bg-slate-100/80 border border-slate-200 px-1.5 py-0.5 rounded">
                                    {{ app.hora }}
                                </span>
                                <span class="px-1.5 py-0.5 rounded text-[8px] font-black uppercase border" :class="getStatusBadgeClass(app.estado)">
                                    {{ app.estado }}
                                </span>
                            </div>
                            <h4 class="text-xs font-bold text-slate-800 truncate">{{ app.paciente_nombre }}</h4>
                            <p class="text-[10px] text-slate-450 truncate mt-1">{{ app.motivo }}</p>
                        </div>

                        <!-- Estado Vacío por día -->
                        <div
                            v-if="getAppointmentsForDate(day).length === 0"
                            class="h-full flex flex-col items-center justify-center py-10 text-center"
                        >
                            <div class="w-8 h-8 rounded-full bg-slate-50 flex items-center justify-center text-slate-300 mb-2 border border-slate-100">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25" />
                                </svg>
                            </div>
                            <span class="text-[10px] font-semibold text-slate-400">Sin citas</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- VISTA MENSUAL -->
            <div v-else class="bg-white rounded-xl border border-slate-200 overflow-hidden shadow-sm">
                <!-- Nombres de Días de la semana -->
                <div class="grid grid-cols-7 border-b border-slate-200 bg-slate-50 text-center py-2.5">
                    <div
                        v-for="name in dayNamesShort"
                        :key="name"
                        class="text-xs font-bold text-slate-400 uppercase tracking-wider"
                    >
                        {{ name }}
                    </div>
                </div>

                <!-- Cuadrícula de días del mes -->
                <div class="grid grid-cols-7 divide-x divide-y divide-slate-150">
                    <div
                        v-for="(cell, index) in monthDays"
                        :key="index"
                        class="min-h-[110px] p-2 flex flex-col group relative"
                        :class="[
                            cell.isCurrentMonth ? 'bg-white' : 'bg-slate-50/50 text-slate-400',
                            isToday(cell.date) ? 'bg-blue-50/15' : ''
                        ]"
                    >
                        <!-- Número del Día -->
                        <div class="flex items-center justify-between mb-1.5">
                            <span
                                class="w-6 h-6 flex items-center justify-center text-xs font-bold rounded-full"
                                :class="[
                                    isToday(cell.date) ? 'bg-blue-600 text-white shadow-sm shadow-blue-500/20' : 'text-slate-700',
                                    !cell.isCurrentMonth ? 'text-slate-350' : ''
                                ]"
                            >
                                {{ cell.date.getDate() }}
                            </span>
                            
                            <!-- Botón Agendar al hover de celda -->
                            <button
                                @click="openCreateModal(cell.date)"
                                class="opacity-0 group-hover:opacity-100 p-1 hover:bg-slate-100 rounded text-slate-400 hover:text-slate-650 transition cursor-pointer"
                                title="Agendar cita"
                            >
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-3 h-3">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                                </svg>
                            </button>
                        </div>

                        <!-- Citas en la celda del Mes (Max 3) -->
                        <div class="flex-1 space-y-1 overflow-hidden">
                            <div
                                v-for="app in getAppointmentsForDate(cell.date).slice(0, 3)"
                                :key="app.id_cita"
                                @click.stop="openDetailsModal(app)"
                                class="px-1.5 py-0.5 rounded text-[9px] font-bold border truncate hover:brightness-95 transition cursor-pointer flex items-center gap-1"
                                :class="getStatusBadgeClass(app.estado)"
                            >
                                <span class="font-extrabold opacity-75 shrink-0">{{ app.hora }}</span>
                                <span class="truncate">{{ app.paciente_nombre }}</span>
                            </div>
                            
                            <!-- Indicador de más citas -->
                            <div
                                v-if="getAppointmentsForDate(cell.date).length > 3"
                                class="text-[9px] font-bold text-blue-600 bg-blue-50 border border-blue-100 text-center rounded px-1"
                            >
                                +{{ getAppointmentsForDate(cell.date).length - 3 }} más
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- MODAL: DETALLES DE CITA / ACCIONES -->
        <div v-if="showDetailsModal && selectedAppointment" class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity" @click="closeDetailsModal"></div>
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
                
                <!-- Contenido Modal -->
                <div class="relative inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full border border-slate-200">
                    
                    <!-- Encabezado con estado -->
                    <div class="p-6 border-b border-slate-150 flex items-center justify-between">
                        <div>
                            <span class="text-[9px] font-extrabold uppercase text-slate-400 tracking-wider">Cita Médica #{{ String(selectedAppointment.id_cita).padStart(4, '0') }}</span>
                            <h3 class="text-base font-extrabold text-slate-800 mt-0.5">Detalles de Programación</h3>
                        </div>
                        <span class="px-3 py-1 rounded-full text-xs font-bold border" :class="getStatusBadgeClass(selectedAppointment.estado)">
                            {{ selectedAppointment.estado }}
                        </span>
                    </div>

                    <!-- Cuerpo de Detalles -->
                    <div class="p-6 space-y-4">
                        <!-- Paciente -->
                        <div class="bg-slate-50 rounded-xl p-4 border border-slate-150 flex items-center gap-3">
                            <div class="w-10 h-10 rounded-full bg-gradient-to-tr from-blue-500/10 to-indigo-500/10 border border-blue-200 flex items-center justify-center font-bold text-sm text-blue-700">
                                {{ selectedAppointment.paciente_nombre.split(' ').map(n => n[0]).join('').substring(0, 2).toUpperCase() }}
                            </div>
                            <div>
                                <span class="text-[10px] text-slate-400 font-bold uppercase tracking-wider">PACIENTE</span>
                                <h4 class="font-extrabold text-slate-850 text-sm mt-0.5">{{ selectedAppointment.paciente_nombre }}</h4>
                            </div>
                        </div>

                        <!-- Fecha y Hora -->
                        <div class="grid grid-cols-2 gap-4">
                            <div class="bg-slate-50 border border-slate-150 rounded-xl p-3.5">
                                <span class="text-[10px] text-slate-400 font-bold uppercase tracking-wider block">FECHA PROGRAMADA</span>
                                <span class="text-xs font-bold text-slate-700 mt-1 block">
                                    {{ new Date(selectedAppointment.fecha + 'T00:00:00').toLocaleDateString('es-ES', { weekday: 'long', day: 'numeric', month: 'long' }) }}
                                </span>
                            </div>
                            <div class="bg-slate-50 border border-slate-150 rounded-xl p-3.5">
                                <span class="text-[10px] text-slate-400 font-bold uppercase tracking-wider block">HORARIO</span>
                                <span class="text-xs font-bold text-slate-700 mt-1 block">{{ selectedAppointment.hora }} hrs</span>
                            </div>
                        </div>

                        <!-- Motivo -->
                        <div class="space-y-1">
                            <span class="text-[10px] text-slate-400 font-bold uppercase tracking-wider">MOTIVO DE CONSULTA</span>
                            <div class="bg-slate-50 border border-slate-150 rounded-xl p-4 text-xs font-semibold text-slate-650 leading-relaxed whitespace-pre-line">
                                {{ selectedAppointment.motivo }}
                            </div>
                        </div>
                    </div>

                    <!-- Pie con Acciones de Gestión de Citas (Confirmar, Cancelar, Atendida, Editar, Eliminar) -->
                    <div class="p-6 bg-slate-50 border-t border-slate-200 flex flex-col gap-3">
                        
                        <!-- Acciones Principales de Estado -->
                        <div class="flex flex-wrap items-center gap-2">
                            <!-- Confirmar -->
                            <button
                                v-if="selectedAppointment.estado === 'Pendiente'"
                                @click="updateStatus('Confirmada')"
                                class="flex-1 min-w-[120px] inline-flex items-center justify-center gap-1.5 px-3 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-xl text-xs font-bold shadow-sm transition cursor-pointer"
                            >
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-4 h-4">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                                </svg>
                                Confirmar Cita
                            </button>

                            <!-- Atendida -->
                            <button
                                v-if="selectedAppointment.estado === 'Confirmada' || selectedAppointment.estado === 'Pendiente'"
                                @click="updateStatus('Atendida')"
                                class="flex-1 min-w-[120px] inline-flex items-center justify-center gap-1.5 px-3 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-xs font-bold shadow-sm transition cursor-pointer"
                            >
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-4 h-4">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12c0 1.268-.63 2.39-1.593 3.068a3.745 3.745 0 01-1.043 3.296 3.745 3.745 0 01-3.296 1.043A3.745 3.745 0 0112 21c-1.268 0-2.39-.63-3.068-1.593a3.746 3.746 0 01-3.296-1.043 3.745 3.745 0 01-1.043-3.296A3.745 3.745 0 013 12c0-1.268.63-2.39 1.593-3.068a3.745 3.745 0 011.043-3.296 3.746 3.746 0 013.296-1.043A3.746 3.746 0 0112 3c1.268 0 2.39.63 3.068 1.593a3.746 3.746 0 013.296 1.043 3.746 3.746 0 011.043 3.296A3.745 3.745 0 0121 12z" />
                                </svg>
                                Registrar Atendida
                            </button>

                            <!-- Cancelar -->
                            <button
                                v-if="selectedAppointment.estado !== 'Cancelada' && selectedAppointment.estado !== 'Atendida'"
                                @click="updateStatus('Cancelada')"
                                class="flex-1 min-w-[120px] inline-flex items-center justify-center gap-1.5 px-3 py-2 bg-rose-50 border border-rose-200 hover:bg-rose-100 text-rose-700 rounded-xl text-xs font-bold transition cursor-pointer"
                            >
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-4 h-4">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636" />
                                </svg>
                                Cancelar Cita
                            </button>

                            <!-- Colocar Pendiente -->
                            <button
                                v-if="selectedAppointment.estado === 'Cancelada'"
                                @click="updateStatus('Pendiente')"
                                class="flex-1 min-w-[120px] inline-flex items-center justify-center gap-1.5 px-3 py-2 bg-amber-50 border border-amber-200 hover:bg-amber-100 text-amber-700 rounded-xl text-xs font-bold transition cursor-pointer"
                            >
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0l3.181 3.183a8.25 8.25 0 0013.803-3.7M4.031 9.865a8.25 8.25 0 0113.803-3.7l3.181 3.182m0-4.991v4.99" />
                                </svg>
                                Reactivar Cita
                            </button>
                        </div>

                        <!-- Edición y Eliminación -->
                        <div class="flex items-center justify-between border-t border-slate-200 pt-3.5">
                            <button
                                @click="openEditModal"
                                class="inline-flex items-center gap-1.5 px-3 py-2 bg-white hover:bg-slate-100 border border-slate-200 rounded-xl text-xs font-bold text-slate-700 transition cursor-pointer"
                            >
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10" />
                                </svg>
                                Re-programar / Editar
                            </button>

                            <button
                                @click="deleteAppointment"
                                class="inline-flex items-center gap-1.5 px-3 py-2 text-rose-600 hover:bg-rose-50 rounded-xl text-xs font-bold border border-transparent hover:border-rose-100 transition cursor-pointer"
                            >
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" />
                                </svg>
                                Eliminar de Agenda
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- MODAL: CREAR CITA -->
        <div v-if="showCreateModal" class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity" @click="closeCreateModal"></div>
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
                
                <div class="relative inline-block align-bottom bg-white rounded-2xl text-left overflow-visible shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full border border-slate-200">
                    
                    <div class="p-6 border-b border-slate-150 flex items-center justify-between">
                        <div>
                            <h3 class="text-base font-extrabold text-slate-800">Nueva Cita Médica</h3>
                            <p class="text-xs text-slate-500 mt-0.5">Completa la información para agendar un paciente.</p>
                        </div>
                        <button @click="closeCreateModal" class="text-slate-450 hover:text-slate-600 transition cursor-pointer">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-5 h-5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                    <form @submit.prevent="submitCreate">
                        <div class="p-6 space-y-4">
                            
                            <!-- Paciente Autocomplete/Selector -->
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
                                <div
                                    v-if="isPatientDropdownOpen && filteredPatients.length === 0"
                                    class="absolute z-50 left-0 right-0 top-full mt-1 bg-white border border-slate-200 rounded-xl shadow-xl p-3 text-xs text-center text-slate-400 font-medium"
                                >
                                    No se encontraron pacientes.
                                    <Link :href="route('doctor.patients.create')" class="text-blue-600 font-bold hover:underline block mt-1">¿Registrar nuevo paciente?</Link>
                                </div>
                            </div>

                            <!-- Fecha y Hora -->
                            <div class="grid grid-cols-2 gap-4">
                                <div class="space-y-1.5">
                                    <label class="text-xs font-bold text-slate-550 uppercase tracking-wider block">FECHA</label>
                                    <input
                                        type="date"
                                        v-model="createForm.fecha_cita"
                                        class="block w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-slate-800 text-xs focus:outline-none focus:bg-white focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 transition"
                                    />
                                    <div v-if="createForm.errors.fecha_cita" class="text-xs text-rose-600 font-semibold mt-1">{{ createForm.errors.fecha_cita }}</div>
                                </div>
                                <div class="space-y-1.5">
                                    <label class="text-xs font-bold text-slate-550 uppercase tracking-wider block">HORA</label>
                                    <input
                                        type="time"
                                        v-model="createForm.hora_cita"
                                        class="block w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-slate-800 text-xs focus:outline-none focus:bg-white focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 transition"
                                    />
                                    <div v-if="createForm.errors.hora_cita" class="text-xs text-rose-600 font-semibold mt-1">{{ createForm.errors.hora_cita }}</div>
                                </div>
                            </div>

                            <!-- Estado Inicial -->
                            <div class="space-y-1.5">
                                <label class="text-xs font-bold text-slate-550 uppercase tracking-wider block">ESTADO INICIAL</label>
                                <select
                                    v-model="createForm.estado"
                                    class="block w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-slate-800 text-xs focus:outline-none focus:bg-white focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 transition"
                                >
                                    <option value="Pendiente">Pendiente (Por confirmar)</option>
                                    <option value="Confirmada">Confirmada</option>
                                    <option value="Atendida">Atendida</option>
                                    <option value="Cancelada">Cancelada</option>
                                </select>
                                <div v-if="createForm.errors.estado" class="text-xs text-rose-600 font-semibold mt-1">{{ createForm.errors.estado }}</div>
                            </div>

                            <!-- Motivo -->
                            <div class="space-y-1.5">
                                <label class="text-xs font-bold text-slate-550 uppercase tracking-wider block">MOTIVO DE CONSULTA</label>
                                <textarea
                                    rows="3"
                                    placeholder="Detalla los síntomas o motivo de consulta médica..."
                                    v-model="createForm.motivo"
                                    class="block w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-slate-800 text-xs focus:outline-none focus:bg-white focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 transition resize-none"
                                ></textarea>
                                <div v-if="createForm.errors.motivo" class="text-xs text-rose-600 font-semibold mt-1">{{ createForm.errors.motivo }}</div>
                            </div>

                        </div>

                        <!-- Pie de Formulario -->
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
                                class="px-5 py-2.5 bg-gradient-to-r from-blue-600 to-indigo-650 hover:from-blue-700 hover:to-indigo-755 text-white text-xs font-bold rounded-xl shadow-md shadow-blue-500/20 active:scale-[0.98] transition cursor-pointer disabled:opacity-50 disabled:cursor-not-allowed"
                            >
                                Agendar Cita
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- MODAL: EDITAR / RE-PROGRAMAR CITA -->
        <div v-if="showEditModal && selectedAppointment" class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity" @click="closeEditModal"></div>
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
                
                <div class="relative inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full border border-slate-200">
                    
                    <div class="p-6 border-b border-slate-150 flex items-center justify-between">
                        <div>
                            <h3 class="text-base font-extrabold text-slate-800">Modificar / Re-programar Cita</h3>
                            <p class="text-xs text-slate-500 mt-0.5">Actualiza el horario o detalles de la cita.</p>
                        </div>
                        <button @click="closeEditModal" class="text-slate-450 hover:text-slate-600 transition cursor-pointer">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-5 h-5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                    <form @submit.prevent="submitEdit">
                        <div class="p-6 space-y-4">
                            <!-- Info del Paciente (Solo lectura) -->
                            <div class="bg-slate-50 rounded-xl p-3.5 border border-slate-150">
                                <span class="text-[10px] text-slate-400 font-bold uppercase tracking-wider block">PACIENTE</span>
                                <h4 class="font-bold text-slate-800 text-sm mt-0.5">{{ selectedAppointment.paciente_nombre }}</h4>
                            </div>

                            <!-- Fecha y Hora -->
                            <div class="grid grid-cols-2 gap-4">
                                <div class="space-y-1.5">
                                    <label class="text-xs font-bold text-slate-550 uppercase tracking-wider block">FECHA</label>
                                    <input
                                        type="date"
                                        v-model="editForm.fecha_cita"
                                        class="block w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-slate-800 text-xs focus:outline-none focus:bg-white focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 transition"
                                    />
                                    <div v-if="editForm.errors.fecha_cita" class="text-xs text-rose-600 font-semibold mt-1">{{ editForm.errors.fecha_cita }}</div>
                                </div>
                                <div class="space-y-1.5">
                                    <label class="text-xs font-bold text-slate-550 uppercase tracking-wider block">HORA</label>
                                    <input
                                        type="time"
                                        v-model="editForm.hora_cita"
                                        class="block w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-slate-800 text-xs focus:outline-none focus:bg-white focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 transition"
                                    />
                                    <div v-if="editForm.errors.hora_cita" class="text-xs text-rose-600 font-semibold mt-1">{{ editForm.errors.hora_cita }}</div>
                                </div>
                            </div>

                            <!-- Motivo -->
                            <div class="space-y-1.5">
                                <label class="text-xs font-bold text-slate-550 uppercase tracking-wider block">MOTIVO DE CONSULTA</label>
                                <textarea
                                    rows="3"
                                    placeholder="Motivo de la cita..."
                                    v-model="editForm.motivo"
                                    class="block w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-slate-800 text-xs focus:outline-none focus:bg-white focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 transition resize-none"
                                ></textarea>
                                <div v-if="editForm.errors.motivo" class="text-xs text-rose-600 font-semibold mt-1">{{ editForm.errors.motivo }}</div>
                            </div>
                        </div>

                        <!-- Pie de Formulario -->
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
                                class="px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold rounded-xl shadow-md shadow-blue-500/20 active:scale-[0.98] transition cursor-pointer disabled:opacity-50"
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
