<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm, usePage } from '@inertiajs/vue3';
import { ref, computed, watch, onMounted } from 'vue';
import UpdateProfileInformationForm from '@/Pages/Profile/Partials/UpdateProfileInformationForm.vue';
import UpdatePasswordForm from '@/Pages/Profile/Partials/UpdatePasswordForm.vue';
import axios from 'axios';

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
    mustVerifyEmail: {
        type: Boolean,
        default: false,
    },
    status: {
        type: String,
        default: '',
    },
});

// Navigation tabs
const activeMainTab = ref('resumen'); // 'resumen', 'agendar', 'expediente', 'historial', 'perfil'

// Watch parameter 'tab' to dynamically select current tab
const checkTabParam = () => {
    const urlParams = new URLSearchParams(window.location.search);
    const tab = urlParams.get('tab');
    if (tab && ['resumen', 'agendar', 'expediente', 'historial', 'perfil'].includes(tab)) {
        activeMainTab.value = tab;
    }
};

onMounted(() => {
    checkTabParam();
});

// Watch Inertia page visiting URLs (e.g. from sidebar links) to update tab reactively
watch(() => usePage().url, () => {
    checkTabParam();
});

// Booking state & wizard
const bookingStep = ref(1); // 1 = Médico y Fecha, 2 = Síntomas y Detalles, 3 = Confirmación
const selectedDoctor = ref('');
const availableSlots = ref([]);
const availableDates = ref([]);
const selectedDate = ref('');
const selectedSlot = ref(null);
const isLoadingSlots = ref(false);

const selectedSymptoms = ref([]);
const bookingDetails = ref('');

const symptomsCategories = [
    {
        name: 'Síntomas Generales',
        symptoms: ['Fiebre / Escalofríos', 'Fatiga / Cansancio extremo', 'Dolor de cabeza / Migraña', 'Pérdida de apetito']
    },
    {
        name: 'Respiratorios',
        symptoms: ['Tos seca / productiva', 'Dolor de garganta', 'Dificultad respiratoria', 'Congestión nasal']
    },
    {
        name: 'Gastrointestinales',
        symptoms: ['Dolor de estómago / Cólicos', 'Náuseas / Vómitos', 'Diarrea / Indigestión']
    },
    {
        name: 'Musculares y Cutáneos',
        symptoms: ['Dolor muscular / articular', 'Erupción cutánea / Picazón', 'Dolor de espalda']
    },
    {
        name: 'Cardiovasculares y Neurológicos',
        symptoms: ['Presión en el pecho / Palpitaciones', 'Presión arterial alta', 'Mareos / Desmayos', 'Entumecimiento']
    }
];

watch(selectedDoctor, (newDocId) => {
    selectedDate.value = '';
    selectedSlot.value = null;
    availableSlots.value = [];
    availableDates.value = [];
    if (!newDocId) return;

    isLoadingSlots.value = true;
    axios.get(route('paciente.doctors.availability', newDocId))
        .then(response => {
            availableSlots.value = response.data;
            const dates = [...new Set(response.data.map(slot => slot.fecha))];
            availableDates.value = dates.sort();
        })
        .catch(err => {
            console.error('Error fetching availability:', err);
            alert('Error al cargar los horarios disponibles para este médico.');
        })
        .finally(() => {
            isLoadingSlots.value = false;
        });
});

const getTimesForDate = (date) => {
    return availableSlots.value.filter(slot => slot.fecha === date);
};

const selectTimeSlot = (slot) => {
    selectedSlot.value = slot;
};

