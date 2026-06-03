<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { ref, computed } from 'vue';

const props = defineProps({
    patient: {
        type: Object,
        required: true,
    },
    proximaCita: {
        type: Object,
        default: null,
    },
    seguimientos: {
        type: Array,
        required: true,
    },
    procedimientos: {
        type: Array,
        required: true,
    },
    historialCitas: {
        type: Array,
        required: true,
    },
    doctores: {
        type: Array,
        required: true,
    },
});

// Tab navigation for medical history
const activeTab = ref('followups'); // 'followups', 'procedures', 'history'

// Forms
const bookingForm = useForm({
    id_doctor: '',
    fecha_cita: '',
    hora_cita: '08:00',
    motivo: '',
});

const profileForm = useForm({
    nombres: props.patient.nombres || '',
    apellidos: props.patient.apellidos || '',
    dui: props.patient.dui || '',
    fecha_nacimiento: props.patient.fecha_nacimiento || '',
    sexo: props.patient.sexo || 'Otro',
    telefono: props.patient.telefono || '',
    direccion: props.patient.direccion || '',
});

// Edit profile toggle
const isEditingProfile = ref(false);

const startEditing = () => {
    profileForm.nombres = props.patient.nombres || '';
    profileForm.apellidos = props.patient.apellidos || '';
    profileForm.dui = props.patient.dui || '';
    profileForm.fecha_nacimiento = props.patient.fecha_nacimiento || '';
    profileForm.sexo = props.patient.sexo || 'Otro';
    profileForm.telefono = props.patient.telefono || '';
    profileForm.direccion = props.patient.direccion || '';
    isEditingProfile.value = true;
};

const cancelEditing = () => {
    isEditingProfile.value = false;
};

// Form submits
const submitBooking = () => {
    bookingForm.post(route('paciente.appointments.store'), {
        onSuccess: () => {
            bookingForm.reset('id_doctor', 'motivo');
            bookingForm.fecha_cita = '';
            alert('¡Tu solicitud de cita ha sido enviada con éxito! Queda a la espera de aprobación por el personal médico.');
        }
    });
};

const submitProfileUpdate = () => {
    profileForm.put(route('paciente.profile.update'), {
        onSuccess: () => {
            isEditingProfile.value = false;
        }
    });
};

// Helper for status badge colors (using warm/healing scheme)
const getStatusBadgeClass = (status) => {
    switch (status) {
        case 'Atendida':
        case 'Finalizado':
            return 'bg-emerald-50 text-emerald-700 border-emerald-200';
        case 'Confirmada':
        case 'En proceso':
            return 'bg-teal-50 text-teal-700 border-teal-200';
        case 'Pendiente':
            return 'bg-amber-50 text-amber-700 border-amber-200';
        case 'Cancelada':
            return 'bg-rose-50 text-rose-700 border-rose-250 opacity-70';
        default:
            return 'bg-slate-50 text-slate-700 border-slate-200';
    }
};

const getAge = (birthdate) => {
    if (!birthdate) return '';
    const today = new Date();
    const birth = new Date(birthdate);
    let age = today.getFullYear() - birth.getFullYear();
    const m = today.getMonth() - birth.getMonth();
    if (m < 0 || (m === 0 && today.getDate() < birth.getDate())) {
        age--;
    }
    return `${age} años`;
};
</script>

