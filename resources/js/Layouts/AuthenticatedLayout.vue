<script setup>
import { Head, Link, useForm, usePage } from '@inertiajs/vue3';
import { computed, ref, watch } from 'vue';

const page = usePage();
const user = computed(() => page.props.auth.user);

// Format current date in Spanish
const formattedDate = computed(() => {
    const options = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
    const dateStr = new Date().toLocaleDateString('es-ES', options);
    return dateStr.charAt(0).toUpperCase() + dateStr.slice(1);
});

// Logout form
const logoutForm = useForm({});
const logout = () => {
    logoutForm.post(route('logout'));
};

// Flash messages alert
const flashSuccess = computed(() => page.props.flash?.success);
const showSuccessAlert = ref(false);

watch(flashSuccess, (newVal) => {
    if (newVal) {
        showSuccessAlert.value = true;
        setTimeout(() => {
            showSuccessAlert.value = false;
        }, 5000);
    }
}, { immediate: true });

const closeAlert = () => {
    showSuccessAlert.value = false;
};

// Check active route helpers
const isRouteActive = (pattern) => {
    return route().current(pattern);
};
</script>

<template>
    <div class="flex h-screen bg-slate-50 text-slate-800 font-sans overflow-hidden">
        
        <!-- Sidebar Izquierda - Estilo Premium Oscuro (#0f1f3d para Doctor, #0c3c3e para Paciente, #221830 para Admin) -->
        <aside
            class="w-64 text-white flex flex-col flex-shrink-0 shadow-lg z-20 transition-all duration-300"
            :class="user.rol === 'paciente' ? 'bg-[#0c3c3e]' : (user.rol === 'admin' ? 'bg-[#221830]' : 'bg-[#0f1f3d]')"
        >
            <!-- Brand Logo -->
            <div class="p-6 border-b border-slate-700/50 flex items-center gap-3">
                <div
                    class="w-9 h-9 rounded-xl flex items-center justify-center shadow-lg transition-all duration-300"
                    :class="user.rol === 'paciente' ? 'bg-gradient-to-tr from-teal-500 to-emerald-600 shadow-teal-500/20' : (user.rol === 'admin' ? 'bg-gradient-to-tr from-fuchsia-600 to-indigo-650 shadow-indigo-500/20' : 'bg-gradient-to-tr from-blue-600 to-indigo-500 shadow-blue-500/20')"
                >
                    <!-- ECG Line cross logo -->
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-5 h-5 text-white">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 12h3.5l1.5-3.5 2.5 7 2-4.5 1.5 1H20" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v2m0 12v2" />
                    </svg>
                </div>
                <div>
                    <h1 class="text-lg font-extrabold tracking-tight bg-gradient-to-r from-white to-blue-100 bg-clip-text text-transparent">ProDoctor</h1>
                    <span class="text-[9px] text-slate-400 font-bold tracking-widest uppercase">GABINETE MÉDICO</span>
                </div>
            </div>

            <!-- Doctor/Patient Profile Summary with colorful gradient avatar -->
            <div class="p-6 bg-[#0c182c]/40 border-b border-slate-700/30 flex items-center gap-3">
                <div
                    class="w-10 h-10 rounded-full border flex items-center justify-center font-bold text-white shadow-md shadow-black/10 transition-all duration-300"
                    :class="user.rol === 'paciente' ? 'bg-gradient-to-tr from-teal-500 to-emerald-600 border-teal-400/40' : (user.rol === 'admin' ? 'bg-gradient-to-tr from-fuchsia-600 to-indigo-650 border-fuchsia-400/40' : 'bg-gradient-to-tr from-blue-500 to-indigo-650 border-blue-400/40')"
                >
                    {{ user.nombre_usuario ? user.nombre_usuario.split(' ').map(n => n[0]).join('').substring(0, 2).toUpperCase() : 'US' }}
                </div>
                <div class="overflow-hidden">
                    <h2 class="text-sm font-bold truncate text-slate-100">{{ user.nombre_usuario }}</h2>
                    <span class="text-[10px] text-blue-400 font-bold uppercase tracking-wider">
                        {{ user.rol === 'doctor' ? 'Médico General' : (user.rol === 'admin' ? 'Administrador' : 'Paciente') }}
                    </span>
                </div>
            </div>

            <!-- Sidebar Navigation Links -->
            <nav class="flex-1 p-4 space-y-1 overflow-y-auto">
                <!-- Dashboard Doctor -->
                <Link
                    v-if="user.rol === 'doctor'"
                    :href="route('dashboard')"
                    class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-bold transition duration-150"
                    :class="isRouteActive('dashboard')
                        ? 'bg-blue-600/10 text-blue-300 border-l-4 border-blue-500'
                        : 'text-slate-400 hover:bg-slate-800/40 hover:text-white'"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12l8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25" />
                    </svg>
                    Dashboard
                </Link>

                <!-- Dashboard Paciente -->
                <Link
                    v-if="user.rol === 'paciente'"
                    :href="route('dashboard')"
                    class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-bold transition duration-150"
                    :class="isRouteActive('dashboard')
                        ? 'bg-teal-600/15 text-teal-350 border-l-4 border-teal-500'
                        : 'text-slate-400 hover:bg-slate-800/40 hover:text-white'"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12l8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25" />
                    </svg>
                    Mi Portal Médico
                </Link>

                <!-- Dashboard Admin -->
                <Link
                    v-if="user.rol === 'admin'"
                    :href="route('admin.dashboard')"
                    class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-bold transition duration-150"
                    :class="isRouteActive('admin.dashboard')
                        ? 'bg-fuchsia-600/15 text-fuchsia-355 border-l-4 border-fuchsia-500'
                        : 'text-slate-400 hover:bg-slate-800/40 hover:text-white'"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12l8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25" />
                    </svg>
                    Dashboard Admin
                </Link>

                <!-- Especialidades (Admin) -->
                <Link
                    v-if="user.rol === 'admin'"
                    :href="route('admin.specialties.index')"
                    class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-bold transition duration-150"
                    :class="isRouteActive('admin.specialties.*')
                        ? 'bg-fuchsia-600/15 text-fuchsia-355 border-l-4 border-fuchsia-500'
                        : 'text-slate-400 hover:bg-slate-800/40 hover:text-white'"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9.53 16.122l.75-2.524a2.25 2.25 0 011.085-1.344l8.36-4.52a.75.75 0 011.07.67v8.948a.75.75 0 01-.643.742l-9.845 1.637a2.25 2.25 0 01-1.777-.384l-1.07-.643z" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9V3m0 0L9 6m3-3l3 3" />
                    </svg>
                    Especialidades
                </Link>

                <!-- Doctores (Admin) -->
                <Link
                    v-if="user.rol === 'admin'"
                    :href="route('admin.doctors.index')"
                    class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-bold transition duration-150"
                    :class="isRouteActive('admin.doctors.*')
                        ? 'bg-fuchsia-600/15 text-fuchsia-355 border-l-4 border-fuchsia-500'
                        : 'text-slate-400 hover:bg-slate-800/40 hover:text-white'"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" />
                    </svg>
                    Doctores
                </Link>

                <!-- Usuarios (Admin) -->
                <Link
                    v-if="user.rol === 'admin'"
                    :href="route('admin.users.index')"
                    class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-bold transition duration-150"
                    :class="isRouteActive('admin.users.*')
                        ? 'bg-fuchsia-600/15 text-fuchsia-355 border-l-4 border-fuchsia-500'
                        : 'text-slate-400 hover:bg-slate-800/40 hover:text-white'"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M17.982 18.725A7.488 7.488 0 0012 15.75a7.488 7.488 0 00-5.982 2.975m11.963 0a9 9 0 10-11.963 0m11.963 0A8.966 8.966 0 0112 21a8.966 8.966 0 01-5.982-2.275M15 9.75a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                    Usuarios
                </Link>
                <!-- Pacientes (Doctor) -->
                <Link
                    v-if="user.rol === 'doctor'"
                    :href="route('doctor.patients.index')"
                    class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-bold transition duration-150"
                    :class="isRouteActive('doctor.patients.*')
                        ? 'bg-blue-600/10 text-blue-300 border-l-4 border-blue-500'
                        : 'text-slate-400 hover:bg-slate-800/40 hover:text-white'"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.109A2.25 2.25 0 0112.75 21.5h-1.5a2.25 2.25 0 01-2.25-2.263V19.13m0-.003c0-1.113.285-2.16.786-3.07M9.13 19.128a9.38 9.38 0 01-2.625.372 9.337 9.337 0 01-4.121-.952 4.125 4.125 0 017.533-2.493M12 11.25a3.75 3.75 0 100-7.5 3.75 3.75 0 000 7.5zm0 0a6 6 0 016 6v.38m-12 0a6 6 0 016-6z" />
                    </svg>
                    Pacientes
                </Link>

                <!-- Agenda de Citas (Doctor) -->
                <Link
                    v-if="user.rol === 'doctor'"
                    :href="route('doctor.appointments.index')"
                    class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-bold transition duration-150"
                    :class="isRouteActive('doctor.appointments.*')
                        ? 'bg-blue-600/10 text-blue-300 border-l-4 border-blue-500'
                        : 'text-slate-400 hover:bg-slate-800/40 hover:text-white'"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5" />
                    </svg>
                    Agenda de Citas
                </Link>

                <!-- Procedimientos (Doctor) -->
                <Link
                    v-if="user.rol === 'doctor'"
                    :href="route('doctor.procedures.index')"
                    class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-bold transition duration-150"
                    :class="isRouteActive('doctor.procedures.*')
                        ? 'bg-blue-600/10 text-blue-300 border-l-4 border-blue-500'
                        : 'text-slate-400 hover:bg-slate-800/40 hover:text-white'"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9.75 3.104v17.792m0-17.792L7.38 5.471m2.37-2.367l2.37 2.367M14.25 3.104v17.792" />
                    </svg>
                    Procedimientos
                </Link>

                <!-- Seguimiento (Doctor) -->
                <Link
                    v-if="user.rol === 'doctor'"
                    :href="route('doctor.followups.index')"
                    class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-bold transition duration-150"
                    :class="isRouteActive('doctor.followups.*')
                        ? 'bg-blue-600/10 text-blue-300 border-l-4 border-blue-500'
                        : 'text-slate-400 hover:bg-slate-800/40 hover:text-white'"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5" />
                    </svg>
                    Seguimiento
                </Link>
            </nav>

            <!-- Bottom Logout Area -->
            <div class="p-4 border-t border-slate-700/40">
                <button @click="logout" class="w-full flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl text-sm font-bold text-rose-300 hover:bg-rose-950/30 hover:text-rose-200 transition duration-150 cursor-pointer">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15M12 9l-3 3m0 0l3 3m-3-3h12.75" />
                    </svg>
                    Cerrar sesión
                </button>
            </div>
        </aside>

        <!-- Main Content Area -->
        <main class="flex-1 flex flex-col overflow-hidden relative">
            <!-- Global Toast Success Alert -->
            <transition
                enter-active-class="transform ease-out duration-300 transition"
                enter-from-class="translate-y-2 opacity-0 sm:translate-y-0 sm:translate-x-2"
                enter-to-class="translate-y-0 opacity-100 sm:translate-x-0"
                leave-active-class="transition ease-in duration-200"
                leave-from-class="opacity-100"
                leave-to-class="opacity-0"
            >
                <div v-if="showSuccessAlert && flashSuccess" class="absolute top-4 right-4 z-50 max-w-sm w-full bg-white border-l-4 border-l-emerald-500 rounded-xl shadow-xl border border-slate-200 p-4 flex items-start gap-3">
                    <div class="w-8 h-8 rounded-full bg-emerald-50 text-emerald-600 flex items-center justify-center flex-shrink-0">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-5 h-5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                        </svg>
                    </div>
                    <div class="flex-1">
                        <h4 class="text-sm font-bold text-slate-800">Operación Exitosa</h4>
                        <p class="text-xs text-slate-500 mt-0.5">{{ flashSuccess }}</p>
                    </div>
                    <button @click="closeAlert" class="text-slate-400 hover:text-slate-600 transition">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            </transition>

            <!-- Header superior -->
            <header class="h-16 bg-white border-b border-slate-200 px-8 flex items-center justify-between flex-shrink-0 shadow-sm z-10">
                <div>
                    <slot name="header" />
                </div>
                <div class="flex items-center gap-4">
                    <span class="text-sm font-semibold text-slate-500">{{ formattedDate }}</span>
                    <div class="relative">
                        <button class="w-9 h-9 rounded-full bg-slate-100 hover:bg-slate-200 flex items-center justify-center text-slate-650 transition cursor-pointer">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 005.454-1.31A8.967 8.967 0 0118 9.75v-.7V9A6 6 0 006 9v.75a8.967 8.967 0 01-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 01-5.714 0m5.714 0a3 3 0 11-5.714 0" />
                            </svg>
                            <span class="absolute top-1.5 right-1.5 w-2 h-2 rounded-full bg-amber-500 ring-2 ring-white"></span>
                        </button>
                    </div>
                </div>
            </header>

            <!-- Dashboard Content Slot Area -->
            <div class="flex-1 overflow-y-auto p-8">
                <slot />
            </div>
        </main>
    </div>
</template>