// Forms
const bookingForm = useForm({
    id_doctor: '',
    id_disponibilidad: '',
    sintomas: [],
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

const isEditingProfile = ref(false);
const phoneNumber = ref('');

const formatPhoneNumber = (event) => {
    let raw = event.target.value.replace(/\D/g, ''); // Keep digits only
    raw = raw.substring(0, 8); // Cap at 8 digits

    let formatted = '';
    if (raw.length > 4) {
        formatted = `${raw.substring(0, 4)}-${raw.substring(4)}`;
    } else {
        formatted = raw;
    }
    
    phoneNumber.value = formatted;
    profileForm.telefono = `+503 ${formatted}`;
    profileForm.errors.telefono = null;
};

const startEditing = () => {
    profileForm.nombres = props.patient.nombres || '';
    profileForm.apellidos = props.patient.apellidos || '';
    profileForm.dui = props.patient.dui || '';
    profileForm.fecha_nacimiento = props.patient.fecha_nacimiento || '';
    profileForm.sexo = props.patient.sexo || 'Otro';
    profileForm.direccion = props.patient.direccion || '';
    
    let rawPhone = props.patient.telefono || '';
    if (rawPhone.startsWith('+503 ')) {
        phoneNumber.value = rawPhone.replace('+503 ', '');
    } else {
        phoneNumber.value = rawPhone;
    }
    profileForm.telefono = props.patient.telefono || '';
    isEditingProfile.value = true;
};

const cancelEditing = () => {
    isEditingProfile.value = false;
};

const submitBooking = () => {
    if (!selectedDoctor.value || !selectedSlot.value) {
        alert('Por favor selecciona un médico y un horario disponible.');
        return;
    }

    bookingForm.id_doctor = selectedDoctor.value;
    bookingForm.id_disponibilidad = selectedSlot.value.id_disponibilidad;
    bookingForm.sintomas = selectedSymptoms.value;
    bookingForm.motivo = bookingDetails.value;

    bookingForm.post(route('paciente.appointments.store'), {
        onSuccess: () => {
            selectedDoctor.value = '';
            selectedDate.value = '';
            selectedSlot.value = null;
            selectedSymptoms.value = [];
            bookingDetails.value = '';
            bookingStep.value = 1;
            bookingForm.reset();
            alert('¡Tu cita médica ha sido agendada con éxito!');
            activeMainTab.value = 'resumen';
        }
    });
};

const submitProfileUpdate = () => {
    const rawDigits = phoneNumber.value.replace(/\D/g, '');
    if (!phoneNumber.value) {
        profileForm.errors.telefono = 'El teléfono es obligatorio.';
        return;
    } else if (rawDigits.length < 8) {
        profileForm.errors.telefono = 'El teléfono debe tener exactamente 8 dígitos.';
        return;
    }
    
    profileForm.telefono = `+503 ${phoneNumber.value}`;

    profileForm.put(route('paciente.profile.update'), {
        onSuccess: () => {
            isEditingProfile.value = false;
        }
    });
};

const toggleSymptom = (symptom) => {
    const idx = selectedSymptoms.value.indexOf(symptom);
    if (idx >= 0) {
        selectedSymptoms.value.splice(idx, 1);
    } else {
        selectedSymptoms.value.push(symptom);
    }
};

const getStatusBadgeClass = (status) => {
    switch (status) {
        case 'Atendida':
        case 'Finalizado':
            return 'bg-emerald-500/10 text-emerald-400 border-emerald-500/20';
        case 'Confirmada':
        case 'En proceso':
            return 'bg-teal-500/10 text-[#00dfb2] border-teal-500/20';
        case 'Pendiente':
            return 'bg-amber-500/10 text-amber-400 border-amber-500/20';
        case 'Cancelada':
            return 'bg-rose-500/10 text-rose-400 border-rose-500/20 opacity-70';
        default:
            return 'bg-slate-500/10 text-slate-400 border-slate-500/20';
    }
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

const getDoctorName = (docId) => {
    const doc = props.doctores.find(d => d.id_doctor === docId);
    return doc ? doc.nombre_completo : '';
};
</script>

<template>
    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-extrabold text-[#00dfb2] tracking-tight">Mi Portal Médico</h2>
            <p class="text-xs text-slate-400 mt-0.5">Bienvenido, {{ patient.nombres }} {{ patient.apellidos }}. Revisa tus citas y expediente clínico.</p>
        </template>

        <template #sidebar-paciente>
            <div class="pl-4 py-1 flex flex-col gap-1.5 ml-4 border-l border-[#13283f] mt-1">
                <button
                    @click="activeMainTab = 'resumen'"
                    class="flex items-center gap-2.5 px-3 py-2 rounded-xl text-xs font-bold transition duration-150 text-left cursor-pointer"
                    :class="activeMainTab === 'resumen' ? 'bg-[#00dfb2]/10 text-[#00dfb2]' : 'text-slate-400 hover:bg-[#040a12]/50 hover:text-white'"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 6a7.5 7.5 0 1 0 7.5 7.5h-7.5V6Z" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 10.5H21A7.5 7.5 0 0 0 13.5 3v7.5Z" />
                    </svg>
                    Resumen
                </button>
                <button
                    @click="activeMainTab = 'agendar'"
                    class="flex items-center gap-2.5 px-3 py-2 rounded-xl text-xs font-bold transition duration-150 text-left cursor-pointer"
                    :class="activeMainTab === 'agendar' ? 'bg-[#00dfb2]/10 text-[#00dfb2]' : 'text-slate-400 hover:bg-[#040a12]/50 hover:text-white'"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5" />
                    </svg>
                    Agendar Cita
                </button>
                <button
                    @click="activeMainTab = 'expediente'"
                    class="flex items-center gap-2.5 px-3 py-2 rounded-xl text-xs font-bold transition duration-150 text-left cursor-pointer"
                    :class="activeMainTab === 'expediente' ? 'bg-[#00dfb2]/10 text-[#00dfb2]' : 'text-slate-400 hover:bg-[#040a12]/50 hover:text-white'"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" />
                    </svg>
                    Expediente
                </button>
                <button
                    @click="activeMainTab = 'historial'"
                    class="flex items-center gap-2.5 px-3 py-2 rounded-xl text-xs font-bold transition duration-150 text-left cursor-pointer"
                    :class="activeMainTab === 'historial' ? 'bg-[#00dfb2]/10 text-[#00dfb2]' : 'text-slate-400 hover:bg-[#040a12]/50 hover:text-white'"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                    </svg>
                    Historial
                </button>
                <button
                    @click="activeMainTab = 'perfil'"
                    class="flex items-center gap-2.5 px-3 py-2 rounded-xl text-xs font-bold transition duration-150 text-left cursor-pointer"
                    :class="activeMainTab === 'perfil' ? 'bg-[#00dfb2]/10 text-[#00dfb2]' : 'text-slate-400 hover:bg-[#040a12]/50 hover:text-white'"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0zM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632z" />
                    </svg>
                    Perfil
                </button>
            </div>
        </template>

        <Head title="Mi Portal Médico - ProDoctor" />

        <div class="space-y-6 select-none">

            <!-- TAB 1: RESUMEN DE SALUD -->
            <div v-if="activeMainTab === 'resumen'" class="space-y-6">
                <!-- PRÓXIMA CITA BANNER -->
                <div
                    v-if="proximaCita"
                    class="bg-gradient-to-r from-teal-500 to-emerald-600 text-[#030914] rounded-2xl p-6 shadow-lg flex flex-col md:flex-row items-center justify-between gap-6 relative overflow-hidden"
                >
                    <div class="absolute inset-0 opacity-10 pointer-events-none flex items-center justify-center">
                        <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="w-full h-48 stroke-white stroke-2">
                            <path d="M2 12h4.5l1.5-4 2.5 8 1.5-5 1.5 1H22" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </div>

                    <div class="flex items-center gap-4 z-10">
                        <div class="w-12 h-12 rounded-xl bg-white/20 flex items-center justify-center flex-shrink-0 text-slate-900">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-6 h-6">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25" />
                            </svg>
                        </div>
                        <div>
                            <span class="text-[10px] font-black uppercase tracking-wider text-teal-900/80">Próxima Consulta Programada</span>
                            <h3 class="text-lg font-black tracking-tight mt-0.5 text-slate-950">{{ proximaCita.doctor_nombre }}</h3>
                            <p class="text-xs text-slate-900 font-bold mt-1">{{ proximaCita.motivo }}</p>
                        </div>
                    </div>

                    <div class="text-center md:text-right z-10">
                        <div class="text-2xl font-black text-slate-950">{{ new Date(proximaCita.fecha + 'T00:00:00').toLocaleDateString('es-ES', { weekday: 'long', day: 'numeric', month: 'long' }) }}</div>
                        <div class="text-sm text-slate-900 font-extrabold mt-1">Horario: {{ proximaCita.hora }} hrs</div>
                        <span class="inline-block mt-3 px-3 py-1 bg-[#030914]/20 text-slate-900 font-extrabold text-[10px] uppercase rounded-full border border-black/10">
                            Estado: {{ proximaCita.estado }}
                        </span>
                    </div>
                </div>

                <div v-else class="bg-[#07111e] rounded-2xl border border-[#13283f] p-6 flex flex-col md:flex-row items-center justify-between gap-4 shadow-sm">
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 rounded-xl bg-teal-500/10 text-[#00dfb2] flex items-center justify-center flex-shrink-0">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-6 h-6">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15.182 15.182a4.5 4.5 0 01-6.364 0M21 12a9 9 0 11-18 0 9 9 0 0118 0zM9.75 9.75c0 .414-.168.75-.375.75S9 10.164 9 9.75 9.168 9 9.375 9s.375.336.375.75zm-.375 0h.008v.015h-.008V9.75zm5.625 0c0 .414-.168.75-.375.75s-.375-.336-.375-.75.168-.75.375-.75.375.336.375.75zm-.375 0h.008v.015h-.008V9.75z" />
                            </svg>
                        </div>
                        <div>
                            <h4 class="font-bold text-slate-200 text-sm">No tienes consultas médicas próximas</h4>
                            <p class="text-xs text-slate-550 mt-0.5">Si requieres de una revisión o control médico puedes agendar una nueva consulta libremente.</p>
                        </div>
                    </div>
                    <button
                        @click="activeMainTab = 'agendar'"
                        class="px-4 py-2 bg-gradient-to-r from-[#00dfb2] to-[#00a887] text-slate-950 text-xs font-black rounded-xl hover:from-[#00ffd5] hover:to-[#00c79f] shadow-md transition cursor-pointer"
                    >
                        Solicitar Cita
                    </button>
                </div>

                <!-- METRIC CARDS AND PROFILE SNAPSHOT -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <!-- Ficha Rápida del Paciente -->
                    <div class="bg-[#07111e] rounded-2xl border border-[#13283f] p-6 space-y-4">
                        <div class="flex items-center gap-3 border-b border-[#13283f] pb-3">
                            <div class="w-10 h-10 rounded-full bg-[#040a12] border border-[#162d4a] flex items-center justify-center font-bold text-[#00dfb2] overflow-hidden">
                                <img v-if="$page.props.auth.user.foto" :src="'/' + $page.props.auth.user.foto" alt="Avatar" class="w-full h-full object-cover" />
                                <span v-else>{{ patient.nombres[0] }}{{ patient.apellidos ? patient.apellidos[0] : '' }}</span>
                            </div>
                            <div>
                                <h4 class="text-xs font-bold text-white leading-tight">{{ patient.nombres }} {{ patient.apellidos }}</h4>
                                <span class="text-[9px] text-[#00dfb2] font-black uppercase tracking-wider block mt-0.5">Expediente #PAC-{{ String(patient.id_paciente).padStart(4, '0') }}</span>
                            </div>
                        </div>
                        <div class="space-y-2.5 text-xs font-semibold text-slate-400">
                            <div class="flex justify-between">
                                <span>Edad:</span>
                                <span class="text-slate-200">{{ getAge(patient.fecha_nacimiento) || 'No registrada' }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span>Tipo de Sangre:</span>
                                <span class="text-[#00dfb2] font-extrabold px-1.5 py-0.5 bg-teal-500/10 rounded">{{ patient.tipo_sangre || 'O+' }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span>Teléfono:</span>
                                <span class="text-slate-200">{{ patient.telefono }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span>Género:</span>
                                <span class="text-slate-200">{{ patient.sexo }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Métricas de Uso de Portal -->
                    <div class="md:col-span-2 grid grid-cols-2 gap-4">
                        <div class="bg-[#07111e] rounded-2xl border border-[#13283f] p-5 flex flex-col justify-between hover:border-slate-700 transition">
                            <div class="text-[10px] font-black uppercase tracking-wider text-slate-500">Citas Programadas</div>
                            <div class="text-3xl font-black text-white mt-3">{{ historialCitas.length }}</div>
                            <span class="text-[10px] text-[#00dfb2] font-semibold mt-2 block">Total en historial</span>
                        </div>
                        <div class="bg-[#07111e] rounded-2xl border border-[#13283f] p-5 flex flex-col justify-between hover:border-slate-700 transition">
                            <div class="text-[10px] font-black uppercase tracking-wider text-slate-500">Recetas & Seguimientos</div>
                            <div class="text-3xl font-black text-white mt-3">{{ seguimientos.length }}</div>
                            <span class="text-[10px] text-[#00dfb2] font-semibold mt-2 block">Tratamientos asignados</span>
                        </div>
                        <div class="bg-[#07111e] rounded-2xl border border-[#13283f] p-5 flex flex-col justify-between hover:border-slate-700 transition">
                            <div class="text-[10px] font-black uppercase tracking-wider text-slate-500">Procedimientos Clínicos</div>
                            <div class="text-3xl font-black text-white mt-3">{{ procedimientos.length }}</div>
                            <span class="text-[10px] text-[#00dfb2] font-semibold mt-2 block">Chequeos ejecutados</span>
                        </div>
                        <div class="bg-[#07111e] rounded-2xl border border-[#13283f] p-5 flex flex-col justify-between hover:border-slate-700 transition">
                            <div class="text-[10px] font-black uppercase tracking-wider text-slate-500">Médicos Disponibles</div>
                            <div class="text-3xl font-black text-white mt-3">{{ doctores.length }}</div>
                            <span class="text-[10px] text-[#00dfb2] font-semibold mt-2 block">Especialidades activas</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- TAB 2: AGENDAR NUEVA CITA (Wizard step-by-step) -->
            <div v-if="activeMainTab === 'agendar'" class="bg-[#07111e] rounded-2xl border border-[#13283f] p-8 shadow-sm space-y-6 max-w-3xl mx-auto">
                <!-- Wizard progress indicator -->
                <div class="flex items-center justify-center gap-3 mb-6 text-xs font-semibold select-none border-b border-[#13283f] pb-6">
                    <div class="flex flex-col items-center">
                        <div class="w-8 h-8 rounded-full flex items-center justify-center font-bold transition duration-200"
                             :class="bookingStep === 1 ? 'bg-gradient-to-r from-[#00dfb2] to-[#00a887] text-slate-950 shadow-md shadow-[#00dfb2]/10' : 'bg-teal-500/10 text-[#00dfb2] border border-[#00dfb2]/20'">
                            1
                        </div>
                        <span class="mt-1.5 text-[9px] uppercase tracking-wider font-bold" :class="bookingStep === 1 ? 'text-[#00dfb2]' : 'text-slate-500'">Especialista y Fecha</span>
                    </div>
                    <div class="h-[1px] w-12 bg-[#162d4a] self-center -mt-4"></div>
                    
                    <div class="flex flex-col items-center">
                        <div class="w-8 h-8 rounded-full flex items-center justify-center font-bold transition duration-200"
                             :class="bookingStep === 2 ? 'bg-gradient-to-r from-[#00dfb2] to-[#00a887] text-slate-950 shadow-md shadow-[#00dfb2]/10' : (bookingStep > 2 ? 'bg-teal-500/10 text-[#00dfb2] border border-[#00dfb2]/20' : 'bg-[#040a12] border border-[#162d4a] text-slate-500')">
                            2
                        </div>
                        <span class="mt-1.5 text-[9px] uppercase tracking-wider font-bold" :class="bookingStep === 2 ? 'text-[#00dfb2]' : 'text-slate-500'">Síntomas y Detalles</span>
                    </div>
                    <div class="h-[1px] w-12 bg-[#162d4a] self-center -mt-4"></div>

                    <div class="flex flex-col items-center">
                        <div class="w-8 h-8 rounded-full flex items-center justify-center font-bold transition duration-200"
                             :class="bookingStep === 3 ? 'bg-gradient-to-r from-[#00dfb2] to-[#00a887] text-slate-950 shadow-md shadow-[#00dfb2]/10' : 'bg-[#040a12] border border-[#162d4a] text-slate-500'">
                            3
                        </div>
                        <span class="mt-1.5 text-[9px] uppercase tracking-wider font-bold" :class="bookingStep === 3 ? 'text-[#00dfb2]' : 'text-slate-500'">Confirmación</span>
                    </div>
                </div>

                <!-- STEP 1: SELECTOR DE DOCTOR Y FECHA -->
                <div v-if="bookingStep === 1" class="space-y-6">
                    <div class="space-y-2">
                        <label class="text-[10px] font-bold text-[#00dfb2] uppercase tracking-widest block">1. Selecciona el Médico Especialista <span class="text-rose-500">*</span></label>
                        <select
                            v-model="selectedDoctor"
                            class="block w-full px-4 py-3 bg-[#040a12] border border-[#162d4a] rounded-xl text-white text-xs focus:outline-none focus:border-[#00dfb2] transition cursor-pointer"
                        >
                            <option value="" disabled>Selecciona el médico especialista...</option>
                            <option
                                v-for="doc in doctores"
                                :key="doc.id_doctor"
                                :value="doc.id_doctor"
                            >
                                {{ doc.nombre_completo }} ({{ doc.especialidad }})
                            </option>
                        </select>
                    </div>

                    <!-- Dynamic slots loading -->
                    <div v-if="selectedDoctor" class="space-y-4 pt-4 border-t border-[#13283f]">
                        <label class="text-[10px] font-bold text-[#00dfb2] uppercase tracking-widest block">2. Horarios Libres de Consulta <span class="text-rose-500">*</span></label>
                        
                        <div v-if="isLoadingSlots" class="text-center py-6 text-slate-500 text-xs font-semibold">
                            Cargando horarios de la agenda del médico...
                        </div>
                        
                        <div v-else-if="availableDates.length === 0" class="p-4 rounded-xl bg-amber-500/5 border border-amber-500/20 text-xs font-semibold text-amber-400 text-center">
                            ⚠️ Este médico no tiene horarios disponibles en la agenda actualmente. Intenta con otro especialista.
                        </div>

                        <div v-else class="space-y-4">
                            <!-- Fecha Selector -->
                            <div class="space-y-1.5">
                                <span class="text-[10px] font-bold text-slate-500 uppercase block">Selecciona la fecha disponible:</span>
                                <div class="flex flex-wrap gap-2">
                                    <button
                                        v-for="date in availableDates"
                                        :key="date"
                                        type="button"
                                        @click="selectedDate = date; selectedSlot = null"
                                        class="px-3.5 py-2.5 border rounded-lg text-xs font-bold transition duration-150 cursor-pointer"
                                        :class="selectedDate === date
                                            ? 'bg-emerald-500/10 border-[#00dfb2] text-[#00dfb2]'
                                            : 'bg-[#040a12] border-[#162d4a] text-slate-400 hover:text-white hover:border-slate-500'"
                                    >
                                        {{ new Date(date + 'T00:00:00').toLocaleDateString('es-ES', { day: 'numeric', month: 'short', weekday: 'short' }) }}
                                    </button>
                                </div>
                            </div>

                            <!-- Hora Slots Grid -->
                            <div v-if="selectedDate" class="space-y-2 pt-2 border-t border-[#13283f]/40">
                                <span class="text-[10px] font-bold text-slate-500 uppercase block">Selecciona la hora de la consulta:</span>
                                <div class="grid grid-cols-3 sm:grid-cols-4 gap-2">
                                    <button
                                        v-for="slot in getTimesForDate(selectedDate)"
                                        :key="slot.id_disponibilidad"
                                        type="button"
                                        @click="selectTimeSlot(slot)"
                                        class="py-2.5 rounded-lg text-xs font-bold text-center border transition duration-150 cursor-pointer"
                                        :class="selectedSlot?.id_disponibilidad === slot.id_disponibilidad
                                            ? 'bg-gradient-to-r from-[#00dfb2] to-[#00a887] text-slate-950 border-none font-black shadow-md scale-[1.02]'
                                            : 'bg-[#040a12] border-[#162d4a] text-slate-350 hover:border-[#00dfb2] hover:text-[#00dfb2]'"
                                    >
                                        {{ slot.hora }} hrs
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Next button -->
                    <div class="pt-6 border-t border-[#13283f] flex justify-end">
                        <button
                            type="button"
                            @click="bookingStep = 2"
                            :disabled="!selectedDoctor || !selectedSlot"
                            class="px-5 py-2.5 bg-gradient-to-r from-[#00dfb2] to-[#00a887] hover:from-[#00ffd5] hover:to-[#00c79f] text-slate-950 rounded-xl text-xs font-black shadow-md transition disabled:opacity-50 disabled:cursor-not-allowed"
                        >
                            Siguiente Paso →
                        </button>
                    </div>
                </div>

                <!-- STEP 2: SÍNTOMAS Y DESCRIPCIÓN -->
                <div v-if="bookingStep === 2" class="space-y-6">
                    <!-- Síntomas Checkboxes -->
                    <div class="space-y-3">
                        <label class="text-[10px] font-bold text-[#00dfb2] uppercase tracking-widest block">1. Selecciona tus Síntomas</label>
                        
                        <div class="space-y-4">
                            <div v-for="category in symptomsCategories" :key="category.name" class="space-y-2">
                                <span class="text-[10px] font-extrabold text-slate-400 tracking-wider block border-l-2 border-[#00dfb2] pl-2">{{ category.name }}</span>
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                                    <div
                                        v-for="symptom in category.symptoms"
                                        :key="symptom"
                                        @click="toggleSymptom(symptom)"
                                        class="flex items-center gap-3 p-3 bg-[#040a12] border border-[#162d4a] rounded-xl hover:border-slate-500 cursor-pointer select-none transition"
                                        :class="selectedSymptoms.includes(symptom) ? 'border-[#00dfb2] bg-emerald-500/5' : ''"
                                    >
                                        <div class="w-4.5 h-4.5 rounded border flex items-center justify-center text-transparent transition"
                                             :class="selectedSymptoms.includes(symptom) ? 'border-[#00dfb2] text-[#00dfb2]' : 'border-slate-700 bg-slate-900'"
                                        >
                                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-3.5 h-3.5">
                                                <path fill-rule="evenodd" d="M16.704 4.153a.75.75 0 0 1 .143 1.052l-8 10.5a.75.75 0 0 1-1.127.075l-4.5-4.5a.75.75 0 0 1 1.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 0 1 1.05-.143Z" clip-rule="evenodd" />
                                            </svg>
                                        </div>
                                        <span class="text-xs font-semibold text-slate-350">{{ symptom }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Detalles y Descripción -->
                    <div class="space-y-2 pt-4 border-t border-[#13283f]">
                        <label for="bookingDetails" class="text-[10px] font-bold text-[#00dfb2] uppercase tracking-widest block">2. Descripción Detallada del Motivo de Consulta <span class="text-rose-500">*</span></label>
                        <textarea
                            id="bookingDetails"
                            rows="4"
                            placeholder="Por favor describe con más detalles adicionales cómo te sientes, cuándo comenzaron los síntomas y cualquier otra observación relevante..."
                            v-model="bookingDetails"
                            class="block w-full px-4 py-3 bg-[#040a12] border border-[#162d4a] rounded-xl text-white text-xs focus:outline-none focus:border-[#00dfb2] transition resize-none leading-relaxed"
                            required
                        ></textarea>
                    </div>

                    <!-- Wizard actions -->
                    <div class="pt-6 border-t border-[#13283f] flex items-center justify-between">
                        <button
                            type="button"
                            @click="bookingStep = 1"
                            class="px-4 py-2.5 bg-[#040a12] hover:bg-[#0c1a2e] border border-[#162d4a] text-slate-400 text-xs font-bold rounded-xl transition"
                        >
                            ← Volver
                        </button>
                        <button
                            type="button"
                            @click="bookingStep = 3"
                            :disabled="!bookingDetails.trim()"
                            class="px-5 py-2.5 bg-gradient-to-r from-[#00dfb2] to-[#00a887] hover:from-[#00ffd5] hover:to-[#00c79f] text-slate-950 rounded-xl text-xs font-black shadow-md transition disabled:opacity-50 disabled:cursor-not-allowed"
                        >
                            Siguiente Paso →
                        </button>
                    </div>
                </div>

                <!-- STEP 3: CONFIRMACIÓN Y RESERVA -->
                <div v-if="bookingStep === 3" class="space-y-6">
                    <div class="p-4 bg-emerald-500/5 border border-emerald-500/20 rounded-2xl space-y-4">
                        <h4 class="text-xs font-black text-[#00dfb2] uppercase tracking-wider text-center">Resumen de la Cita Médica</h4>
                        
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs font-semibold text-slate-300 pt-2 border-t border-[#13283f]/60">
                            <div>
                                <span class="text-[9px] text-slate-500 uppercase tracking-widest block">Médico Especialista</span>
                                <span class="text-slate-200 text-sm font-bold block mt-0.5">{{ getDoctorName(selectedDoctor) }}</span>
                            </div>
                            <div>
                                <span class="text-[9px] text-slate-500 uppercase tracking-widest block">Fecha y Hora</span>
                                <span class="text-slate-200 text-sm font-bold block mt-0.5">
                                    {{ new Date(selectedSlot.fecha + 'T00:00:00').toLocaleDateString('es-ES', { day: 'numeric', month: 'long', year: 'numeric' }) }} a las {{ selectedSlot.hora }} hrs
                                </span>
                            </div>
                            <div class="sm:col-span-2">
                                <span class="text-[9px] text-slate-500 uppercase tracking-widest block">Síntomas Reportados</span>
                                <span class="text-slate-250 block mt-1">
                                    {{ selectedSymptoms.length > 0 ? selectedSymptoms.join(', ') : 'Ningún síntoma seleccionado' }}
                                </span>
                            </div>
                            <div class="sm:col-span-2">
                                <span class="text-[9px] text-slate-500 uppercase tracking-widest block">Descripción del Motivo</span>
                                <p class="text-slate-300 mt-1 whitespace-pre-wrap leading-relaxed bg-[#040a12] p-3 rounded-lg border border-[#162d4a]">{{ bookingDetails }}</p>
                            </div>
                        </div>
                    </div>

                    <form @submit.prevent="submitBooking" class="pt-4 border-t border-[#13283f] flex items-center justify-between">
                        <button
                            type="button"
                            @click="bookingStep = 2"
                            class="px-4 py-2.5 bg-[#040a12] hover:bg-[#0c1a2e] border border-[#162d4a] text-slate-400 text-xs font-bold rounded-xl transition"
                        >
                            ← Volver
                        </button>
                        <button
                            type="submit"
                            :disabled="bookingForm.processing"
                            class="px-6 py-3 bg-gradient-to-r from-[#00dfb2] to-[#00a887] hover:from-[#00ffd5] hover:to-[#00c79f] text-slate-950 rounded-xl text-xs font-black shadow-lg shadow-[#00dfb2]/10 transition active:scale-[0.99]"
                        >
                            Confirmar y Agendar Cita
                        </button>
                    </form>
                </div>
            </div>

            <!-- TAB 3: MI EXPEDIENTE CLÍNICO -->
            <div v-if="activeMainTab === 'expediente'" class="space-y-6">
                <!-- RECETAS SECCIÓN -->
                <div class="bg-[#07111e] rounded-2xl border border-[#13283f] p-6 shadow-sm space-y-6">
                    <div class="border-b border-[#13283f] pb-3">
                        <h3 class="text-sm font-black text-[#00dfb2] uppercase tracking-wider">Recetas y Evolución Clínica</h3>
                        <p class="text-[11px] text-slate-550 mt-0.5">Indicaciones de tratamientos, observaciones y recetas médicas dadas por tus doctores.</p>
                    </div>

                    <div class="space-y-4">
                        <div
                            v-for="seg in seguimientos"
                            :key="seg.id_seguimiento"
                            class="p-5 bg-[#040a12] border border-[#13283f] rounded-xl space-y-4 relative border-l-4 border-l-[#00dfb2]"
                        >
                            <div class="flex items-center justify-between">
                                <span class="text-xs font-extrabold text-slate-200">Atendido por: {{ seg.doctor_nombre }}</span>
                                <span class="text-[10px] font-bold text-slate-500">Fecha: {{ new Date(seg.fecha + 'T00:00:00').toLocaleDateString('es-ES', { day: 'numeric', month: 'long', year: 'numeric' }) }}</span>
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 pt-2 border-t border-[#13283f] text-xs leading-relaxed">
                                <div>
                                    <span class="text-[9px] font-bold text-slate-500 uppercase tracking-wider block">Observación Médica</span>
                                    <p class="text-slate-300 font-medium mt-1 whitespace-pre-line leading-relaxed">{{ seg.observaciones }}</p>
                                </div>
                                <div>
                                    <span class="text-[9px] font-bold text-slate-500 uppercase tracking-wider block">Receta y Tratamiento</span>
                                    <p class="text-[#00dfb2] font-semibold mt-1 whitespace-pre-line leading-relaxed bg-teal-500/5 p-3 border border-teal-500/10 rounded-lg">{{ seg.tratamiento }}</p>
                                </div>
                            </div>
                            <div v-if="seg.proxima_revision" class="pt-3 border-t border-[#13283f] flex items-center">
                                <span class="text-[9px] font-bold text-[#00dfb2] bg-teal-500/10 border border-teal-500/15 px-3 py-1 rounded-full uppercase tracking-wider">
                                    Próxima revisión médica: {{ new Date(seg.proxima_revision + 'T00:00:00').toLocaleDateString('es-ES', { day: 'numeric', month: 'long', year: 'numeric' }) }}
                                </span>
                            </div>
                        </div>

                        <div v-if="seguimientos.length === 0" class="text-center py-12 text-slate-550 text-xs font-medium">
                            No tienes registros de recetas médicas en tu expediente.
                        </div>
                    </div>
                </div>

                <!-- PROCEDIMIENTOS SECCIÓN -->
                <div class="bg-[#07111e] rounded-2xl border border-[#13283f] p-6 shadow-sm space-y-6">
                    <div class="border-b border-[#13283f] pb-3">
                        <h3 class="text-sm font-black text-[#00dfb2] uppercase tracking-wider">Procedimientos Realizados</h3>
                        <p class="text-[11px] text-slate-550 mt-0.5">Tratamientos, chequeos técnicos u observaciones especiales ejecutadas en la clínica.</p>
                    </div>

                    <div class="space-y-4">
                        <div
                            v-for="proc in procedimientos"
                            :key="proc.id_procedimiento"
                            class="p-4 bg-[#040a12] border border-[#13283f] rounded-xl flex items-center justify-between border-l-4 border-l-[#00dfb2] hover:border-slate-700 transition"
                        >
                            <div>
                                <h4 class="text-xs font-bold text-slate-200">{{ proc.nombre }}</h4>
                                <p class="text-[11px] text-slate-400 mt-1 leading-relaxed max-w-2xl">{{ proc.descripcion }}</p>
                                <div class="flex items-center gap-4 mt-2.5 text-[9px] text-slate-500 font-extrabold uppercase tracking-wider">
                                    <span>Fecha: {{ new Date(proc.fecha + 'T00:00:00').toLocaleDateString('es-ES', { day: 'numeric', month: 'short', year: 'numeric' }) }}</span>
                                    <span>Doctor: {{ proc.doctor_nombre }}</span>
                                </div>
                            </div>
                            <span class="px-2.5 py-0.5 rounded-full text-[9px] font-black uppercase border flex-shrink-0" :class="getStatusBadgeClass(proc.estado)">
                                {{ proc.estado }}
                            </span>
                        </div>

                        <div v-if="procedimientos.length === 0" class="text-center py-12 text-slate-550 text-xs font-medium">
                            No se registran procedimientos asignados en tu expediente.
                        </div>
                    </div>
                </div>
            </div>

            <!-- TAB 4: HISTORIAL DE CITAS -->
            <div v-if="activeMainTab === 'historial'" class="bg-[#07111e] rounded-2xl border border-[#13283f] p-6 shadow-sm space-y-4">
                <div class="border-b border-[#13283f] pb-3">
                    <h3 class="text-sm font-black text-[#00dfb2] uppercase tracking-wider">Historial y Estado de Citas</h3>
                    <p class="text-[11px] text-slate-550 mt-0.5">Control y registros de todas tus consultas clínicas programadas.</p>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse text-xs">
                        <thead>
                            <tr class="bg-[#040a12] border-b border-[#13283f] text-slate-400">
                                <th class="px-4 py-3 text-xs font-bold uppercase tracking-wider">Médico</th>
                                <th class="px-4 py-3 text-xs font-bold uppercase tracking-wider">Fecha de la Cita</th>
                                <th class="px-4 py-3 text-xs font-bold uppercase tracking-wider">Hora</th>
                                <th class="px-4 py-3 text-xs font-bold uppercase tracking-wider">Motivo / Síntomas</th>
                                <th class="px-4 py-3 text-xs font-bold uppercase tracking-wider">Estado</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-[#13283f]">
                            <tr v-for="cita in historialCitas" :key="cita.id_cita" class="hover:bg-[#0c1a2e]/30 transition">
                                <td class="px-4 py-3.5 font-bold text-slate-200">{{ cita.doctor_nombre }}</td>
                                <td class="px-4 py-3.5 font-semibold text-slate-400">{{ new Date(cita.fecha + 'T00:00:00').toLocaleDateString('es-ES', { day: 'numeric', month: 'long', year: 'numeric' }) }}</td>
                                <td class="px-4 py-3.5 text-slate-400 font-semibold">{{ cita.hora }}</td>
                                <td class="px-4 py-3.5 text-slate-400 font-medium truncate max-w-[220px]">{{ cita.motivo }}</td>
                                <td class="px-4 py-3.5">
                                    <span class="px-2.5 py-0.5 rounded-full text-[9px] font-black uppercase border" :class="getStatusBadgeClass(cita.estado)">
                                        {{ cita.estado }}
                                    </span>
                                </td>
                            </tr>
                            <tr v-if="historialCitas.length === 0">
                                <td colspan="5" class="px-4 py-8 text-center text-slate-550 font-medium">No registras solicitudes de citas aún.</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- TAB 5: MIS DATOS PERSONALES / PERFIL COMPLETO FUSIONADO -->
            <div v-if="activeMainTab === 'perfil'" class="space-y-6 max-w-5xl mx-auto animate-fade-in">
                <!-- Ficha y Datos Clínicos -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 items-start">
                    <!-- Circular Avatar Summary -->
                    <div class="bg-[#07111e] rounded-2xl border border-[#13283f] p-6 text-center flex flex-col items-center space-y-4 shadow-sm">
                        <div class="relative w-24 h-24 rounded-full bg-[#040a12] border-2 border-[#00dfb2]/20 flex items-center justify-center font-black text-white text-3xl shadow-lg overflow-hidden group">
                            <img v-if="$page.props.auth.user.foto" :src="'/' + $page.props.auth.user.foto" alt="Avatar" class="w-full h-full object-cover" />
                            <span v-else>
                                {{ (patient.nombres ? patient.nombres[0] : '') + (patient.apellidos ? patient.apellidos[0] : '') }}
                            </span>
                        </div>
                        <div>
                            <h4 class="font-extrabold text-white text-base leading-tight">{{ patient.nombres }} {{ patient.apellidos }}</h4>
                            <span class="text-[10px] text-[#00dfb2] font-black uppercase tracking-widest mt-1 block">Expediente #PAC-{{ String(patient.id_paciente).padStart(4, '0') }}</span>
                        </div>
                        
                        <div class="w-full pt-4 border-t border-[#13283f] text-left text-xs space-y-3 font-semibold text-slate-400">
                            <div class="flex justify-between">
                                <span>Tipo de Sangre:</span>
                                <span class="text-[#00dfb2] font-extrabold bg-teal-500/10 border border-teal-500/20 px-2 py-0.5 rounded">{{ patient.tipo_sangre || 'O+' }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span>Edad:</span>
                                <span class="text-slate-200 font-bold">{{ getAge(patient.fecha_nacimiento) || 'No registrada' }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span>Género:</span>
                                <span class="text-slate-200 font-bold">{{ patient.sexo }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Formulario/Ficha de Datos -->
                    <div class="md:col-span-2 bg-[#07111e] rounded-2xl border border-[#13283f] p-6 shadow-sm space-y-6">
                        <div class="flex items-center justify-between border-b border-[#13283f] pb-3">
                            <h3 class="text-sm font-black text-[#00dfb2] uppercase tracking-wider">Ficha Técnica del Paciente</h3>
                            <button
                                v-if="!isEditingProfile"
                                @click="startEditing"
                                class="px-3.5 py-1.5 bg-emerald-500/10 hover:bg-emerald-500/15 text-[#00dfb2] border border-teal-500/20 text-xs font-bold rounded-lg transition cursor-pointer"
                            >
                                Editar Información
                            </button>
                        </div>

                        <!-- Vista Lectura Perfil -->
                        <div v-if="!isEditingProfile" class="grid grid-cols-1 sm:grid-cols-2 gap-6 text-xs font-semibold">
                            <div class="space-y-1">
                                <span class="text-slate-500 block uppercase text-[10px] tracking-wider">Nombres</span>
                                <span class="text-slate-250 text-sm font-bold block">{{ patient.nombres }}</span>
                            </div>
                            <div class="space-y-1">
                                <span class="text-slate-500 block uppercase text-[10px] tracking-wider">Apellidos</span>
                                <span class="text-slate-250 text-sm font-bold block">{{ patient.apellidos }}</span>
                            </div>
                            <div class="space-y-1">
                                <span class="text-slate-500 block uppercase text-[10px] tracking-wider">Documento Único de Identidad (DUI)</span>
                                <span class="text-slate-250 text-sm font-bold block">{{ patient.dui || 'No registrado' }}</span>
                            </div>
                            <div class="space-y-1">
                                <span class="text-slate-500 block uppercase text-[10px] tracking-wider">Fecha de Nacimiento</span>
                                <span class="text-slate-250 text-sm font-bold block">{{ new Date(patient.fecha_nacimiento + 'T00:00:00').toLocaleDateString('es-ES', {day: 'numeric', month: 'long', year: 'numeric'}) }}</span>
                            </div>
                            <div class="space-y-1">
                                <span class="text-slate-500 block uppercase text-[10px] tracking-wider">Teléfono de Contacto</span>
                                <span class="text-slate-250 text-sm font-bold block">{{ patient.telefono }}</span>
                            </div>
                            <div class="space-y-1">
                                <span class="text-slate-500 block uppercase text-[10px] tracking-wider">Dirección Física</span>
                                <span class="text-slate-250 text-sm font-bold block leading-relaxed">{{ patient.direccion }}</span>
                            </div>
                        </div>

                        <!-- Formulario de Edición de Perfil -->
                        <form v-else @submit.prevent="submitProfileUpdate" class="space-y-5">
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div class="space-y-1.5">
                                    <label class="text-[10px] font-bold text-[#00dfb2] uppercase tracking-wider block">Nombres</label>
                                    <input
                                        type="text"
                                        v-model="profileForm.nombres"
                                        class="block w-full px-3.5 py-2.5 bg-[#040a12] border border-[#162d4a] rounded-xl text-white text-xs focus:outline-none focus:border-[#00dfb2] transition"
                                        required
                                    />
                                    <div v-if="profileForm.errors.nombres" class="text-[10px] text-rose-500 font-semibold">{{ profileForm.errors.nombres }}</div>
                                </div>
                                <div class="space-y-1.5">
                                    <label class="text-[10px] font-bold text-[#00dfb2] uppercase tracking-wider block">Apellidos</label>
                                    <input
                                        type="text"
                                        v-model="profileForm.apellidos"
                                        class="block w-full px-3.5 py-2.5 bg-[#040a12] border border-[#162d4a] rounded-xl text-white text-xs focus:outline-none focus:border-[#00dfb2] transition"
                                        required
                                    />
                                    <div v-if="profileForm.errors.apellidos" class="text-[10px] text-rose-500 font-semibold">{{ profileForm.errors.apellidos }}</div>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div class="space-y-1.5">
                                    <label class="text-[10px] font-bold text-[#00dfb2] uppercase tracking-wider block">DUI</label>
                                    <input
                                        type="text"
                                        v-model="profileForm.dui"
                                        placeholder="00000000-0"
                                        class="block w-full px-3.5 py-2.5 bg-[#040a12] border border-[#162d4a] rounded-xl text-white text-xs focus:outline-none focus:border-[#00dfb2] transition"
                                    />
                                    <div v-if="profileForm.errors.dui" class="text-[10px] text-rose-500 font-semibold">{{ profileForm.errors.dui }}</div>
                                </div>
                                <div class="space-y-1.5">
                                    <label class="text-[10px] font-bold text-[#00dfb2] uppercase tracking-wider block">Género</label>
                                    <select
                                        v-model="profileForm.sexo"
                                        class="block w-full px-3.5 py-2.5 bg-[#040a12] border border-[#162d4a] rounded-xl text-white text-xs focus:outline-none focus:border-[#00dfb2] transition cursor-pointer"
                                    >
                                        <option value="Masculino">Masculino</option>
                                        <option value="Femenino">Femenino</option>
                                        <option value="Otro">Otro</option>
                                    </select>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div class="space-y-1.5">
                                    <label class="text-[10px] font-bold text-[#00dfb2] uppercase tracking-wider block">F. Nacimiento</label>
                                    <input
                                        type="date"
                                        v-model="profileForm.fecha_nacimiento"
                                        class="block w-full px-3.5 py-2.5 bg-[#040a12] border border-[#162d4a] rounded-xl text-white text-xs focus:outline-none focus:border-[#00dfb2] transition"
                                        required
                                    />
                                    <div v-if="profileForm.errors.fecha_nacimiento" class="text-[10px] text-rose-500 font-semibold">{{ profileForm.errors.fecha_nacimiento }}</div>
                                </div>
                                <div class="space-y-1.5">
                                    <label class="text-[10px] font-bold text-[#00dfb2] uppercase tracking-wider block">Teléfono (El Salvador)</label>
                                    <div class="flex gap-2">
                                        <div class="flex items-center justify-center px-3.5 bg-[#040a12] border border-[#162d4a] rounded-xl text-white text-xs font-bold select-none">
                                            🇸🇻 +503
                                        </div>
                                        <input
                                            type="text"
                                            v-model="phoneNumber"
                                            @input="formatPhoneNumber"
                                            placeholder="7000-0000"
                                            class="block flex-1 px-3.5 py-2.5 bg-[#040a12] border border-[#162d4a] rounded-xl text-white text-xs focus:outline-none focus:border-[#00dfb2] transition"
                                            required
                                        />
                                    </div>
                                    <div v-if="profileForm.errors.telefono" class="text-[10px] text-rose-500 font-semibold">{{ profileForm.errors.telefono }}</div>
                                </div>
                            </div>

                            <div class="space-y-1.5">
                                <label class="text-[10px] font-bold text-[#00dfb2] uppercase tracking-wider block">Dirección</label>
                                <textarea
                                    rows="3"
                                    v-model="profileForm.direccion"
                                    class="block w-full px-3.5 py-2.5 bg-[#040a12] border border-[#162d4a] rounded-xl text-white text-xs focus:outline-none focus:border-[#00dfb2] transition resize-none"
                                    required
                                ></textarea>
                                <div v-if="profileForm.errors.direccion" class="text-[10px] text-rose-500 font-semibold">{{ profileForm.errors.direccion }}</div>
                            </div>

                            <div class="flex items-center justify-end gap-2.5 pt-2">
                                <button
                                    type="button"
                                    @click="cancelEditing"
                                    class="px-4 py-2.5 bg-[#040a12] hover:bg-[#081325] border border-[#162d4a] text-slate-400 text-xs font-bold rounded-xl transition cursor-pointer"
                                >
                                    Cancelar
                                </button>
                                <button
                                    type="submit"
                                    :disabled="profileForm.processing"
                                    class="px-5 py-2.5 bg-gradient-to-r from-[#00dfb2] to-[#00a887] hover:from-[#00ffd5] hover:to-[#00c79f] text-slate-950 text-xs font-black rounded-xl shadow-md transition cursor-pointer"
                                >
                                    Guardar Cambios
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Datos de Acceso y Avatar (UpdateProfileInformationForm) -->
                <div class="bg-[#07111e] rounded-2xl border border-[#13283f] p-6 shadow-sm">
                    <UpdateProfileInformationForm
                        :must-verify-email="mustVerifyEmail"
                        :status="status"
                    />
                </div>

                <!-- Seguridad y Clave (UpdatePasswordForm) -->
                <div class="bg-[#07111e] rounded-2xl border border-[#13283f] p-6 shadow-sm">
                    <UpdatePasswordForm />
                </div>
            </div>

        </div>
    </AuthenticatedLayout>
</template>
