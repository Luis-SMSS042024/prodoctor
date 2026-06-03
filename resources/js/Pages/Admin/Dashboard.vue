<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';

const props = defineProps({
    stats: {
        type: Object,
        required: true,
    },
    recentAppointments: {
        type: Array,
        required: true,
    },
    recentUsers: {
        type: Array,
        required: true,
    },
});

// Helper for user roles badges
const getRoleBadgeClass = (role) => {
    switch (role) {
        case 'doctor':
            return 'bg-blue-50 text-blue-700 border-blue-200';
        case 'paciente':
            return 'bg-teal-50 text-teal-700 border-teal-200';
        case 'admin':
            return 'bg-fuchsia-50 text-fuchsia-700 border-fuchsia-200';
        default:
            return 'bg-slate-50 text-slate-700 border-slate-200';
    }
};

const getStatusClass = (status) => {
    switch (status) {
        case 'Atendida':
            return 'bg-emerald-50 text-emerald-700 border-emerald-250';
        case 'Confirmada':
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
            <h2 class="text-xl font-extrabold text-slate-800 tracking-tight">Consola de Administración</h2>
        </template>

        <Head title="Panel del Administrador - ProDoctor" />

        <div class="space-y-8 select-none">
            
            <!-- Cards de Estadísticas del Administrador -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <!-- Especialidades - Borde Fucsia -->
                <Link :href="route('admin.specialties.index')" class="bg-white rounded-xl border-y border-r border-l-4 border-l-fuchsia-500 border-slate-200 p-6 flex items-center justify-between shadow-md hover:shadow-xl transition-all duration-300 group cursor-pointer text-left">
                    <div class="space-y-2">
                        <span class="text-xs font-bold uppercase tracking-wider text-slate-400">ESPECIALIDADES</span>
                        <div class="flex items-baseline gap-2">
                            <span class="text-3xl font-extrabold text-slate-800 tracking-tight">{{ stats.especialidades }}</span>
                        </div>
                    </div>
                    <div class="w-12 h-12 rounded-xl bg-fuchsia-50 group-hover:bg-fuchsia-100 flex items-center justify-center text-fuchsia-600 transition duration-250">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-6 h-6">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9.53 16.122l.75-2.524a2.25 2.25 0 011.085-1.344l8.36-4.52a.75.75 0 011.07.67v8.948z" />
                        </svg>
                    </div>
                </Link>

                <!-- Doctores - Borde Morado -->
                <Link :href="route('admin.doctors.index')" class="bg-white rounded-xl border-y border-r border-l-4 border-l-purple-500 border-slate-200 p-6 flex items-center justify-between shadow-md hover:shadow-xl transition-all duration-300 group cursor-pointer text-left">
                    <div class="space-y-2">
                        <span class="text-xs font-bold uppercase tracking-wider text-slate-400">DOCTORES</span>
                        <div class="flex items-baseline gap-2">
                            <span class="text-3xl font-extrabold text-slate-800 tracking-tight">{{ stats.doctores }}</span>
                        </div>
                    </div>
                    <div class="w-12 h-12 rounded-xl bg-purple-50 group-hover:bg-purple-100 flex items-center justify-center text-purple-600 transition duration-250">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-6 h-6">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75" />
                        </svg>
                    </div>
                </Link>

                <!-- Usuarios - Borde Indigo -->
                <Link :href="route('admin.users.index')" class="bg-white rounded-xl border-y border-r border-l-4 border-l-indigo-500 border-slate-200 p-6 flex items-center justify-between shadow-md hover:shadow-xl transition-all duration-300 group cursor-pointer text-left">
                    <div class="space-y-2">
                        <span class="text-xs font-bold uppercase tracking-wider text-slate-400">CUENTAS DE USUARIOS</span>
                        <div class="flex items-baseline gap-2">
                            <span class="text-3xl font-extrabold text-slate-800 tracking-tight">{{ stats.usuarios }}</span>
                        </div>
                    </div>
                    <div class="w-12 h-12 rounded-xl bg-indigo-50 group-hover:bg-indigo-100 flex items-center justify-center text-indigo-600 transition duration-250">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-6 h-6">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M17.982 18.725A7.488 7.488 0 0012 15.75a7.488 7.488 0 00-5.982 2.975m11.963 0" />
                        </svg>
                    </div>
                </Link>

                <!-- Pacientes (solo lectura para admin) -->
                <div class="bg-white rounded-xl border-y border-r border-l-4 border-l-teal-500 border-slate-200 p-6 flex items-center justify-between shadow-md transition-all duration-300">
                    <div class="space-y-2">
                        <span class="text-xs font-bold uppercase tracking-wider text-slate-400">EXPEDIENTES PACIENTES</span>
                        <div class="flex items-baseline gap-2">
                            <span class="text-3xl font-extrabold text-slate-800 tracking-tight">{{ stats.pacientes }}</span>
                        </div>
                    </div>
                    <div class="w-12 h-12 rounded-xl bg-teal-50 flex items-center justify-center text-teal-600">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-6 h-6">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h3.75M9 15h3.375M9 18h3.375m-6-3h.008v.008H6V15zm0 3h.008v.008H6V18zm0-6h.008v.008H6V12m1.5-6h.008v.008H7.5V6zm3 0h.008v.008h-.008V6zm3 0h.008v.008h-.008V6zm3 0h.008v.008h-.008V6zm3 0h.008v.008h-.008V6zm-9 3h.008v.008H9V9zm3 0h.008v.008h-.008V9zm3 0h.008v.008h-.008V9zm3 0h.008v.008h-.008V9zm-9 3h.008v.008H12v-.008zm1.5-9a.75.75 0 01.75-.75h7.5a.75.75 0 01.75.75v16.5a.75.75 0 01-.75.75h-7.5a.75.75 0 01-.75-.75V3z" />
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Dashboard Content: Recientes Citas y Recientes Usuarios -->
            <div class="grid grid-cols-1 xl:grid-cols-3 gap-8">
                
                <!-- Recientes Citas del Gabinete (Columna Izquierda 2/3) -->
                <div class="xl:col-span-2 bg-white rounded-xl border border-slate-200 overflow-hidden flex flex-col shadow-md">
                    <div class="p-6 border-b border-slate-200">
                        <h3 class="text-base font-bold text-slate-800">Recientes Citas Médicas</h3>
                        <p class="text-xs text-slate-500 mt-1">Monitoreo de programación y flujo en el gabinete.</p>
                    </div>
                    <div class="overflow-x-auto flex-1">
                        <table class="w-full text-left border-collapse text-xs">
                            <thead>
                                <tr class="bg-slate-50 border-b border-slate-200">
                                    <th class="px-6 py-4 text-xs font-bold uppercase tracking-wider text-slate-400">Paciente</th>
                                    <th class="px-6 py-4 text-xs font-bold uppercase tracking-wider text-slate-400">Doctor</th>
                                    <th class="px-6 py-4 text-xs font-bold uppercase tracking-wider text-slate-400">Fecha</th>
                                    <th class="px-6 py-4 text-xs font-bold uppercase tracking-wider text-slate-400">Estado</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                <tr v-for="cita in recentAppointments" :key="cita.id_cita" class="hover:bg-slate-50/50 transition">
                                    <td class="px-6 py-4 font-bold text-slate-800">{{ cita.paciente }}</td>
                                    <td class="px-6 py-4 font-semibold text-slate-655">{{ cita.doctor }}</td>
                                    <td class="px-6 py-4 text-slate-500 font-semibold">
                                        {{ new Date(cita.fecha + 'T00:00:00').toLocaleDateString('es-ES', { day: 'numeric', month: 'short', year: 'numeric' }) }}
                                    </td>
                                    <td class="px-6 py-4">
                                        <span class="px-2 py-0.5 rounded text-[9px] font-bold border uppercase" :class="getStatusClass(cita.estado)">
                                            {{ cita.estado }}
                                        </span>
                                    </td>
                                </tr>
                                <tr v-if="recentAppointments.length === 0">
                                    <td colspan="4" class="px-6 py-8 text-center text-slate-400">No hay citas programadas registradas en el sistema.</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Recientes Usuarios Registrados (Columna Derecha 1/3) -->
                <div class="bg-white rounded-xl border border-slate-200 p-6 flex flex-col space-y-6 shadow-md">
                    <div>
                        <h3 class="text-base font-bold text-slate-800 tracking-tight">Últimas Cuentas</h3>
                        <p class="text-xs text-slate-500 mt-1">Cuentas creadas recientemente en la plataforma.</p>
                    </div>
                    <div class="flex-1 space-y-4">
                        <div
                            v-for="user in recentUsers"
                            :key="user.id_usuario"
                            class="flex items-center justify-between p-3 rounded-xl border border-slate-150 hover:bg-slate-50/50 transition"
                        >
                            <div>
                                <h4 class="text-xs font-bold text-slate-800">{{ user.nombre_usuario }}</h4>
                                <p class="text-[10px] text-slate-400 mt-0.5 truncate max-w-[150px]">{{ user.correo }}</p>
                            </div>
                            <span class="px-2 py-0.5 rounded text-[8px] font-black uppercase border shrink-0" :class="getRoleBadgeClass(user.rol)">
                                {{ user.rol }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
