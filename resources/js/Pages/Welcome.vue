<script setup>
import { Head, Link } from '@inertiajs/vue3';
import { ref } from 'vue';

defineProps({
    canLogin: {
        type: Boolean,
    },
    canRegister: {
        type: Boolean,
    },
    laravelVersion: {
        type: String,
    },
    phpVersion: {
        type: String,
    },
});

const mobileMenuOpen = ref(false);

const specialties = [
    {
        name: 'Cardiología',
        desc: 'Evaluación y tratamiento integral de patologías cardiovasculares con tecnología avanzada.',
        icon: 'heart'
    },
    {
        name: 'Pediatría',
        desc: 'Cuidado médico especializado y preventivo para el desarrollo saludable de tus hijos.',
        icon: 'child'
    },
    {
        name: 'Medicina General',
        desc: 'Atención médica de primer contacto orientada a la prevención, diagnóstico y tratamiento.',
        icon: 'stethoscope'
    },
    {
        name: 'Traumatología',
        desc: 'Tratamiento experto de lesiones óseas, articulares y del sistema músculo-esquelético.',
        icon: 'bandaid'
    },
    {
        name: 'Ginecología',
        desc: 'Cuidado integral de la salud femenina en todas las etapas de la vida.',
        icon: 'shield'
    },
    {
        name: 'Dermatología',
        desc: 'Diagnóstico y tratamiento de afecciones de la piel, cabello y uñas con enfoque clínico.',
        icon: 'sparkles'
    }
];

const faqs = ref([
    {
        q: '¿Cómo puedo agendar una cita médica?',
        a: 'Es muy sencillo. Regístrate en nuestra plataforma como paciente, inicia sesión, accede a tu "Portal Médico" y haz clic en "Agendar Cita". Elige tu médico de preferencia, el horario disponible y listo.',
        open: false
    },
    {
        q: '¿Puedo ver mis recetas y seguimientos médicos en línea?',
        a: 'Sí. A través de nuestro Portal de Pacientes, tendrás acceso en tiempo real a todas tus consultas pasadas, recetas médicas, procedimientos registrados y el seguimiento clínico brindado por tu médico.',
        open: false
    },
    {
        q: '¿Cómo cancelo o reprogramo una cita?',
        a: 'Desde tu Panel de Paciente puedes visualizar tus citas programadas. Si necesitas cancelarla, puedes hacerlo con un solo clic. Para reprogramar, te sugerimos cancelar la actual y agendar una nueva en el horario que más te convenga.',
        open: false
    }
]);
</script>

