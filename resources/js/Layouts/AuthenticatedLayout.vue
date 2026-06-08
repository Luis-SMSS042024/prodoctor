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

// Notifications state
const showNotifications = ref(false);
const notifications = ref([
    { id: 1, text: 'Nueva cita agendada por paciente Sofía Mendoza', time: 'Hace 10 min', read: false },
    { id: 2, text: 'El reporte de seguimiento clínico de Carlos López fue firmado.', time: 'Hace 1 hora', read: true },
    { id: 3, text: 'Nueva especialidad registrada: Dermatología.', time: 'Hace 1 día', read: true }
]);

const unreadCount = computed(() => notifications.value.filter(n => !n.read).length);

const toggleNotifications = () => {
    showNotifications.value = !showNotifications.value;
};

const markAllRead = () => {
    notifications.value.forEach(n => n.read = true);
};

// Check active route helpers
const isRouteActive = (pattern) => {
    return route().current(pattern);
};
</script>

<template>
    <div class="flex h-screen bg-[#030914] text-slate-300 font-sans overflow-hidden">
        
        <!-- Sidebar Izquierda - Estilo Premium Oscuro con Verde/Teal -->
        <aside
            class="w-64 text-white flex flex-col flex-shrink-0 bg-[#07111e] border-r border-[#13283f] shadow-lg z-20 transition-all duration-300"
        >
            <!-- Brand Logo -->
            <div class="p-6 border-b border-[#13283f] flex items-center gap-3">
                <div
                    class="w-9 h-9 rounded-xl flex items-center justify-center bg-gradient-to-tr from-emerald-500/20 to-teal-500/10 border border-[#00dfb2]/20 shadow-lg shadow-emerald-500/5 transition-all duration-300"
                >
                    <!-- ECG Line cross logo -->
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-5 h-5 text-[#00dfb2]">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 12h3.5l1.5-3.5 2.5 7 2-4.5 1.5 1H20" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v2m0 12v2" />
                    </svg>
                </div>
                <div>
                    <h1 class="text-lg font-extrabold tracking-tight text-white">ProDoctor</h1>
                    <span class="text-[9px] text-slate-500 font-bold tracking-widest uppercase block -mt-0.5">GABINETE MÉDICO</span>
                </div>
            </div>

            <!-- Doctor/Patient Profile Summary with colorful gradient avatar -->
            <div class="p-6 bg-[#040a12]/40 border-b border-[#13283f] flex items-center gap-3">
                <div
                    class="w-10 h-10 rounded-full border border-[#00dfb2]/20 flex items-center justify-center font-bold text-[#00dfb2] bg-emerald-500/10 shadow-md shadow-black/10 overflow-hidden transition-all duration-300 flex-shrink-0"
                >
                    <img v-if="user.foto" :src="'/' + user.foto" alt="Avatar" class="w-full h-full object-cover" />
                    <span v-else>
                        {{ user.nombre_usuario ? user.nombre_usuario.split(' ').map(n => n[0]).join('').substring(0, 2).toUpperCase() : 'US' }}
                    </span>
                </div>
                <div class="overflow-hidden flex-1">
                    <h2 class="text-sm font-bold truncate text-slate-100">{{ user.nombre_usuario }}</h2>
                    <div class="flex items-center justify-between mt-0.5">
                        <span class="text-[10px] text-[#00dfb2] font-bold uppercase tracking-wider">
                            {{ user.rol === 'doctor' ? 'Médico General' : (user.rol === 'admin' ? 'Administrador' : 'Paciente') }}
                        </span>
                        <Link 
                            :href="user.rol === 'paciente' ? route('paciente.dashboard', { tab: 'perfil' }) : route('profile.edit')" 
                            class="text-[10px] text-slate-550 hover:text-white transition font-bold underline"
                        >
                            Editar
                        </Link>
                    </div>
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
                        ? 'bg-emerald-500/10 text-[#00dfb2] border-l-4 border-[#00dfb2]'
                        : 'text-slate-400 hover:bg-[#040a12] hover:text-white'"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12l8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25" />
                    </svg>
                    Dashboard
                </Link>

                <!-- Dashboard Paciente -->
                <div v-if="user.rol === 'paciente'" class="space-y-1">
                    <Link
                        :href="route('dashboard')"
                        class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-bold transition duration-150"
                        :class="isRouteActive('dashboard')
                            ? 'bg-emerald-500/10 text-[#00dfb2] border-l-4 border-[#00dfb2]'
                            : 'text-slate-400 hover:bg-[#040a12] hover:text-white'"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12l8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25" />
                        </svg>
                        Mi Portal Médico
                    </Link>
                    <slot name="sidebar-paciente" />
                </div>

                <!-- Dashboard Admin -->
                <Link
                    v-if="user.rol === 'admin'"
                    :href="route('admin.dashboard')"
                    class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-bold transition duration-150"
                    :class="isRouteActive('admin.dashboard')
                        ? 'bg-emerald-500/10 text-[#00dfb2] border-l-4 border-[#00dfb2]'
                        : 'text-slate-400 hover:bg-[#040a12] hover:text-white'"
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
                        ? 'bg-emerald-500/10 text-[#00dfb2] border-l-4 border-[#00dfb2]'
                        : 'text-slate-400 hover:bg-[#040a12] hover:text-white'"
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
                        ? 'bg-emerald-500/10 text-[#00dfb2] border-l-4 border-[#00dfb2]'
                        : 'text-slate-400 hover:bg-[#040a12] hover:text-white'"
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
                        ? 'bg-emerald-500/10 text-[#00dfb2] border-l-4 border-[#00dfb2]'
                        : 'text-slate-400 hover:bg-[#040a12] hover:text-white'"
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
                        ? 'bg-emerald-500/10 text-[#00dfb2] border-l-4 border-[#00dfb2]'
                        : 'text-slate-400 hover:bg-[#040a12] hover:text-white'"
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
                        ? 'bg-emerald-500/10 text-[#00dfb2] border-l-4 border-[#00dfb2]'
                        : 'text-slate-400 hover:bg-[#040a12] hover:text-white'"
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
                        ? 'bg-emerald-500/10 text-[#00dfb2] border-l-4 border-[#00dfb2]'
                        : 'text-slate-400 hover:bg-[#040a12] hover:text-white'"
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
                        ? 'bg-emerald-500/10 text-[#00dfb2] border-l-4 border-[#00dfb2]'
                        : 'text-slate-400 hover:bg-[#040a12] hover:text-white'"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5" />
                    </svg>
                    Seguimiento
                </Link>
            </nav>

            <!-- Bottom Logout Area -->
            <div class="p-4 border-t border-[#13283f]">
                <button @click="logout" class="w-full flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl text-sm font-bold text-rose-450 hover:bg-rose-950/20 hover:text-rose-400 transition duration-150 cursor-pointer">
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
                <div v-if="showSuccessAlert && flashSuccess" class="absolute top-4 right-4 z-50 max-w-sm w-full bg-[#07111e] border-l-4 border-l-[#00dfb2] rounded-xl shadow-xl border border-[#13283f] p-4 flex items-start gap-3">
                    <div class="w-8 h-8 rounded-full bg-emerald-500/10 text-[#00dfb2] flex items-center justify-center flex-shrink-0">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-5 h-5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                        </svg>
                    </div>
                    <div class="flex-1">
                        <h4 class="text-sm font-bold text-white">Operación Exitosa</h4>
                        <p class="text-xs text-slate-450 mt-0.5">{{ flashSuccess }}</p>
                    </div>
                    <button @click="closeAlert" class="text-slate-500 hover:text-slate-300 transition">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            </transition>

            <!-- Header superior -->
            <header class="min-h-[4rem] py-3 bg-[#07111e] border-b border-[#13283f] px-8 flex items-center justify-between flex-shrink-0 shadow-sm z-30">
                <div class="flex-1 mr-6">
                    <slot name="header" />
                </div>
                <div class="flex items-center gap-4">
                    <span class="text-sm font-semibold text-slate-500">{{ formattedDate }}</span>
                    <div class="relative">
                        <button 
                            @click="toggleNotifications"
                            class="w-9 h-9 rounded-full bg-[#040a12] hover:bg-[#081325] flex items-center justify-center text-slate-400 hover:text-slate-200 transition cursor-pointer relative"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 005.454-1.31A8.967 8.967 0 0118 9.75v-.7V9A6 6 0 006 9v.75a8.967 8.967 0 01-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 01-5.714 0m5.714 0a3 3 0 11-5.714 0" />
                            </svg>
                            <span v-if="unreadCount > 0" class="absolute top-1.5 right-1.5 w-2.5 h-2.5 rounded-full bg-rose-500 ring-2 ring-[#07111e] animate-pulse"></span>
                        </button>

                        <!-- Dropdown de notificaciones -->
                        <div v-if="showNotifications" class="absolute right-0 mt-2 w-80 bg-[#07111e] border border-[#13283f] rounded-2xl shadow-xl z-50 overflow-hidden text-left py-2">
                            <div class="px-4 py-2 border-b border-[#13283f] flex items-center justify-between">
                                <span class="text-xs font-extrabold text-white">Notificaciones</span>
                                <button v-if="unreadCount > 0" @click="markAllRead" class="text-[10px] font-bold text-[#00dfb2] hover:text-[#00ffd5] hover:underline">Marcar leídas</button>
                            </div>
                            <div class="max-h-60 overflow-y-auto divide-y divide-[#13283f]">
                                <div v-for="notif in notifications" :key="notif.id" class="p-3.5 space-y-1 hover:bg-[#040a12] transition cursor-default">
                                    <div class="flex items-start justify-between gap-2">
                                        <p class="text-xs font-semibold text-slate-400" :class="{'text-white font-bold': !notif.read}">{{ notif.text }}</p>
                                        <span v-if="!notif.read" class="w-1.5 h-1.5 rounded-full bg-[#00dfb2] flex-shrink-0 mt-1"></span>
                                    </div>
                                    <span class="text-[10px] text-slate-600 font-bold">{{ notif.time }}</span>
                                </div>
                                <div v-if="notifications.length === 0" class="p-6 text-center text-slate-550 text-xs font-semibold">No tienes notificaciones.</div>
                            </div>
                        </div>
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
