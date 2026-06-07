<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps({
    patient: {
        type: Object,
        required: true,
    },
});

const showDeleteModal = ref(false);
const activePrintData = ref(null);

// Print Prescription action
const printPrescription = (cita, seg) => {
    activePrintData.value = {
        paciente: `${props.patient.nombres} ${props.patient.apellidos}`,
        dui: props.patient.dui,
        edad: getAge(props.patient.fecha_nacimiento),
        fecha: new Date(cita.fecha_cita + 'T00:00:00').toLocaleDateString('es-ES', {day: 'numeric', month: 'long', year: 'numeric'}),
        doctor: cita.doctor ? `${cita.doctor.nombres} ${cita.doctor.apellidos}` : 'Alejandro Ortega',
        observaciones: seg.observaciones,
        tratamiento: seg.tratamiento,
        proxima_revision: seg.proxima_revision ? new Date(seg.proxima_revision + 'T00:00:00').toLocaleDateString('es-ES', {day: 'numeric', month: 'long', year: 'numeric'}) : null
    };
    setTimeout(() => {
        window.print();
    }, 200);
};

const getAge = (birthdate) => {
    if (!birthdate) return '';
    const parts = birthdate.split('-');
    if (parts.length !== 3) return '';
    const birthYear = parseInt(parts[0], 10);
    const birthMonth = parseInt(parts[1], 10);
    const birthDay = parseInt(parts[2], 10);

    const today = new Date();
    let age = today.getFullYear() - birthYear;
    const m = (today.getMonth() + 1) - birthMonth;
    if (m < 0 || (m === 0 && today.getDate() < birthDay)) {
        age--;
    }
    return `${age} años`;
};

// Delete patient request
const deletePatient = () => {
    router.delete(route('doctor.patients.destroy', props.patient.id_paciente), {
        onSuccess: () => {
            showDeleteModal.value = false;
        }
    });
};

// Helper for patient profile initials
const getInitials = (nombres, apellidos) => {
    const n = nombres ? nombres.split(' ')[0][0] : '';
    const a = apellidos ? apellidos.split(' ')[0][0] : '';
    return (n + a).toUpperCase();
};

// Helper for status badge colors
const getStatusClass = (status) => {
    switch (status) {
        case 'Atendida':
        case 'Completada':
        case 'Finalizado':
            return 'bg-emerald-50 text-emerald-700 border-emerald-250';
        case 'Confirmada':
        case 'En proceso':
            return 'bg-blue-50 text-blue-700 border-blue-250';
        case 'Pendiente':
            return 'bg-amber-50 text-amber-700 border-amber-250';
        case 'Cancelada':
            return 'bg-rose-50 text-rose-700 border-rose-250';
        default:
            return 'bg-slate-50 text-slate-700 border-slate-250';
    }
};
</script>