<template>
    <Head title="ProDoctor - Sistema de Gestión Médica Premium" />
    
    <div class="min-h-screen bg-slate-50 text-slate-800 antialiased font-sans selection:bg-blue-600 selection:text-white">
        <!-- Top bar/Header -->
        <header class="sticky top-0 z-50 bg-white/80 backdrop-blur-md border-b border-slate-200">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex items-center justify-between h-20">
                    <!-- Brand Logo -->
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-blue-600 to-indigo-500 flex items-center justify-center shadow-lg shadow-blue-500/20">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-5 h-5 text-white">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M4 12h3.5l1.5-3.5 2.5 7 2-4.5 1.5 1H20" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v2m0 12v2" />
                            </svg>
                        </div>
                        <div>
                            <span class="text-xl font-extrabold tracking-tight bg-gradient-to-r from-blue-600 to-indigo-650 bg-clip-text text-transparent">ProDoctor</span>
                            <span class="block text-[9px] text-slate-400 font-bold tracking-widest uppercase -mt-1">Gabinete Médico</span>
                        </div>
                    </div>

                    <!-- Navigation Desktop -->
                    <nav class="hidden md:flex items-center gap-8 text-sm font-bold text-slate-600">
                        <a href="#servicios" class="hover:text-blue-600 transition">Especialidades</a>
                        <a href="#metricas" class="hover:text-blue-600 transition">Estadísticas</a>
                        <a href="#nosotros" class="hover:text-blue-600 transition">Sobre Nosotros</a>
                        <a href="#faqs" class="hover:text-blue-600 transition">Preguntas Frecuentes</a>
                    </nav>

                    <!-- Auth actions -->
                    <div class="hidden md:flex items-center gap-4">
                        <template v-if="$page.props.auth.user">
                            <Link :href="route('dashboard')" class="inline-flex items-center justify-center px-5 py-2.5 rounded-xl text-sm font-bold text-white bg-blue-600 hover:bg-blue-700 shadow-md shadow-blue-500/10 hover:shadow-lg transition active:scale-[0.98]">
                                Ir al Dashboard &rarr;
                            </Link>
                        </template>
                        <template v-else>
                            <Link :href="route('login')" class="text-sm font-bold text-slate-600 hover:text-blue-600 transition px-3 py-2">
                                Iniciar Sesión
                            </Link>
                            <Link v-if="canRegister" :href="route('register')" class="inline-flex items-center justify-center px-5 py-2.5 rounded-xl text-sm font-bold text-white bg-slate-900 hover:bg-slate-800 shadow-md shadow-slate-950/10 hover:shadow-lg transition active:scale-[0.98]">
                                Registrarse
                            </Link>
                        </template>
                    </div>

                    <!-- Mobile Menu Button -->
                    <div class="md:hidden">
                        <button @click="mobileMenuOpen = !mobileMenuOpen" class="p-2 rounded-lg text-slate-500 hover:bg-slate-100 hover:text-slate-700 transition">
                            <svg v-if="!mobileMenuOpen" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-6 h-6">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
                            </svg>
                            <svg v-else xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-6 h-6">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Mobile Navigation -->
            <div v-if="mobileMenuOpen" class="md:hidden border-b border-slate-200 bg-white">
                <div class="px-4 pt-2 pb-6 space-y-3">
                    <a href="#servicios" @click="mobileMenuOpen = false" class="block py-2 text-base font-bold text-slate-600 hover:text-blue-600">Especialidades</a>
                    <a href="#metricas" @click="mobileMenuOpen = false" class="block py-2 text-base font-bold text-slate-600 hover:text-blue-600">Estadísticas</a>
                    <a href="#nosotros" @click="mobileMenuOpen = false" class="block py-2 text-base font-bold text-slate-600 hover:text-blue-600">Sobre Nosotros</a>
                    <a href="#faqs" @click="mobileMenuOpen = false" class="block py-2 text-base font-bold text-slate-600 hover:text-blue-600">Preguntas Frecuentes</a>
                    <hr class="border-slate-200 my-2" />
                    <div class="flex flex-col gap-2 pt-2">
                        <template v-if="$page.props.auth.user">
                            <Link :href="route('dashboard')" @click="mobileMenuOpen = false" class="w-full text-center py-2.5 rounded-xl text-sm font-bold text-white bg-blue-600 hover:bg-blue-700">
                                Ir al Dashboard
                            </Link>
                        </template>
                        <template v-else>
                            <Link :href="route('login')" @click="mobileMenuOpen = false" class="w-full text-center py-2.5 rounded-xl text-sm font-bold text-slate-700 border border-slate-200 hover:bg-slate-50">
                                Iniciar Sesión
                            </Link>
                            <Link v-if="canRegister" :href="route('register')" @click="mobileMenuOpen = false" class="w-full text-center py-2.5 rounded-xl text-sm font-bold text-white bg-slate-900 hover:bg-slate-800">
                                Registrarse
                            </Link>
                        </template>
                    </div>
                </div>
            </div>
        </header>

        <!-- Hero Section -->
        <section class="relative py-20 lg:py-32 overflow-hidden bg-white">
            <!-- Background grids/blobs -->
            <div class="absolute inset-0 z-0 opacity-30">
                <div class="absolute -top-40 -right-40 w-96 h-96 bg-blue-400 rounded-full filter blur-3xl"></div>
                <div class="absolute top-60 -left-20 w-80 h-80 bg-teal-300 rounded-full filter blur-3xl"></div>
            </div>

            <div class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 grid grid-cols-1 lg:grid-cols-2 gap-12 lg:gap-8 items-center">
                <!-- Text Content -->
                <div class="space-y-8 text-center lg:text-left">
                    <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-blue-50 border border-blue-200/60 text-blue-600 text-xs font-extrabold tracking-wide uppercase">
                        <span class="w-2 h-2 rounded-full bg-blue-500 animate-ping"></span>
                        Gestión Médica Innovadora y Segura
                    </div>
                    
                    <h1 class="text-4xl sm:text-5xl lg:text-6xl font-extrabold text-slate-900 tracking-tight leading-tight">
                        Cuidado médico de <br class="hidden sm:inline" />
                        <span class="bg-gradient-to-r from-blue-600 to-indigo-650 bg-clip-text text-transparent">excelencia digital</span>
                    </h1>

                    <p class="text-base sm:text-lg text-slate-500 max-w-xl mx-auto lg:mx-0 font-medium leading-relaxed">
                        ProDoctor es la plataforma médica avanzada que simplifica la interacción entre médicos y pacientes. Administra tu agenda, consulta expedientes y genera seguimientos clínicos con un diseño intuitivo y moderno.
                    </p>

                    <div class="flex flex-col sm:flex-row items-center justify-center lg:justify-start gap-4">
                        <template v-if="$page.props.auth.user">
                            <Link :href="route('dashboard')" class="w-full sm:w-auto inline-flex items-center justify-center px-8 py-4 rounded-xl text-base font-bold text-white bg-blue-600 hover:bg-blue-700 shadow-lg shadow-blue-500/20 hover:shadow-xl transition active:scale-[0.98]">
                                Acceder al Panel de Control &rarr;
                            </Link>
                        </template>
                        <template v-else>
                            <Link :href="route('register')" class="w-full sm:w-auto inline-flex items-center justify-center px-8 py-4 rounded-xl text-base font-bold text-white bg-blue-600 hover:bg-blue-700 shadow-lg shadow-blue-500/20 hover:shadow-xl transition active:scale-[0.98]">
                                Registrarse como Paciente
                            </Link>
                            <Link :href="route('login')" class="w-full sm:w-auto inline-flex items-center justify-center px-8 py-4 rounded-xl text-base font-bold text-slate-700 bg-white border border-slate-200 hover:border-slate-350 hover:bg-slate-50 shadow-sm transition active:scale-[0.98]">
                                Acceso Doctores
                            </Link>
                        </template>
                    </div>

                    <div class="pt-6 flex flex-wrap justify-center lg:justify-start items-center gap-6 text-slate-400 text-xs font-bold uppercase tracking-wider">
                        <div class="flex items-center gap-2">
                            <svg class="w-5 h-5 text-emerald-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
                            Historial Clínico Seguro
                        </div>
                        <div class="flex items-center gap-2">
                            <svg class="w-5 h-5 text-emerald-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
                            Recetas Médicas Rápidas
                        </div>
                        <div class="flex items-center gap-2">
                            <svg class="w-5 h-5 text-emerald-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
                            Agenda en Línea
                        </div>
                    </div>
                </div>

                <!-- Interactive mockup / graphic representation -->
                <div class="relative flex items-center justify-center lg:justify-end">
                    <!-- Decorative back elements -->
                    <div class="absolute w-72 h-72 sm:w-96 sm:h-96 rounded-full bg-blue-100 opacity-50 -z-10 translate-x-12 translate-y-12"></div>
                    
                    <!-- Dashboard Mockup Card -->
                    <div class="w-full max-w-lg bg-slate-900 text-white rounded-3xl p-6 sm:p-8 shadow-2xl border border-slate-800 relative select-none animate-fade-in">
                        <!-- Window dots -->
                        <div class="flex gap-2 mb-6">
                            <span class="w-3 h-3 rounded-full bg-rose-500"></span>
                            <span class="w-3 h-3 rounded-full bg-amber-500"></span>
                            <span class="w-3 h-3 rounded-full bg-emerald-500"></span>
                        </div>
                        
                        <div class="space-y-6">
                            <div class="flex items-center justify-between">
                                <div>
                                    <span class="text-[10px] font-bold text-blue-400 uppercase tracking-widest">Panel Médico Activo</span>
                                    <h3 class="text-xl font-bold tracking-tight text-white mt-1">Dr. Alejandro Ortega</h3>
                                </div>
                                <span class="bg-blue-500/10 border border-blue-500/30 text-blue-400 text-xs px-3 py-1 rounded-full font-bold">Cardiólogo</span>
                            </div>

                            <!-- Graph / visual representation -->
                            <div class="bg-slate-800/50 border border-slate-700/50 rounded-2xl p-4 space-y-4">
                                <div class="flex items-center justify-between text-xs font-bold text-slate-400">
                                    <span>Rendimiento de Citas (Semanal)</span>
                                    <span class="text-emerald-400">+18% vs semana anterior</span>
                                </div>
                                <div class="h-28 flex items-end gap-2.5 pt-2">
                                    <div class="flex-1 bg-slate-700 hover:bg-blue-500 transition-all duration-300 rounded-t-lg h-[40%] relative group cursor-pointer">
                                        <span class="absolute -top-7 left-1/2 -translate-x-1/2 bg-slate-950 text-[10px] font-bold text-white px-2 py-0.5 rounded opacity-0 group-hover:opacity-100 transition-opacity">12</span>
                                    </div>
                                    <div class="flex-1 bg-slate-700 hover:bg-blue-500 transition-all duration-300 rounded-t-lg h-[65%] relative group cursor-pointer">
                                        <span class="absolute -top-7 left-1/2 -translate-x-1/2 bg-slate-950 text-[10px] font-bold text-white px-2 py-0.5 rounded opacity-0 group-hover:opacity-100 transition-opacity">20</span>
                                    </div>
                                    <div class="flex-1 bg-slate-700 hover:bg-blue-500 transition-all duration-300 rounded-t-lg h-[50%] relative group cursor-pointer">
                                        <span class="absolute -top-7 left-1/2 -translate-x-1/2 bg-slate-950 text-[10px] font-bold text-white px-2 py-0.5 rounded opacity-0 group-hover:opacity-100 transition-opacity">15</span>
                                    </div>
                                    <div class="flex-1 bg-blue-500 rounded-t-lg h-[90%] relative group cursor-pointer">
                                        <span class="absolute -top-7 left-1/2 -translate-x-1/2 bg-slate-950 text-[10px] font-bold text-white px-2 py-0.5 rounded opacity-0 group-hover:opacity-100 transition-opacity">28</span>
                                    </div>
                                    <div class="flex-1 bg-slate-700 hover:bg-blue-500 transition-all duration-300 rounded-t-lg h-[55%] relative group cursor-pointer">
                                        <span class="absolute -top-7 left-1/2 -translate-x-1/2 bg-slate-950 text-[10px] font-bold text-white px-2 py-0.5 rounded opacity-0 group-hover:opacity-100 transition-opacity">17</span>
                                    </div>
                                </div>
                                <div class="flex justify-between text-[10px] font-extrabold text-slate-500 tracking-wider">
                                    <span>LUN</span>
                                    <span>MAR</span>
                                    <span>MIÉ</span>
                                    <span>JUE</span>
                                    <span>VIE</span>
                                </div>
                            </div>

                            <!-- Inline patient alert -->
                            <div class="flex items-center justify-between p-3.5 bg-slate-800/80 border border-slate-750 rounded-2xl">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-full bg-emerald-500/10 border border-emerald-500/30 flex items-center justify-center text-emerald-400 font-bold text-xs">
                                        SM
                                    </div>
                                    <div>
                                        <h4 class="text-xs font-bold">Sofía Mendoza</h4>
                                        <p class="text-[9px] text-slate-400">Consulta de Control a las 15:30</p>
                                    </div>
                                </div>
                                <span class="text-[10px] font-bold text-emerald-400 bg-emerald-500/10 px-2 py-0.5 rounded border border-emerald-500/20">Confirmada</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Stats Section -->
        <section id="metricas" class="py-12 bg-slate-900 text-white">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="grid grid-cols-2 md:grid-cols-4 gap-8 text-center divide-y md:divide-y-0 md:divide-x divide-slate-800">
                    <div class="space-y-2 pt-6 md:pt-0">
                        <span class="block text-4xl sm:text-5xl font-extrabold text-blue-500">12k+</span>
                        <span class="text-xs sm:text-sm font-bold text-slate-400 uppercase tracking-widest">PACIENTES ATENDIDOS</span>
                    </div>
                    <div class="space-y-2 pt-6 md:pt-0">
                        <span class="block text-4xl sm:text-5xl font-extrabold text-teal-400">30+</span>
                        <span class="text-xs sm:text-sm font-bold text-slate-400 uppercase tracking-widest">MÉDICOS EXPERTOS</span>
                    </div>
                    <div class="space-y-2 pt-6 md:pt-0">
                        <span class="block text-4xl sm:text-5xl font-extrabold text-amber-500">99.4%</span>
                        <span class="text-xs sm:text-sm font-bold text-slate-400 uppercase tracking-widest">SATISFACCIÓN PACIENTE</span>
                    </div>
                    <div class="space-y-2 pt-6 md:pt-0">
                        <span class="block text-4xl sm:text-5xl font-extrabold text-blue-500">24/7</span>
                        <span class="text-xs sm:text-sm font-bold text-slate-400 uppercase tracking-widest">SOPORTE Y AGENDAMIENTO</span>
                    </div>
                </div>
            </div>
        </section>

        <!-- Services/Specialties Section -->
        <section id="servicios" class="py-24 bg-slate-50">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-16">
                <div class="text-center max-w-2xl mx-auto space-y-4">
                    <span class="text-xs font-extrabold text-blue-600 uppercase tracking-widest">Nuestra Experiencia</span>
                    <h2 class="text-3xl sm:text-4xl font-extrabold text-slate-900 tracking-tight">Especialidades Médicas de Vanguardia</h2>
                    <p class="text-sm sm:text-base text-slate-500 font-medium">Ofrecemos cobertura completa para ti y tu familia con médicos comprometidos y tecnología de punta.</p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                    <div v-for="spec in specialties" :key="spec.name" class="bg-white rounded-2xl border border-slate-200 p-8 shadow-sm hover:shadow-xl hover:border-blue-200 transition-all duration-300 group cursor-default text-left">
                        <div class="w-12 h-12 rounded-xl bg-blue-50 group-hover:bg-blue-600 group-hover:text-white transition duration-300 flex items-center justify-center text-blue-600 mb-6 shadow-sm">
                            <!-- Stethoscope icon -->
                            <svg v-if="spec.icon === 'stethoscope'" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-6 h-6">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12c0 1.268-.63 2.39-1.593 3.068a3.745 3.745 0 01-1.043 3.296 3.745 3.745 0 01-3.296 1.043A3.745 3.745 0 0112 21c-1.268 0-2.39-.63-3.068-1.593a3.746 3.746 0 01-3.296-1.043 3.745 3.745 0 01-1.043-3.296A3.745 3.745 0 013 12c0-1.268.63-2.39 1.593-3.068a3.745 3.745 0 011.043-3.296 3.746 3.746 0 013.296-1.043A3.746 3.746 0 0112 3c1.268 0 2.39.63 3.068 1.593a3.746 3.746 0 013.296 1.043 3.746 3.746 0 011.043 3.296A3.745 3.745 0 0121 12z" />
                            </svg>
                            <!-- Heart icon -->
                            <svg v-else-if="spec.icon === 'heart'" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-6 h-6">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12z" />
                            </svg>
                            <!-- Child icon -->
                            <svg v-else-if="spec.icon === 'child'" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-6 h-6">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M18 18.72a9.094 9.094 0 003.741-.479 3 3 0 00-4.682-2.72m.94 3.198l.001.031c0 .225-.012.447-.037.666A11.944 11.944 0 0112 21c-2.17 0-4.207-.576-5.963-1.584A6.062 6.062 0 016 18.719m12 0a5.961 5.961 0 00-.721-2.815M9 7.5A3 3 0 119 1.5 3 3 0 019 7.5zm3 0a3 3 0 113-3 3 3 0 01-3 3zm-9 11.22c0-1.125.504-2.125 1.258-2.815m.94 3.198l-.001.031c0 .225.012.447.037.666A11.94 11.94 0 0112 21c2.17 0 4.207-.576 5.963-1.584A6.062 6.062 0 0018 18.72m-12 0a5.962 5.962 0 01.721-2.815M9 15h-.008L9 15z" />
                            </svg>
                            <!-- Bandaid icon -->
                            <svg v-else-if="spec.icon === 'bandaid'" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-6 h-6">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                            </svg>
                            <!-- Shield icon -->
                            <svg v-else-if="spec.icon === 'shield'" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-6 h-6">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.57-.598-3.75h-.152c-3.196 0-6.1-1.248-8.25-3.286zm0 0v-2.03m0 2.03V5.5" />
                            </svg>
                            <!-- Sparkles icon -->
                            <svg v-else xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-6 h-6">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9.813 15.904L9 21l-.813-5.096L3 15l5.187-.813L9 9l.813 5.187L15 15l-5.187.813zM18 6.5L17.5 9l-.5-2.5L14.5 6l2.5-.5L17.5 3l.5 2.5L20.6 6l-2.6.5z" />
                            </svg>
                        </div>
                        <h3 class="text-lg font-bold text-slate-800 mb-3 group-hover:text-blue-600 transition">{{ spec.name }}</h3>
                        <p class="text-xs sm:text-sm text-slate-500 font-medium leading-relaxed">{{ spec.desc }}</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- About Us Section -->
        <section id="nosotros" class="py-24 bg-white relative overflow-hidden">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 grid grid-cols-1 lg:grid-cols-2 gap-16 items-center">
                <!-- Left graphic illustration -->
                <div class="relative flex justify-center">
                    <div class="w-full max-w-md bg-gradient-to-tr from-blue-600 to-indigo-650 rounded-3xl p-8 text-white shadow-xl relative overflow-hidden select-none">
                        <div class="absolute inset-0 opacity-10">
                            <!-- Abstract grid -->
                            <svg width="100%" height="100%" xmlns="http://www.w3.org/2000/svg">
                                <defs><pattern id="grid" width="20" height="20" patternUnits="userSpaceOnUse"><path d="M 20 0 L 0 0 0 20" fill="none" stroke="white" stroke-width="1"/></pattern></defs>
                                <rect width="100%" height="100%" fill="url(#grid)" />
                            </svg>
                        </div>
                        
                        <div class="relative space-y-6 z-10">
                            <h3 class="text-2xl font-bold leading-tight">Expediente Clínico Digital Centralizado</h3>
                            <p class="text-sm text-blue-100 font-medium leading-relaxed">
                                Olvídate de los papeles traspapelados y expedientes desorganizados. Con ProDoctor, cada médico cuenta con un perfil para actualizar pautas, seguimientos médicos e imprimir recetas directamente desde la nube.
                            </p>
                            <div class="pt-4 border-t border-blue-400/40 space-y-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-6 h-6 rounded-full bg-white/20 flex items-center justify-center text-xs font-bold">1</div>
                                    <span class="text-xs font-bold text-white">Seguridad de datos cifrados</span>
                                </div>
                                <div class="flex items-center gap-3">
                                    <div class="w-6 h-6 rounded-full bg-white/20 flex items-center justify-center text-xs font-bold">2</div>
                                    <span class="text-xs font-bold text-white">Acceso inmediato desde cualquier dispositivo</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right text content -->
                <div class="space-y-8 text-left">
                    <span class="text-xs font-extrabold text-blue-600 uppercase tracking-widest">Comprometidos Contigo</span>
                    <h2 class="text-3xl sm:text-4xl font-extrabold text-slate-900 tracking-tight">Hacia una atención médica moderna y conectada</h2>
                    <p class="text-base text-slate-500 font-medium leading-relaxed">
                        Nuestra misión es empoderar a los profesionales de la salud con herramientas digitales premium para brindar diagnósticos más rápidos y precisos. A la vez, damos a los pacientes el control total de su historial y citas de manera ágil.
                    </p>
                    
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 pt-2">
                        <div class="bg-slate-50 p-6 rounded-2xl border border-slate-200">
                            <h4 class="text-base font-bold text-slate-800">Para Pacientes</h4>
                            <p class="text-xs text-slate-500 mt-2 font-medium">Agendamiento web ágil, consulta de recetas médicas y registro de antecedentes en un entorno sumamente seguro.</p>
                        </div>
                        <div class="bg-slate-50 p-6 rounded-2xl border border-slate-200">
                            <h4 class="text-base font-bold text-slate-800">Para Doctores</h4>
                            <p class="text-xs text-slate-500 mt-2 font-medium">Control de agenda, registro de procedimientos detallados y generación automática de seguimientos de evolución clínica.</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- FAQs Section -->
        <section id="faqs" class="py-24 bg-slate-50">
            <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 space-y-16">
                <div class="text-center space-y-4">
                    <span class="text-xs font-extrabold text-blue-600 uppercase tracking-widest">Preguntas Frecuentes</span>
                    <h2 class="text-3xl font-extrabold text-slate-900 tracking-tight">Resolver tus dudas</h2>
                </div>

                <div class="space-y-4">
                    <div v-for="(faq, idx) in faqs" :key="idx" class="bg-white rounded-2xl border border-slate-200 overflow-hidden transition-all duration-350">
                        <button @click="faq.open = !faq.open" class="w-full px-6 py-5 flex items-center justify-between font-bold text-slate-800 hover:text-blue-600 text-left transition">
                            <span>{{ faq.q }}</span>
                            <span class="ml-4 text-slate-400">
                                <svg v-if="!faq.open" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-4 h-4">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
                                </svg>
                                <svg v-else xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-4 h-4">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 15.75l7.5-7.5 7.5 7.5" />
                                </svg>
                            </span>
                        </button>
                        <div v-if="faq.open" class="px-6 pb-6 text-sm text-slate-500 font-medium leading-relaxed animate-fade-in">
                            {{ faq.a }}
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- CTA Section -->
        <section class="py-20 bg-gradient-to-tr from-blue-600 to-indigo-650 text-white relative overflow-hidden">
            <div class="absolute inset-0 opacity-10">
                <svg width="100%" height="100%" xmlns="http://www.w3.org/2000/svg">
                    <defs><pattern id="dots" width="30" height="30" patternUnits="userSpaceOnUse"><circle cx="15" cy="15" r="1.5" fill="white" /></pattern></defs>
                    <rect width="100%" height="100%" fill="url(#dots)" />
                </svg>
            </div>
            
            <div class="relative z-10 max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center space-y-8">
                <h2 class="text-3xl sm:text-4xl font-extrabold tracking-tight">
                    ¿Listo para experimentar una gestión médica del futuro?
                </h2>
                <p class="text-base sm:text-lg text-blue-100 max-w-xl mx-auto font-medium">
                    Regístrate gratis hoy mismo y accede de inmediato al portal clínico de ProDoctor.
                </p>
                <div class="flex justify-center gap-4">
                    <template v-if="$page.props.auth.user">
                        <Link :href="route('dashboard')" class="inline-flex items-center justify-center px-8 py-3.5 rounded-xl text-sm font-bold text-blue-600 bg-white hover:bg-slate-50 shadow-lg transition active:scale-[0.98]">
                            Panel Administrativo
                        </Link>
                    </template>
                    <template v-else>
                        <Link :href="route('register')" class="inline-flex items-center justify-center px-8 py-3.5 rounded-xl text-sm font-bold text-blue-600 bg-white hover:bg-slate-50 shadow-lg transition active:scale-[0.98]">
                            Comenzar Registro
                        </Link>
                        <Link :href="route('login')" class="inline-flex items-center justify-center px-8 py-3.5 rounded-xl text-sm font-bold text-white bg-blue-750/30 border border-blue-400/30 hover:bg-blue-750/50 shadow-sm transition active:scale-[0.98]">
                            Acceso Personal
                        </Link>
                    </template>
                </div>
            </div>
        </section>

        <!-- Footer -->
        <footer class="bg-slate-900 text-slate-500 py-16 border-t border-slate-800">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-12">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                    <!-- Brand info -->
                    <div class="space-y-4">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-lg bg-blue-600 flex items-center justify-center">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4 text-white">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 12h3.5l1.5-3.5 2.5 7 2-4.5 1.5 1H20" />
                                </svg>
                            </div>
                            <span class="text-base font-extrabold text-white">ProDoctor</span>
                        </div>
                        <p class="text-xs font-medium leading-relaxed text-slate-400">
                            Gestión clínica segura e inteligente para consultorios, clínicas y profesionales independientes.
                        </p>
                    </div>

                    <!-- Services links -->
                    <div class="space-y-4">
                        <h4 class="text-xs font-extrabold text-white uppercase tracking-widest">Especialidades</h4>
                        <ul class="space-y-2 text-xs font-medium">
                            <li><a href="#" class="hover:text-white transition">Cardiología</a></li>
                            <li><a href="#" class="hover:text-white transition">Pediatría</a></li>
                            <li><a href="#" class="hover:text-white transition">Ginecología</a></li>
                            <li><a href="#" class="hover:text-white transition">Dermatología</a></li>
                        </ul>
                    </div>

                    <!-- Platform links -->
                    <div class="space-y-4">
                        <h4 class="text-xs font-extrabold text-white uppercase tracking-widest">Portal</h4>
                        <ul class="space-y-2 text-xs font-medium">
                            <li><Link :href="route('login')" class="hover:text-white transition">Ingresar al Sistema</Link></li>
                            <li><Link :href="route('register')" class="hover:text-white transition">Registrar Cuenta</Link></li>
                            <li><a href="#" class="hover:text-white transition">Políticas de Privacidad</a></li>
                            <li><a href="#" class="hover:text-white transition">Soporte Médico</a></li>
                        </ul>
                    </div>

                    <!-- Contact info -->
                    <div class="space-y-4">
                        <h4 class="text-xs font-extrabold text-white uppercase tracking-widest">Contacto</h4>
                        <ul class="space-y-2 text-xs font-medium text-slate-400">
                            <li>📍 Av. de la Salud #450, Piso 5</li>
                            <li>📞 +52 (55) 5489-7700</li>
                            <li>✉️ contacto@prodoctor.com</li>
                        </ul>
                    </div>
                </div>

                <div class="pt-8 border-t border-slate-800 flex flex-col sm:flex-row items-center justify-between gap-4 text-xs font-medium text-slate-400">
                    <p>&copy; {{ new Date().getFullYear() }} ProDoctor. Todos los derechos reservados.</p>
                    <p>Laravel v{{ laravelVersion }} &middot; PHP v{{ phpVersion }}</p>
                </div>
            </div>
        </footer>
    </div>
</template>

<style scoped>
@keyframes fadeIn {
    from { opacity: 0; transform: translateY(12px); }
    to { opacity: 1; transform: translateY(0); }
}

.animate-fade-in {
    animation: fadeIn 0.6s cubic-bezier(0.16, 1, 0.3, 1) forwards;
}
</style>
