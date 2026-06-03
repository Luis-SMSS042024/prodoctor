<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';

const props = defineProps({
    stats: {
        type: Object,
        required: true,
    },
    citas: {
        type: Array,
        required: true,
    },
});

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
            <h2 class="text-xl font-extrabold text-slate-800 tracking-tight">Panel de Control</h2>
        </template>

        <Head title="Panel del Doctor - ProDoctor" />

        <div class="space-y-8 select-none">
            <!-- Tarjetas de Métricas - Rediseñadas con bordes de color y sombras más pronunciadas -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <!-- Pacientes Totales - Borde Azul -->
                <Link :href="route('doctor.patients.index')" class="bg-white rounded-xl border-y border-r border-l-4 border-l-blue-500 border-slate-200 p-6 flex items-center justify-between shadow-md hover:shadow-xl transition-all duration-300 group cursor-pointer text-left">
                    <div class="space-y-2">
                        <span class="text-xs font-bold uppercase tracking-wider text-slate-400">PACIENTES TOTALES</span>
                        <div class="flex items-baseline gap-2">
                            <span class="text-3xl font-extrabold text-slate-800 tracking-tight">{{ stats.pacientes_totales }}</span>
                            <span class="text-[10px] font-bold text-blue-600 bg-blue-50 px-2 py-0.5 rounded border border-blue-200">+12 este mes</span>
                        </div>
                    </div>
                    <div class="w-12 h-12 rounded-xl bg-blue-50 group-hover:bg-blue-100 flex items-center justify-center text-blue-600 transition duration-250">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-6 h-6">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" />
                        </svg>
                    </div>
                </Link>

                <!-- Citas Hoy - Borde Verde -->
                <Link :href="route('doctor.appointments.index')" class="bg-white rounded-xl border-y border-r border-l-4 border-l-emerald-500 border-slate-200 p-6 flex items-center justify-between shadow-md hover:shadow-xl transition-all duration-300 group cursor-pointer text-left">
                    <div class="space-y-2">
                        <span class="text-xs font-bold uppercase tracking-wider text-slate-400">CITAS HOY</span>
                        <div class="flex items-baseline gap-2">
                            <span class="text-3xl font-extrabold text-slate-800 tracking-tight">{{ stats.citas_hoy }}</span>
                            <span v-if="stats.citas_hoy_pendientes > 0" class="text-[10px] font-bold text-amber-600 bg-amber-50 px-2 py-0.5 rounded border border-amber-200">{{ stats.citas_hoy_pendientes }} pendientes</span>
                            <span v-else class="text-[10px] font-bold text-emerald-600 bg-emerald-50 px-2 py-0.5 rounded border border-emerald-200">Al día</span>
                        </div>
                    </div>
                    <div class="w-12 h-12 rounded-xl bg-emerald-50 group-hover:bg-emerald-100 flex items-center justify-center text-emerald-600 transition duration-250">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-6 h-6">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5" />
                        </svg>
                    </div>
                </Link>

                <!-- Procedimientos - Borde Naranja -->
                <Link :href="route('doctor.procedures.index')" class="bg-white rounded-xl border-y border-r border-l-4 border-l-orange-500 border-slate-200 p-6 flex items-center justify-between shadow-md hover:shadow-xl transition-all duration-300 group cursor-pointer text-left">
                    <div class="space-y-2">
                        <span class="text-xs font-bold uppercase tracking-wider text-slate-400">PROCEDIMIENTOS</span>
                        <div class="flex items-baseline gap-2">
                            <span class="text-3xl font-extrabold text-slate-800 tracking-tight">{{ stats.procedimientos }}</span>
                            <span class="text-[10px] font-bold text-orange-600 bg-orange-50 px-2 py-0.5 rounded border border-orange-200">Activos</span>
                        </div>
                    </div>
                    <div class="w-12 h-12 rounded-xl bg-orange-50 group-hover:bg-orange-100 flex items-center justify-center text-orange-600 transition duration-250">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-6 h-6">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9.75 3.104v17.792m0-17.792L7.38 5.471m2.37-2.367l2.37 2.367M14.25 3.104v17.792" />
                        </svg>
                    </div>
                </Link>

                <!-- Seguimientos - Borde Amarillo/Ámbar -->
                <Link :href="route('doctor.followups.index')" class="bg-white rounded-xl border-y border-r border-l-4 border-l-amber-500 border-slate-200 p-6 flex items-center justify-between shadow-md hover:shadow-xl transition-all duration-300 group cursor-pointer text-left">
                    <div class="space-y-2">
                        <span class="text-xs font-bold uppercase tracking-wider text-slate-400">SEGUIMIENTOS</span>
                        <div class="flex items-baseline gap-2">
                            <span class="text-3xl font-extrabold text-slate-800 tracking-tight">{{ stats.seguimientos }}</span>
                            <span class="text-[10px] font-bold text-amber-600 bg-amber-50 px-2 py-0.5 rounded border border-amber-200">En control</span>
                        </div>
                    </div>
                    <div class="w-12 h-12 rounded-xl bg-amber-50 group-hover:bg-amber-100 flex items-center justify-center text-amber-600 transition duration-250">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-6 h-6">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5" />
                        </svg>
                    </div>
                </Link>
            </div>

            <!-- Contenido Principal Dividido en Columnas -->
            <div class="grid grid-cols-1 xl:grid-cols-3 gap-8">
                <!-- Tabla de Citas del Día (Columna Izquierda 2/3) -->
                <div class="xl:col-span-2 bg-white rounded-xl border border-slate-200 overflow-hidden flex flex-col shadow-md">
                    <div class="p-6 border-b border-slate-200 flex items-center justify-between">
                        <div>
                            <h3 class="text-lg font-bold text-slate-800">Citas del Día</h3>
                            <p class="text-xs text-slate-500 mt-1">Monitoreo de citas programadas para la jornada de hoy.</p>
                        </div>
                        <Link :href="route('doctor.appointments.index')" class="text-sm font-bold text-blue-600 hover:text-blue-700 hover:underline">Ver todas &rarr;</Link>
                    </div>
                    <div class="overflow-x-auto flex-1">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="bg-slate-50 border-b border-slate-200">
                                    <th class="px-6 py-4 text-xs font-bold uppercase tracking-wider text-slate-400">Paciente</th>
                                    <th class="px-6 py-4 text-xs font-bold uppercase tracking-wider text-slate-400">Hora</th>
                                    <th class="px-6 py-4 text-xs font-bold uppercase tracking-wider text-slate-400">Tipo de Consulta</th>
                                    <th class="px-6 py-4 text-xs font-bold uppercase tracking-wider text-slate-400">Estado</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                <tr v-for="cita in citas" :key="cita.id_cita" class="hover:bg-slate-50/50 transition">
                                    <td class="px-6 py-4 font-bold text-slate-800 text-sm">{{ cita.paciente }}</td>
                                    <td class="px-6 py-4 text-sm font-semibold text-slate-500">{{ cita.hora }}</td>
                                    <td class="px-6 py-4 text-sm text-slate-650 truncate max-w-xs">{{ cita.tipo }}</td>
                                    <td class="px-6 py-4">
                                        <span class="px-3 py-1 text-xs font-bold rounded-full border" :class="getStatusClass(cita.estado)">
                                            {{ cita.estado }}
                                        </span>
                                    </td>
                                </tr>
                                <tr v-if="citas.length === 0">
                                    <td colspan="4" class="px-6 py-8 text-center text-slate-400">No hay citas programadas para hoy.</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Acciones Rápidas (Columna Derecha 1/3) -->
                <div class="bg-white rounded-xl border border-slate-200 p-6 flex flex-col space-y-6 shadow-md">
                    <div>
                        <h3 class="text-lg font-bold text-slate-800 tracking-tight">Acciones Rápidas</h3>
                        <p class="text-xs text-slate-500 mt-1">Accesos directos para la gestión médica diaria.</p>
                    </div>
                    <div class="flex-1 space-y-3">
                        <Link :href="route('doctor.patients.create')" class="flex items-center justify-between p-4 rounded-xl border border-slate-150 hover:border-blue-200 hover:bg-blue-50/30 transition group">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-lg bg-blue-50 group-hover:bg-blue-100 flex items-center justify-center text-blue-600">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                                    </svg>
                                </div>
                                <div>
                                    <h4 class="text-sm font-bold text-slate-800 group-hover:text-blue-800">Nuevo Paciente</h4>
                                    <p class="text-[11px] text-slate-400 mt-0.5 font-medium">Registrar en el sistema</p>
                                </div>
                            </div>
                            <span class="text-slate-300 group-hover:text-blue-600 transition font-bold">&rarr;</span>
                        </Link>

                        <Link :href="route('doctor.appointments.index')" class="flex items-center justify-between p-4 rounded-xl border border-slate-150 hover:border-emerald-200 hover:bg-emerald-50/30 transition group">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-lg bg-emerald-50 group-hover:bg-emerald-100 flex items-center justify-center text-emerald-600">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5" />
                                    </svg>
                                </div>
                                <div>
                                    <h4 class="text-sm font-bold text-slate-800 group-hover:text-emerald-800">Programar Cita</h4>
                                    <p class="text-[11px] text-slate-400 mt-0.5 font-medium">Asignar horario y especialidad</p>
                                </div>
                            </div>
                            <span class="text-slate-300 group-hover:text-emerald-600 transition font-bold">&rarr;</span>
                        </Link>

                        <Link :href="route('doctor.procedures.index')" class="flex items-center justify-between p-4 rounded-xl border border-slate-150 hover:border-orange-200 hover:bg-orange-50/30 transition group cursor-pointer">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-lg bg-orange-50 group-hover:bg-orange-100 flex items-center justify-center text-orange-600">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M9.75 3.104v17.792m0-17.792" />
                                    </svg>
                                </div>
                                <div>
                                    <h4 class="text-sm font-bold text-slate-800 group-hover:text-orange-800">Registrar Procedimiento</h4>
                                    <p class="text-[11px] text-slate-400 mt-0.5 font-medium">Asignar procedimiento médico</p>
                                </div>
                            </div>
                            <span class="text-slate-300 group-hover:text-orange-600 transition font-bold">&rarr;</span>
                        </Link>

                        <Link :href="route('doctor.followups.index')" class="flex items-center justify-between p-4 rounded-xl border border-slate-150 hover:border-amber-200 hover:bg-amber-50/30 transition group cursor-pointer">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-lg bg-amber-50 group-hover:bg-amber-100 flex items-center justify-center text-amber-600">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5" />
                                    </svg>
                                </div>
                                <div>
                                    <h4 class="text-sm font-bold text-slate-800 group-hover:text-amber-800">Ver Seguimientos</h4>
                                    <p class="text-[11px] text-slate-400 mt-0.5 font-medium">Pendientes de revisión y firma</p>
                                </div>
                            </div>
                            <span class="text-slate-300 group-hover:text-amber-600 transition font-bold">&rarr;</span>
                        </Link>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