<template>
    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between w-full">
                <!-- Breadcrumbs -->
                <div class="flex items-center gap-2 text-sm text-slate-500 font-semibold">
                    <Link :href="route('doctor.patients.index')" class="hover:text-blue-600 transition">Pacientes</Link>
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-3.5 h-3.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5" />
                    </svg>
                    <span class="text-slate-800">Ficha #PAC-{{ String(patient.id_paciente).padStart(4, '0') }}</span>
                </div>
                
                <!-- Action Buttons -->
                <div class="flex items-center gap-3">
                    <Link
                        :href="route('doctor.patients.index')"
                        class="px-4 py-2.5 bg-white hover:bg-slate-50 border border-slate-200 text-slate-650 hover:text-slate-800 rounded-xl text-xs font-bold transition cursor-pointer"
                    >
                        Volver
                    </Link>
                    <button
                        @click="showDeleteModal = true"
                        class="px-4 py-2.5 bg-rose-50 hover:bg-rose-100 border border-rose-200 text-rose-700 rounded-xl text-xs font-bold transition cursor-pointer"
                    >
                        Eliminar Paciente
                    </button>
                    <Link
                        :href="route('doctor.patients.edit', patient.id_paciente)"
                        class="px-5 py-2.5 bg-gradient-to-r from-blue-600 to-indigo-650 hover:from-blue-700 hover:to-indigo-755 text-white rounded-xl text-xs font-bold shadow-md shadow-blue-500/20 active:scale-[0.98] transition cursor-pointer"
                    >
                        Editar Expediente
                    </Link>
                </div>
            </div>
        </template>

        <Head :title="`Ficha Paciente - ${patient.nombres} ${patient.apellidos}`" />

        <div class="select-none space-y-8 relative">
            
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 items-start">
                
                <!-- Columna Izquierda (Datos Médicos e Historial - 2/3) -->
                <div class="lg:col-span-2 space-y-6">
                    <!-- Ficha de Datos del Paciente -->
                    <div class="bg-white rounded-xl border border-slate-200 p-8 shadow-sm space-y-6">
                        <div>
                            <h3 class="text-base font-bold text-slate-800">Ficha de Datos Médicos</h3>
                            <p class="text-xs text-slate-450 mt-1">Información demográfica, filiación y registro clínico.</p>
                        </div>

                        <!-- Grid de Datos -->
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-6 bg-slate-50 rounded-xl p-6 border border-slate-100">
                            <div>
                                <span class="text-[9px] font-bold text-slate-400 uppercase tracking-wide block">Nombres</span>
                                <span class="text-sm font-bold text-slate-800">{{ patient.nombres }}</span>
                            </div>
                            <div>
                                <span class="text-[9px] font-bold text-slate-400 uppercase tracking-wide block">Apellidos</span>
                                <span class="text-sm font-bold text-slate-800">{{ patient.apellidos }}</span>
                            </div>
                            <div>
                                <span class="text-[9px] font-bold text-slate-400 uppercase tracking-wide block">DUI</span>
                                <span class="text-sm font-bold text-slate-700">{{ patient.dui || 'No registrado' }}</span>
                            </div>
                            <div>
                                <span class="text-[9px] font-bold text-slate-400 uppercase tracking-wide block">Teléfono</span>
                                <span class="text-sm font-bold text-slate-700">{{ patient.telefono }}</span>
                            </div>
                            <div>
                                <span class="text-[9px] font-bold text-slate-400 uppercase tracking-wide block">Fecha Nacimiento</span>
                                <span class="text-sm font-semibold text-slate-700">{{ new Date(patient.fecha_nacimiento + 'T00:00:00').toLocaleDateString('es-ES', {day: 'numeric', month: 'long', year: 'numeric'}) }}</span>
                            </div>
                            <div>
                                <span class="text-[9px] font-bold text-slate-400 uppercase tracking-wide block">Edad</span>
                                <span class="text-sm font-semibold text-slate-700">{{ getAge(patient.fecha_nacimiento) }}</span>
                            </div>
                            <div>
                                <span class="text-[9px] font-bold text-slate-400 uppercase tracking-wide block">Género</span>
                                <span class="text-sm font-semibold text-slate-700">{{ patient.sexo }}</span>
                            </div>
                            <div>
                                <span class="text-[9px] font-bold text-slate-400 uppercase tracking-wide block">Tipo de Sangre</span>
                                <span v-if="patient.tipo_sangre" class="px-2 py-0.5 rounded text-[10px] font-extrabold border bg-blue-50 text-blue-700 border-blue-200 w-max mt-1 block">
                                    {{ patient.tipo_sangre }}
                                </span>
                                <span v-else class="text-sm font-semibold text-slate-750">Desconocido</span>
                            </div>
                        </div>

                        <!-- Dirección Residencia -->
                        <div class="space-y-1">
                            <span class="text-[9px] font-bold text-slate-400 uppercase tracking-wide block">Dirección Residencia</span>
                            <span class="text-sm text-slate-750">{{ patient.direccion }}</span>
                        </div>

                        <!-- Alergias Conocidas (Banner con color de alerta si existen) -->
                        <div class="space-y-2">
                            <span class="text-[9px] font-bold text-slate-400 uppercase tracking-wide block">Alergias Conocidas</span>
                            <div v-if="patient.alergias" class="flex items-center gap-3 p-4 rounded-xl border border-rose-150 bg-rose-50/50 text-rose-800">
                                <div class="w-6 h-6 rounded-full bg-rose-100 flex items-center justify-center text-rose-700 flex-shrink-0">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-4 h-4">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" />
                                    </svg>
                                </div>
                                <span class="text-sm font-bold">{{ patient.alergias }}</span>
                            </div>
                            <div v-else class="flex items-center gap-3 p-4 rounded-xl border border-emerald-150 bg-emerald-50/40 text-emerald-800">
                                <div class="w-6 h-6 rounded-full bg-emerald-100 flex items-center justify-center text-emerald-700 flex-shrink-0">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-4 h-4">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                                    </svg>
                                </div>
                                <span class="text-sm font-bold text-emerald-700">Ninguna alergia registrada o conocida para este paciente.</span>
                            </div>
                        </div>

                        <!-- Observaciones o Notas del Expediente -->
                        <div class="space-y-1.5">
                            <span class="text-[9px] font-bold text-slate-400 uppercase tracking-wide block">Notas u Observaciones del Expediente</span>
                            <div class="p-4 bg-slate-50 border border-slate-150 rounded-xl text-slate-650 text-sm italic">
                                {{ patient.observaciones || 'No se han ingresado notas adicionales en el expediente.' }}
                            </div>
                        </div>
                    </div>

                    <!-- Historial Clínico del Paciente (Citas y Procedimientos) -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Historial de Citas -->
                        <div class="bg-white rounded-xl border border-slate-200 overflow-hidden shadow-sm flex flex-col min-h-[300px]">
                            <div class="p-6 border-b border-slate-200">
                                <h3 class="text-sm font-bold text-slate-800 uppercase tracking-wider">Historial de Citas</h3>
                                <p class="text-[11px] text-slate-450 mt-1">Consultas y citas programadas con el médico.</p>
                            </div>
                            <div class="divide-y divide-slate-100 overflow-y-auto flex-1 max-h-[350px]">
                                <div v-for="cita in patient.citas" :key="cita.id_cita" class="p-4 space-y-2 hover:bg-slate-50/50 transition">
                                    <div class="flex items-center justify-between">
                                        <span class="text-xs font-bold text-slate-550">{{ new Date(cita.fecha_cita + 'T00:00:00').toLocaleDateString('es-ES', {day: 'numeric', month: 'short', year: 'numeric'}) }} - {{ cita.hora_cita.substring(0, 5) }}</span>
                                        <span class="px-2 py-0.5 text-[9px] font-bold rounded-full border" :class="getStatusClass(cita.estado)">{{ cita.estado }}</span>
                                    </div>
                                    <p class="text-xs text-slate-650 font-medium line-clamp-2"><span class="font-bold text-slate-450">Motivo:</span> {{ cita.motivo }}</p>
                                    <div class="text-[10px] text-slate-400 font-semibold uppercase tracking-wide">
                                        Médico: Dr. {{ cita.doctor ? (cita.doctor.nombres + ' ' + cita.doctor.apellidos) : 'Roberto Méndez' }}
                                    </div>
                                    
                                    <!-- Seguimiento Clínico Asociado -->
                                    <div v-if="cita.seguimientos && cita.seguimientos.length > 0" class="mt-2.5 pt-2.5 border-t border-slate-100 bg-slate-50 p-2.5 rounded-lg space-y-2">
                                        <div class="text-[10px] font-extrabold text-blue-700 uppercase tracking-wider">Evolución Clínica & Receta</div>
                                        <div v-for="seg in cita.seguimientos" :key="seg.id_seguimiento" class="space-y-1">
                                            <p class="text-[11px] text-slate-600 leading-relaxed font-semibold"><span class="text-slate-450 font-extrabold">Observación:</span> {{ seg.observaciones }}</p>
                                            <p class="text-[11px] text-slate-600 leading-relaxed font-semibold"><span class="text-slate-450 font-extrabold">Tratamiento:</span> {{ seg.tratamiento }}</p>
                                            <p v-if="seg.proxima_revision" class="text-[9px] text-indigo-600 font-extrabold">Próxima revisión: {{ new Date(seg.proxima_revision + 'T00:00:00').toLocaleDateString('es-ES', {day: 'numeric', month: 'short', year: 'numeric'}) }}</p>
                                            <button 
                                                @click="printPrescription(cita, seg)"
                                                class="mt-2 text-[10px] font-extrabold text-blue-600 hover:text-blue-700 flex items-center gap-1 cursor-pointer"
                                            >
                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-3.5 h-3.5">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6.72 13.82l-.24-2.22m13.7 2.22l.24-2.22m-13.94 0h13.94M12 3v18m-9-9h18" />
                                                </svg>
                                                Imprimir Receta
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                <div v-if="patient.citas.length === 0" class="p-8 text-center text-slate-400 font-medium text-xs flex-1 flex items-center justify-center">
                                    No hay citas registradas para este paciente.
                                </div>
                            </div>
                        </div>

                        <!-- Historial de Procedimientos -->
                        <div class="bg-white rounded-xl border border-slate-200 overflow-hidden shadow-sm flex flex-col min-h-[300px]">
                            <div class="p-6 border-b border-slate-200">
                                <h3 class="text-sm font-bold text-slate-800 uppercase tracking-wider">Procedimientos Médicos</h3>
                                <p class="text-[11px] text-slate-450 mt-1">Tratamientos o controles asignados al paciente.</p>
                            </div>
                            <div class="divide-y divide-slate-100 overflow-y-auto flex-1 max-h-[350px]">
                                <div v-for="proc in patient.procedimientos" :key="proc.id_procedimiento" class="p-4 space-y-2 hover:bg-slate-50/50 transition">
                                    <div class="flex items-center justify-between">
                                        <span class="text-xs font-bold text-slate-550">{{ new Date(proc.fecha_procedimiento + 'T00:00:00').toLocaleDateString('es-ES', {day: 'numeric', month: 'short', year: 'numeric'}) }}</span>
                                        <span class="px-2 py-0.5 text-[9px] font-bold rounded-full border" :class="getStatusClass(proc.estado)">{{ proc.estado }}</span>
                                    </div>
                                    <h4 class="text-xs font-bold text-slate-800">{{ proc.nombre_procedimiento }}</h4>
                                    <p class="text-xs text-slate-500 line-clamp-2">{{ proc.descripcion }}</p>
                                    <div class="text-[10px] text-slate-450 font-semibold uppercase tracking-wide">
                                        Responsable: Dr. {{ proc.doctor ? (proc.doctor.nombres + ' ' + proc.doctor.apellidos) : 'Roberto Méndez' }}
                                    </div>
                                </div>
                                <div v-if="patient.procedimientos.length === 0" class="p-8 text-center text-slate-400 font-medium text-xs flex-1 flex items-center justify-center">
                                    No hay procedimientos registrados para este paciente.
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Columna Derecha (Foto y Contacto de Emergencia - 1/3) -->
                <div class="space-y-6">
                    <!-- Foto y Metadatos del Expediente -->
                    <div class="bg-white rounded-xl border border-slate-200 p-6 flex flex-col items-center shadow-sm">
                        <!-- Photo Box (Initials) -->
                        <div class="w-28 h-28 rounded-full border-4 border-slate-100 bg-slate-50 flex items-center justify-center text-slate-400 relative overflow-hidden shadow-inner mb-4">
                            <div class="absolute inset-0 bg-gradient-to-tr from-blue-500/10 to-indigo-500/10 border border-blue-200 flex items-center justify-center font-black text-2xl text-blue-700">
                                {{ getInitials(patient.nombres, patient.apellidos) }}
                            </div>
                        </div>
                        <span class="text-xs font-semibold text-slate-400 uppercase tracking-wide">FOTO DEL PACIENTE</span>
                        
                        <!-- Expediente Tag -->
                        <div class="mt-6 w-full pt-6 border-t border-slate-100 flex flex-col items-center space-y-2">
                            <span class="text-[11px] font-bold text-slate-400 tracking-wider">ID EXPEDIENTE</span>
                            <span class="px-3 py-1 bg-blue-50 text-blue-700 font-extrabold text-xs rounded border border-blue-200 uppercase tracking-widest">
                                #PAC-{{ String(patient.id_paciente).padStart(4, '0') }}
                            </span>
                            <div class="flex flex-col items-center pt-3 text-[10px] text-slate-400 space-y-1 font-semibold">
                                <span>Registrado por: Dr. Roberto Méndez</span>
                                <span>Fecha: {{ new Date(patient.created_at).toLocaleDateString('es-ES', {day: 'numeric', month: 'short', year: 'numeric'}) }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Contacto de Emergencia Card -->
                    <div class="bg-white rounded-xl border border-slate-200 p-6 shadow-sm space-y-5">
                        <div>
                            <h3 class="text-sm font-bold text-slate-800 flex items-center gap-2">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5 text-emerald-600">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M18 18.72a9.094 9.094 0 003.741-.479 3 3 0 00-4.682-2.72m.94 3.198.001.031c0 .225-.012.447-.037.666A11.944 11.944 0 0112 21c-2.17 0-4.207-.576-5.963-1.584A6.062 6.062 0 016 18.719m12 0a5.971 5.971 0 00-.941-3.197m0 0A5.995 5.995 0 0012 12.75a5.995 5.995 0 00-5.058 2.772m0 0a3 3 0 00-4.681 2.72 8.986 8.986 0 003.74.477m.94-3.197a5.971 5.971 0 00-.94 3.197" />
                                </svg>
                                Contacto de Emergencia
                            </h3>
                            <p class="text-[11px] text-slate-450 mt-1">Familiar o tutor responsable del paciente.</p>
                        </div>

                        <div class="space-y-4 bg-slate-50 border border-slate-100 rounded-xl p-4 text-xs font-semibold">
                            <!-- Nombre del Contacto -->
                            <div>
                                <span class="text-[9px] font-bold text-slate-400 uppercase tracking-wide block mb-0.5">Nombre Completo</span>
                                <span class="text-sm font-bold text-slate-800">{{ patient.contacto_emergencia_nombre || 'No registrado' }}</span>
                            </div>

                            <!-- Parentesco -->
                            <div>
                                <span class="text-[9px] font-bold text-slate-400 uppercase tracking-wide block mb-0.5">Parentesco / Relación</span>
                                <span class="text-xs font-bold text-slate-700">{{ patient.contacto_emergencia_parentesco || 'No registrado' }}</span>
                            </div>

                            <!-- Teléfono del Contacto -->
                            <div>
                                <span class="text-[9px] font-bold text-slate-400 uppercase tracking-wide block mb-0.5">Teléfono de Contacto</span>
                                <span class="text-xs font-bold text-slate-700">{{ patient.contacto_emergencia_telefono || 'No registrado' }}</span>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

            <!-- Custom Delete Confirmation Modal (Glassmorphism design) -->
            <div v-if="showDeleteModal" class="fixed inset-0 z-50 overflow-y-auto flex items-center justify-center p-6 bg-slate-900/60 backdrop-blur-sm select-none">
                <div class="relative bg-white max-w-sm w-full rounded-2xl p-6 shadow-2xl border border-slate-100 space-y-6">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-full bg-rose-50 text-rose-600 flex items-center justify-center flex-shrink-0">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-6 h-6">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" />
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-base font-bold text-slate-800">¿Eliminar Expediente?</h3>
                            <p class="text-xs text-slate-450 mt-0.5">Esta acción no se puede deshacer.</p>
                        </div>
                    </div>
                    
                    <p class="text-xs text-slate-650 leading-relaxed">
                        ¿Estás seguro de que deseas eliminar permanentemente el expediente clínico de <strong>{{ patient.nombres }} {{ patient.apellidos }}</strong>? Se borrarán todos sus registros asociados.
                    </p>

                    <div class="flex items-center justify-end gap-3">
                        <button
                            @click="showDeleteModal = false"
                            class="px-4 py-2 bg-slate-100 hover:bg-slate-200 border border-slate-200 text-slate-650 hover:text-slate-800 rounded-lg text-xs font-bold transition cursor-pointer"
                        >
                            Cancelar
                        </button>
                        <button
                            @click="deletePatient"
                            class="px-4 py-2 bg-rose-600 hover:bg-rose-700 text-white rounded-lg text-xs font-bold transition cursor-pointer"
                        >
                            Sí, Eliminar
                        </button>
                    </div>
                </div>
            </div>

        </div>

        <!-- Print Prescription Layout (hidden on screen, visible on print) -->
        <div v-if="activePrintData" id="print-prescription-section" class="hidden print:block fixed inset-0 bg-white p-12 text-slate-800 text-left font-sans z-[999999]">
            <!-- Letterhead / Membrete -->
            <div class="flex items-center justify-between border-b-2 border-blue-600 pb-6 mb-8">
                <div>
                    <h1 class="text-3xl font-extrabold tracking-tight text-blue-600">ProDoctor</h1>
                    <p class="text-xs text-slate-400 font-bold uppercase tracking-widest mt-0.5">Gabinete Médico Profesional</p>
                </div>
                <div class="text-right text-xs text-slate-500 font-semibold space-y-0.5">
                    <p>📍 Av. de la Salud #450, Piso 5</p>
                    <p>📞 +52 (55) 5489-7700</p>
                    <p>✉️ contacto@prodoctor.com</p>
                </div>
            </div>

            <!-- Title -->
            <div class="text-center mb-8">
                <h2 class="text-lg font-bold uppercase tracking-wider text-slate-700">Receta Médica y Evolución</h2>
            </div>

            <!-- Patient / Doctor Info Grid -->
            <div class="grid grid-cols-2 gap-6 bg-slate-50 border border-slate-200 rounded-xl p-6 mb-8 text-xs">
                <div>
                    <p class="text-[10px] font-bold text-slate-400 uppercase">Paciente</p>
                    <p class="text-sm font-bold text-slate-800 mt-0.5">{{ activePrintData.paciente }}</p>
                    <p class="text-slate-500 mt-1">DUI: {{ activePrintData.dui || 'No registrado' }} | Edad: {{ activePrintData.edad }}</p>
                </div>
                <div>
                    <p class="text-[10px] font-bold text-slate-400 uppercase">Médico Tratante</p>
                    <p class="text-sm font-bold text-slate-800 mt-0.5">Dr. {{ activePrintData.doctor }}</p>
                    <p class="text-slate-500 mt-1">Fecha de Emisión: {{ activePrintData.fecha }}</p>
                </div>
            </div>

            <!-- Content -->
            <div class="space-y-8 min-h-[350px]">
                <div>
                    <h3 class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">Diagnóstico / Observaciones</h3>
                    <p class="text-sm text-slate-700 leading-relaxed bg-white border border-slate-200 rounded-xl p-4 min-h-[80px] font-medium">
                        {{ activePrintData.observaciones }}
                    </p>
                </div>

                <div>
                    <h3 class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">Tratamiento y Prescripción Médica</h3>
                    <p class="text-sm text-slate-700 leading-relaxed bg-white border border-slate-200 rounded-xl p-4 min-h-[120px] whitespace-pre-line font-medium">
                        {{ activePrintData.tratamiento }}
                    </p>
                </div>

                <div v-if="activePrintData.proxima_revision">
                    <h3 class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">Próxima Revisión Programada</h3>
                    <p class="text-sm text-indigo-700 font-bold bg-indigo-50 border border-indigo-150 rounded-xl p-3.5 w-max">
                        📅 {{ activePrintData.proxima_revision }}
                    </p>
                </div>
            </div>

            <!-- Signature and stamp -->
            <div class="mt-20 flex flex-col items-center">
                <div class="w-64 border-t border-slate-350 text-center pt-3 text-xs font-semibold text-slate-500">
                    <p class="font-bold text-slate-700">Dr. {{ activePrintData.doctor }}</p>
                    <p>Firma y Sello Médico</p>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<style>
@media print {
    body * {
        visibility: hidden !important;
    }
    #print-prescription-section,
    #print-prescription-section * {
        visibility: visible !important;
    }
    #print-prescription-section {
        position: absolute !important;
        left: 0 !important;
        top: 0 !important;
        width: 100% !important;
        background: white !important;
        color: black !important;
    }
}
</style>
