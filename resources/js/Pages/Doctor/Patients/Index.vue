<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { ref, watch } from 'vue';

const props = defineProps({
    patients: {
        type: Object,
        required: true,
    },
    filters: {
        type: Object,
        default: () => ({ search: '' }),
    },
});

const search = ref(props.filters.search || '');

// Perform search request
const performSearch = () => {
    router.get(route('doctor.patients.index'), { search: search.value }, {
        preserveState: true,
        replace: true,
    });
};

// Clear search filter
const clearSearch = () => {
    search.value = '';
    performSearch();
};

// Calculate age from birthdate
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

// Helper for patient profile initials
const getInitials = (nombres, apellidos) => {
    const n = nombres ? nombres.split(' ')[0][0] : '';
    const a = apellidos ? apellidos.split(' ')[0][0] : '';
    return (n + a).toUpperCase();
};
</script>

<template>
    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between w-full">
                <h2 class="text-xl font-extrabold text-slate-800 tracking-tight">Expedientes de Pacientes</h2>
                <Link
                    :href="route('doctor.patients.create')"
                    class="inline-flex items-center gap-2 px-4 py-2.5 bg-gradient-to-r from-blue-600 to-indigo-650 hover:from-blue-700 hover:to-indigo-755 text-white rounded-xl text-xs font-bold shadow-md shadow-blue-500/20 active:scale-[0.98] transition cursor-pointer"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-4 h-4">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                    </svg>
                    Registrar Paciente
                </Link>
            </div>
        </template>

        <Head title="Listado de Pacientes - ProDoctor" />

        <div class="space-y-6 select-none">
            <!-- Barra de Filtros y Búsqueda -->
            <div class="bg-white rounded-xl border border-slate-200 p-4 flex flex-col md:flex-row items-center justify-between gap-4 shadow-sm">
                <!-- Buscador -->
                <div class="w-full md:w-96 relative">
                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.637 10.637z" />
                        </svg>
                    </div>
                    <input
                        type="text"
                        placeholder="Buscar por nombre, apellido, DUI o teléfono..."
                        v-model="search"
                        @keyup.enter="performSearch"
                        class="block w-full pl-10 pr-10 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-slate-800 text-xs placeholder-slate-450 focus:outline-none focus:bg-white focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 transition"
                    />
                    <button
                        v-if="search"
                        @click="clearSearch"
                        class="absolute inset-y-0 right-0 pr-3 flex items-center text-slate-400 hover:text-slate-600 cursor-pointer"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-4 h-4">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <div class="flex items-center gap-2 w-full md:w-auto">
                    <button
                        @click="performSearch"
                        class="w-full md:w-auto px-4 py-2.5 bg-slate-100 hover:bg-slate-200 border border-slate-200 rounded-xl text-xs font-bold text-slate-650 hover:text-slate-800 transition cursor-pointer"
                    >
                        Buscar
                    </button>
                </div>
            </div>

            <!-- Tabla de Expedientes de Pacientes -->
            <div class="bg-white rounded-xl border-y border-r border-l-4 border-l-blue-500 border-slate-200 overflow-hidden shadow-md">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-slate-50 border-b border-slate-200">
                                <th class="px-6 py-4 text-xs font-bold uppercase tracking-wider text-slate-400">Expediente</th>
                                <th class="px-6 py-4 text-xs font-bold uppercase tracking-wider text-slate-400">Paciente</th>
                                <th class="px-6 py-4 text-xs font-bold uppercase tracking-wider text-slate-400">Edad / Sexo</th>
                                <th class="px-6 py-4 text-xs font-bold uppercase tracking-wider text-slate-400">DUI</th>
                                <th class="px-6 py-4 text-xs font-bold uppercase tracking-wider text-slate-400">Teléfono</th>
                                <th class="px-6 py-4 text-xs font-bold uppercase tracking-wider text-slate-400">Tipo Sangre</th>
                                <th class="px-6 py-4 text-xs font-bold uppercase tracking-wider text-slate-400 text-right">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            <tr v-for="patient in patients.data" :key="patient.id_paciente" class="hover:bg-slate-50/50 transition">
                                <!-- ID / Expediente -->
                                <td class="px-6 py-4 font-extrabold text-blue-600 text-xs">
                                    #PAC-{{ String(patient.id_paciente).padStart(4, '0') }}
                                </td>
                                <!-- Nombre Paciente -->
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="w-8 h-8 rounded-full bg-gradient-to-tr from-blue-500/10 to-indigo-500/10 border border-blue-200 flex items-center justify-center font-bold text-xs text-blue-700">
                                            {{ getInitials(patient.nombres, patient.apellidos) }}
                                        </div>
                                        <div>
                                            <h4 class="font-bold text-slate-800 text-sm">{{ patient.nombres }} {{ patient.apellidos }}</h4>
                                            <span class="text-[10px] text-slate-400 truncate max-w-xs block mt-0.5">{{ patient.direccion }}</span>
                                        </div>
                                    </div>
                                </td>
                                <!-- Edad y Sexo -->
                                <td class="px-6 py-4">
                                    <div class="text-sm font-semibold text-slate-700">{{ getAge(patient.fecha_nacimiento) }}</div>
                                    <span class="text-[10px] text-slate-400 font-bold uppercase tracking-wide mt-0.5 block">{{ patient.sexo }}</span>
                                </td>
                                <!-- DUI -->
                                <td class="px-6 py-4 text-sm font-medium text-slate-600">
                                    {{ patient.dui || 'N/A' }}
                                </td>
                                <!-- Teléfono -->
                                <td class="px-6 py-4 text-sm font-medium text-slate-650">
                                    {{ patient.telefono }}
                                </td>
                                <!-- Tipo de Sangre -->
                                <td class="px-6 py-4">
                                    <span v-if="patient.tipo_sangre" class="px-2 py-0.5 rounded text-[10px] font-extrabold border bg-slate-50 text-slate-600 border-slate-200">
                                        {{ patient.tipo_sangre }}
                                    </span>
                                    <span v-else class="text-slate-400 text-xs">N/A</span>
                                </td>
                                <!-- Botones de Acciones -->
                                <td class="px-6 py-4 text-right space-x-2">
                                    <Link
                                        :href="route('doctor.patients.show', patient.id_paciente)"
                                        class="inline-flex items-center px-3 py-1.5 bg-blue-50 hover:bg-blue-100 border border-blue-200 rounded-lg text-[11px] font-bold text-blue-700 transition cursor-pointer"
                                    >
                                        Ver Ficha
                                    </Link>
                                    <Link
                                        :href="route('doctor.patients.edit', patient.id_paciente)"
                                        class="inline-flex items-center px-3 py-1.5 bg-slate-50 hover:bg-slate-100 border border-slate-200 rounded-lg text-[11px] font-bold text-slate-700 transition cursor-pointer"
                                    >
                                        Editar
                                    </Link>
                                </td>
                            </tr>
                            <tr v-if="patients.data.length === 0">
                                <td colspan="7" class="px-6 py-12 text-center text-slate-400 font-medium">No se encontraron expedientes de pacientes.</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Paginación -->
                <div v-if="patients.links && patients.links.length > 3" class="px-6 py-4 bg-slate-50 border-t border-slate-200 flex items-center justify-between text-xs">
                    <div class="text-slate-500 font-medium">
                        Mostrando del {{ patients.from || 0 }} al {{ patients.to || 0 }} de {{ patients.total }} expedientes
                    </div>
                    <div class="flex items-center gap-1">
                        <template v-for="(link, key) in patients.links" :key="key">
                            <div v-if="link.url === null" class="px-3 py-2 bg-slate-100 border border-slate-200 rounded-lg text-slate-400 font-semibold cursor-not-allowed select-none" v-html="link.label"></div>
                            <Link
                                v-else
                                :href="link.url"
                                class="px-3 py-2 border rounded-lg font-semibold transition cursor-pointer"
                                :class="link.active 
                                    ? 'bg-blue-600 border-blue-600 text-white font-bold' 
                                    : 'bg-white border-slate-200 hover:bg-slate-50 text-slate-650 hover:text-slate-800'"
                                v-html="link.label"
                            />
                        </template>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