<template>
    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between w-full">
                <div>
                    <h2 class="text-xl font-extrabold text-teal-900 tracking-tight">Mi Portal Médico</h2>
                    <p class="text-xs text-teal-650 mt-0.5">Bienvenido, {{ patient.nombres }} {{ patient.apellidos }}. Revisa tus citas, recetas y tratamientos.</p>
                </div>
            </div>
        </template>

        <Head title="Mi Portal Médico - ProDoctor" />

        <div class="space-y-8 select-none">
            
            <!-- PRÓXIMA CITA BANNER -->
            <div
                v-if="proximaCita"
                class="bg-gradient-to-r from-teal-500 to-emerald-600 text-white rounded-2xl p-6 shadow-lg shadow-teal-600/10 flex flex-col md:flex-row items-center justify-between gap-6 relative overflow-hidden"
            >
                <!-- ECG background line decoration -->
                <div class="absolute inset-0 opacity-10 pointer-events-none flex items-center justify-center">
                    <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="w-full h-48 stroke-white stroke-2">
                        <path d="M2 12h4.5l1.5-4 2.5 8 1.5-5 1.5 1H22" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </div>

                <div class="flex items-center gap-4 z-10">
                    <div class="w-12 h-12 rounded-xl bg-white/20 flex items-center justify-center flex-shrink-0 text-white">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-6 h-6">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25" />
                        </svg>
                    </div>
                    <div>
                        <span class="text-[10px] font-black uppercase tracking-wider text-teal-100">Próxima Consulta Programada</span>
                        <h3 class="text-lg font-black tracking-tight mt-0.5">{{ proximaCita.doctor_nombre }}</h3>
                        <p class="text-xs text-teal-50 font-medium mt-1">Motivo: {{ proximaCita.motivo }}</p>
                    </div>
                </div>

                <div class="text-center md:text-right z-10">
                    <div class="text-2xl font-black">{{ new Date(proximaCita.fecha + 'T00:00:00').toLocaleDateString('es-ES', { weekday: 'long', day: 'numeric', month: 'long' }) }}</div>
                    <div class="text-sm text-teal-100 font-bold mt-1">Horario: {{ proximaCita.hora }} hrs</div>
                    <span class="inline-block mt-3 px-3 py-1 bg-white/25 text-white font-extrabold text-[10px] uppercase rounded-full border border-white/20">
                        Cita {{ proximaCita.estado }}
                    </span>
                </div>
            </div>

            <!-- VACÍO PRÓXIMA CITA -->
            <div
                v-else
                class="bg-white rounded-2xl border border-teal-100 p-6 flex flex-col md:flex-row items-center justify-between gap-4 shadow-sm"
            >
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 rounded-xl bg-teal-50 text-teal-600 flex items-center justify-center flex-shrink-0">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-6 h-6">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.182 15.182a4.5 4.5 0 01-6.364 0M21 12a9 9 0 11-18 0 9 9 0 0118 0zM9.75 9.75c0 .414-.168.75-.375.75S9 10.164 9 9.75 9.168 9 9.375 9s.375.336.375.75zm-.375 0h.008v.015h-.008V9.75zm5.625 0c0 .414-.168.75-.375.75s-.375-.336-.375-.75.168-.75.375-.75.375.336.375.75zm-.375 0h.008v.015h-.008V9.75z" />
                        </svg>
                    </div>
                    <div>
                        <h4 class="font-bold text-slate-800 text-sm">No tienes citas programadas próximamente</h4>
                        <p class="text-xs text-slate-500 mt-0.5">Te recomendamos programar una cita si necesitas controles o recetas.</p>
                    </div>
                </div>
            </div>

            <!-- CONTENIDO PRINCIPAL: 3 COLUMNAS -->
            <div class="grid grid-cols-1 xl:grid-cols-3 gap-8 items-start">
                
                <!-- COLUMNA 1: MI PERFIL (EDITABLE) -->
                <div class="bg-white rounded-2xl border border-teal-100 p-6 shadow-sm space-y-6">
                    <div class="flex items-center justify-between">
                        <h3 class="text-sm font-black text-teal-900 uppercase tracking-wider">Mis Datos Personales</h3>
                        <button
                            v-if="!isEditingProfile"
                            @click="startEditing"
                            class="text-xs font-bold text-teal-600 hover:text-teal-700 hover:underline cursor-pointer"
                        >
                            Editar
                        </button>
                    </div>

                    <!-- Vista Lectura Perfil -->
                    <div v-if="!isEditingProfile" class="space-y-4">
                        <div class="flex items-center gap-3 bg-teal-50/55 rounded-xl p-4 border border-teal-100/50">
                            <div class="w-12 h-12 rounded-full bg-gradient-to-tr from-teal-500 to-emerald-600 flex items-center justify-center font-black text-white text-base shadow-sm">
                                {{ (patient.nombres ? patient.nombres[0] : '') + (patient.apellidos ? patient.apellidos[0] : '') }}
                            </div>
                            <div>
                                <h4 class="font-extrabold text-slate-800 text-sm leading-tight">{{ patient.nombres }} {{ patient.apellidos }}</h4>
                                <span class="text-[10px] text-teal-650 font-bold uppercase tracking-wider mt-0.5 block">Expediente #PAC-{{ String(patient.id_paciente).padStart(4, '0') }}</span>
                            </div>
                        </div>

                        <div class="space-y-3.5 text-xs font-semibold bg-slate-50 rounded-xl p-4 border border-slate-100">
                            <div class="flex justify-between border-b border-slate-200/60 pb-2">
                                <span class="text-slate-400">DUI</span>
                                <span class="text-slate-850 font-bold">{{ patient.dui || 'No registrado' }}</span>
                            </div>
                            <div class="flex justify-between border-b border-slate-200/60 pb-2">
                                <span class="text-slate-400">Edad / Género</span>
                                <span class="text-slate-850 font-bold">{{ getAge(patient.fecha_nacimiento) || 'No registrada' }} / {{ patient.sexo }}</span>
                            </div>
                            <div class="flex justify-between border-b border-slate-200/60 pb-2">
                                <span class="text-slate-400">Teléfono</span>
                                <span class="text-slate-850 font-bold">{{ patient.telefono }}</span>
                            </div>
                            <div class="flex justify-between border-b border-slate-200/60 pb-2">
                                <span class="text-slate-400">Tipo de Sangre</span>
                                <span class="text-teal-700 font-extrabold bg-teal-50 border border-teal-150 px-2 py-0.5 rounded">{{ patient.tipo_sangre || 'Desconocido' }}</span>
                            </div>
                            <div>
                                <span class="text-slate-400 block mb-1">Dirección</span>
                                <span class="text-slate-700 font-medium block leading-normal">{{ patient.direccion }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Formulario de Edición de Perfil -->
                    <form v-else @submit.prevent="submitProfileUpdate" class="space-y-4">
                        <div class="grid grid-cols-2 gap-3">
                            <div class="space-y-1">
                                <label class="text-[10px] font-bold text-slate-450 uppercase block">Nombres</label>
                                <input
                                    type="text"
                                    v-model="profileForm.nombres"
                                    class="block w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-slate-800 text-xs focus:outline-none focus:border-teal-500 focus:ring-4 focus:ring-teal-500/10 transition"
                                />
                                <div v-if="profileForm.errors.nombres" class="text-[10px] text-rose-600 font-semibold">{{ profileForm.errors.nombres }}</div>
                            </div>
                            <div class="space-y-1">
                                <label class="text-[10px] font-bold text-slate-455 uppercase block">Apellidos</label>
                                <input
                                    type="text"
                                    v-model="profileForm.apellidos"
                                    class="block w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-slate-800 text-xs focus:outline-none focus:border-teal-500 focus:ring-4 focus:ring-teal-500/10 transition"
                                />
                                <div v-if="profileForm.errors.apellidos" class="text-[10px] text-rose-600 font-semibold">{{ profileForm.errors.apellidos }}</div>
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-3">
                            <div class="space-y-1">
                                <label class="text-[10px] font-bold text-slate-455 uppercase block">DUI</label>
                                <input
                                    type="text"
                                    v-model="profileForm.dui"
                                    placeholder="00000000-0"
                                    class="block w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-slate-800 text-xs focus:outline-none focus:border-teal-500 focus:ring-4 focus:ring-teal-500/10 transition"
                                />
                                <div v-if="profileForm.errors.dui" class="text-[10px] text-rose-600 font-semibold">{{ profileForm.errors.dui }}</div>
                            </div>
                            <div class="space-y-1">
                                <label class="text-[10px] font-bold text-slate-455 uppercase block">Género</label>
                                <select
                                    v-model="profileForm.sexo"
                                    class="block w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-slate-800 text-xs focus:outline-none focus:border-teal-500 focus:ring-4 focus:ring-teal-500/10 transition"
                                >
                                    <option value="Masculino">Masculino</option>
                                    <option value="Femenino">Femenino</option>
                                    <option value="Otro">Otro</option>
                                </select>
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-3">
                            <div class="space-y-1">
                                <label class="text-[10px] font-bold text-slate-455 uppercase block">F. Nacimiento</label>
                                <input
                                    type="date"
                                    v-model="profileForm.fecha_nacimiento"
                                    class="block w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-slate-800 text-xs focus:outline-none focus:border-teal-500 focus:ring-4 focus:ring-teal-500/10 transition"
                                />
                                <div v-if="profileForm.errors.fecha_nacimiento" class="text-[10px] text-rose-600 font-semibold">{{ profileForm.errors.fecha_nacimiento }}</div>
                            </div>
                            <div class="space-y-1">
                                <label class="text-[10px] font-bold text-slate-455 uppercase block">Teléfono</label>
                                <input
                                    type="text"
                                    v-model="profileForm.telefono"
                                    class="block w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-slate-800 text-xs focus:outline-none focus:border-teal-500 focus:ring-4 focus:ring-teal-500/10 transition"
                                />
                                <div v-if="profileForm.errors.telefono" class="text-[10px] text-rose-600 font-semibold">{{ profileForm.errors.telefono }}</div>
                            </div>
                        </div>

                        <div class="space-y-1">
                            <label class="text-[10px] font-bold text-slate-455 uppercase block">Dirección</label>
                            <textarea
                                rows="2"
                                v-model="profileForm.direccion"
                                class="block w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-slate-800 text-xs focus:outline-none focus:border-teal-500 focus:ring-4 focus:ring-teal-500/10 transition resize-none"
                            ></textarea>
                            <div v-if="profileForm.errors.direccion" class="text-[10px] text-rose-600 font-semibold">{{ profileForm.errors.direccion }}</div>
                        </div>

                        <div class="flex items-center justify-end gap-2 pt-2">
                            <button
                                type="button"
                                @click="cancelEditing"
                                class="px-3 py-1.5 bg-slate-100 hover:bg-slate-200 text-slate-655 text-xs font-bold rounded-xl transition cursor-pointer"
                            >
                                Cancelar
                            </button>
                            <button
                                type="submit"
                                :disabled="profileForm.processing"
                                class="px-4 py-1.5 bg-teal-600 hover:bg-teal-700 text-white text-xs font-bold rounded-xl shadow-md transition cursor-pointer"
                            >
                                Guardar
                            </button>
                        </div>
                    </form>
                </div>

                <!-- COLUMNA 2: CLINICAL RECORDS (TABS: FOLLOWUPS, PROCEDURES, HISTORY - 2/3) -->
                <div class="xl:col-span-2 space-y-6 flex flex-col">
                    <!-- Tab Headers -->
                    <div class="bg-white rounded-xl border border-teal-100 p-2 flex gap-2 shadow-sm shrink-0">
                        <button
                            @click="activeTab = 'followups'"
                            class="flex-1 py-2.5 rounded-lg text-xs font-bold transition duration-150 cursor-pointer"
                            :class="activeTab === 'followups' ? 'bg-teal-50 text-teal-750' : 'text-slate-550 hover:bg-slate-50'"
                        >
                            Mis Seguimientos / Recetas
                        </button>
                        <button
                            @click="activeTab = 'procedures'"
                            class="flex-1 py-2.5 rounded-lg text-xs font-bold transition duration-150 cursor-pointer"
                            :class="activeTab === 'procedures' ? 'bg-teal-50 text-teal-750' : 'text-slate-550 hover:bg-slate-50'"
                        >
                            Procedimientos
                        </button>
                        <button
                            @click="activeTab = 'history'"
                            class="flex-1 py-2.5 rounded-lg text-xs font-bold transition duration-150 cursor-pointer"
                            :class="activeTab === 'history' ? 'bg-teal-50 text-teal-750' : 'text-slate-550 hover:bg-slate-50'"
                        >
                            Historial de Citas
                        </button>
                    </div>

                    <!-- Tab Contents -->
                    <div class="bg-white rounded-2xl border border-teal-100 p-6 shadow-sm flex-1 min-h-[400px]">
                        
                        <!-- TAB: FOLLOWUPS -->
                        <div v-if="activeTab === 'followups'" class="space-y-6">
                            <div class="border-b border-slate-100 pb-3">
                                <h3 class="text-sm font-black text-teal-900 uppercase tracking-wider">Seguimientos Clínicos y Recetas</h3>
                                <p class="text-[11px] text-slate-450 mt-0.5">Indicaciones de tratamientos asignados en tus citas médicas.</p>
                            </div>

                            <div class="space-y-4">
                                <div
                                    v-for="seg in seguimientos"
                                    :key="seg.id_seguimiento"
                                    class="p-5 bg-slate-50 border border-slate-200/60 rounded-xl space-y-3 relative border-l-4 border-l-teal-500"
                                >
                                    <div class="flex items-center justify-between">
                                        <span class="text-xs font-bold text-slate-800">Atendido por: {{ seg.doctor_nombre }}</span>
                                        <span class="text-[10px] font-bold text-slate-400">Fecha: {{ new Date(seg.fecha + 'T00:00:00').toLocaleDateString('es-ES', { day: 'numeric', month: 'long', year: 'numeric' }) }}</span>
                                    </div>
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 pt-2 border-t border-slate-200/50 text-xs leading-relaxed">
                                        <div>
                                            <span class="text-[9px] font-bold text-slate-400 uppercase tracking-wider block">Observación Médica</span>
                                            <p class="text-slate-655 font-medium mt-1 font-semibold whitespace-pre-line">{{ seg.observaciones }}</p>
                                        </div>
                                        <div>
                                            <span class="text-[9px] font-bold text-slate-400 uppercase tracking-wider block">Receta y Tratamiento</span>
                                            <p class="text-slate-655 font-medium mt-1 font-semibold whitespace-pre-line bg-teal-50/30 p-2 border border-teal-100/50 rounded-lg">{{ seg.tratamiento }}</p>
                                        </div>
                                    </div>
                                    <div v-if="seg.proxima_revision" class="pt-2 border-t border-slate-200/40">
                                        <span class="text-[9px] font-extrabold text-blue-600 bg-blue-50 border border-blue-150 px-2 py-0.5 rounded">
                                            Próxima revisión médica: {{ new Date(seg.proxima_revision + 'T00:00:00').toLocaleDateString('es-ES', { day: 'numeric', month: 'long', year: 'numeric' }) }}
                                        </span>
                                    </div>
                                </div>

                                <div v-if="seguimientos.length === 0" class="text-center py-12 text-slate-400 text-xs font-medium">
                                    No tienes registros de seguimientos clínicos en tu expediente.
                                </div>
                            </div>
                        </div>

                        <!-- TAB: PROCEDEMENTS -->
                        <div v-if="activeTab === 'procedures'" class="space-y-6">
                            <div class="border-b border-slate-100 pb-3">
                                <h3 class="text-sm font-black text-teal-900 uppercase tracking-wider">Procedimientos Médicos</h3>
                                <p class="text-[11px] text-slate-455 mt-0.5">Tratamientos técnicos especiales o controles asignados.</p>
                            </div>

                            <div class="space-y-4">
                                <div
                                    v-for="proc in procedimientos"
                                    :key="proc.id_procedimiento"
                                    class="p-4 bg-slate-50 border border-slate-200/60 rounded-xl flex items-center justify-between border-l-4 border-l-emerald-500"
                                >
                                    <div>
                                        <h4 class="text-xs font-black text-slate-800">{{ proc.nombre }}</h4>
                                        <p class="text-[11px] text-slate-455 mt-1">{{ proc.descripcion }}</p>
                                        <div class="flex items-center gap-4 mt-2 text-[10px] text-slate-400 font-bold uppercase">
                                            <span>Fecha: {{ new Date(proc.fecha + 'T00:00:00').toLocaleDateString('es-ES', { day: 'numeric', month: 'short', year: 'numeric' }) }}</span>
                                            <span>Doctor: {{ proc.doctor_nombre }}</span>
                                        </div>
                                    </div>
                                    <span class="px-2 py-0.5 rounded text-[9px] font-black uppercase border" :class="getStatusBadgeClass(proc.estado)">
                                        {{ proc.estado }}
                                    </span>
                                </div>

                                <div v-if="procedimientos.length === 0" class="text-center py-12 text-slate-400 text-xs font-medium">
                                    No se registran procedimientos asignados en tu ficha.
                                </div>
                            </div>
                        </div>

                        <!-- TAB: HISTORY -->
                        <div v-if="activeTab === 'history'" class="space-y-6">
                            <div class="border-b border-slate-100 pb-3">
                                <h3 class="text-sm font-black text-teal-900 uppercase tracking-wider">Historial Clínico de Citas</h3>
                                <p class="text-[11px] text-slate-455 mt-0.5">Resumen de tus citas médicas registradas en ProDoctor.</p>
                            </div>

                            <div class="overflow-x-auto">
                                <table class="w-full text-left border-collapse text-xs">
                                    <thead>
                                        <tr class="bg-slate-50 border-b border-slate-200">
                                            <th class="px-4 py-3 text-xs font-bold uppercase tracking-wider text-slate-400">Doctor</th>
                                            <th class="px-4 py-3 text-xs font-bold uppercase tracking-wider text-slate-400">Fecha</th>
                                            <th class="px-4 py-3 text-xs font-bold uppercase tracking-wider text-slate-400">Hora</th>
                                            <th class="px-4 py-3 text-xs font-bold uppercase tracking-wider text-slate-400">Motivo</th>
                                            <th class="px-4 py-3 text-xs font-bold uppercase tracking-wider text-slate-400">Estado</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-slate-100">
                                        <tr v-for="cita in historialCitas" :key="cita.id_cita" class="hover:bg-slate-50/50 transition">
                                            <td class="px-4 py-3 font-bold text-slate-800">{{ cita.doctor_nombre }}</td>
                                            <td class="px-4 py-3 font-semibold text-slate-500">{{ new Date(cita.fecha + 'T00:00:00').toLocaleDateString('es-ES', { day: 'numeric', month: 'short', year: 'numeric' }) }}</td>
                                            <td class="px-4 py-3 text-slate-655 font-semibold">{{ cita.hora }}</td>
                                            <td class="px-4 py-3 text-slate-500 font-medium truncate max-w-[200px]">{{ cita.motivo }}</td>
                                            <td class="px-4 py-3">
                                                <span class="px-2 py-0.5 rounded text-[9px] font-black uppercase border" :class="getStatusBadgeClass(cita.estado)">
                                                    {{ cita.estado }}
                                                </span>
                                            </td>
                                        </tr>
                                        <tr v-if="historialCitas.length === 0">
                                            <td colspan="5" class="px-4 py-8 text-center text-slate-400 font-medium">No se registran citas pasadas.</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                    </div>
                </div>

                <!-- COLUMNA 3: SOLICITAR CITA FORM -->
                <div class="bg-white rounded-2xl border border-teal-100 p-6 shadow-sm space-y-6">
                    <div>
                        <h3 class="text-sm font-black text-teal-900 uppercase tracking-wider">Solicitar Consulta</h3>
                        <p class="text-xs text-slate-500 mt-0.5">Programa una nueva cita con nuestros especialistas.</p>
                    </div>

                    <form @submit.prevent="submitBooking" class="space-y-4">
                        <!-- Médico / Especialidad Selector -->
                        <div class="space-y-1.5">
                            <label class="text-[10px] font-bold text-slate-455 uppercase block">Médico / Especialidad</label>
                            <select
                                v-model="bookingForm.id_doctor"
                                class="block w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-slate-800 text-xs focus:outline-none focus:border-teal-500 focus:ring-4 focus:ring-teal-500/10 transition"
                            >
                                <option value="" disabled>Selecciona el médico...</option>
                                <option
                                    v-for="doc in doctores"
                                    :key="doc.id_doctor"
                                    :value="doc.id_doctor"
                                >
                                    {{ doc.nombre_completo }} ({{ doc.especialidad }})
                                </option>
                            </select>
                            <div v-if="bookingForm.errors.id_doctor" class="text-xs text-rose-600 font-semibold">{{ bookingForm.errors.id_doctor }}</div>
                        </div>

                        <!-- Fecha y Hora -->
                        <div class="grid grid-cols-2 gap-4">
                            <div class="space-y-1.5">
                                <label class="text-[10px] font-bold text-slate-455 uppercase block">Fecha</label>
                                <input
                                    type="date"
                                    v-model="bookingForm.fecha_cita"
                                    class="block w-full px-4 py-2 bg-slate-50 border border-slate-200 rounded-xl text-slate-800 text-xs focus:outline-none focus:border-teal-500 focus:ring-4 focus:ring-teal-500/10 transition"
                                />
                                <div v-if="bookingForm.errors.fecha_cita" class="text-xs text-rose-600 font-semibold">{{ bookingForm.errors.fecha_cita }}</div>
                            </div>
                            <div class="space-y-1.5">
                                <label class="text-[10px] font-bold text-slate-455 uppercase block">Hora</label>
                                <input
                                    type="time"
                                    v-model="bookingForm.hora_cita"
                                    class="block w-full px-4 py-2 bg-slate-50 border border-slate-200 rounded-xl text-slate-800 text-xs focus:outline-none focus:border-teal-500 focus:ring-4 focus:ring-teal-500/10 transition"
                                />
                                <div v-if="bookingForm.errors.hora_cita" class="text-xs text-rose-600 font-semibold">{{ bookingForm.errors.hora_cita }}</div>
                            </div>
                        </div>

                        <!-- Motivo -->
                        <div class="space-y-1.5">
                            <label class="text-[10px] font-bold text-slate-455 uppercase block">Síntomas / Motivo</label>
                            <textarea
                                rows="3"
                                placeholder="Describe brevemente tus síntomas o el motivo de tu consulta..."
                                v-model="bookingForm.motivo"
                                class="block w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-slate-800 text-xs focus:outline-none focus:border-teal-500 focus:ring-4 focus:ring-teal-500/10 transition resize-none"
                            ></textarea>
                            <div v-if="bookingForm.errors.motivo" class="text-xs text-rose-600 font-semibold">{{ bookingForm.errors.motivo }}</div>
                        </div>

                        <button
                            type="submit"
                            :disabled="bookingForm.processing || !bookingForm.id_doctor || !bookingForm.fecha_cita || !bookingForm.motivo"
                            class="w-full py-3 bg-gradient-to-r from-teal-500 to-emerald-600 hover:from-teal-600 hover:to-emerald-700 text-white rounded-xl text-xs font-black shadow-md shadow-teal-500/20 active:scale-[0.98] transition cursor-pointer disabled:opacity-50 disabled:cursor-not-allowed"
                        >
                            Solicitar Cita Médica
                        </button>
                    </form>
                </div>

            </div>

        </div>
    </AuthenticatedLayout>
</template>
