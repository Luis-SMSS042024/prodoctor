<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps({
    stats: {
        type: Object,
        required: true,
    },
    citas: {
        type: Array,
        required: true,
    },
    citasPendientes: {
        type: Array,
        default: () => [],
    },
});

const updateCitaStatus = (id_cita, estado) => {
    if (confirm(estado === 'Confirmada' ? '¿Estás seguro de que deseas confirmar esta cita?' : '¿Estás seguro de que deseas rechazar/cancelar esta cita?')) {
        router.patch(route('doctor.appointments.status', id_cita), {
            estado: estado
        }, {
            preserveScroll: true,
        });
    }
};

// AI Diagnostic Assistant state & logic
const showAIModal = ref(false);
const symptomsInput = ref('');
const diagnosisResult = ref(null);
const isLoadingAI = ref(false);

const analyzeSymptoms = () => {
    if (!symptomsInput.value.trim()) return;
    isLoadingAI.value = true;
    diagnosisResult.value = null;
    
    setTimeout(() => {
        const query = symptomsInput.value.toLowerCase();
        let title = 'Sintomatología Inespecífica';
        let differential = 'Requiere examen clínico completo e historia clínica detallada.';
        let tests = 'Hemograma completo, PCR (Proteína C Reactiva), Examen General de Orina.';
        let treatment = 'Sintomático (hidratación abundante, reposo relativo y analgésicos según tolerancia).';
        let severity = 'Baja';
        let severityColor = 'text-blue-700 bg-blue-50 border-blue-200';

        if (query.includes('fiebre') && query.includes('tos') && (query.includes('garganta') || query.includes('gripe') || query.includes('secrecion'))) {
            title = 'Faringoamigdalitis Aguda / Rinofaringitis';
            differential = 'Influenza Estacional, Faringitis estreptocócica (si hay placas purulentas), COVID-19.';
            tests = 'Hisopado faríngeo rápido, hemograma completo, prueba rápida de antígenos nasales.';
            treatment = 'Paracetamol 500mg VO cada 8 horas por 3 días. Hidratación abundante. Si se observan placas purulentas, valorar amoxicilina 875mg c/12h por 7 días.';
            severity = 'Baja';
            severityColor = 'text-emerald-700 bg-emerald-50 border-emerald-250';
        } else if (query.includes('disnea') || query.includes('dolor de pecho') || query.includes('dolor toracico') || query.includes('opresion') || query.includes('pecho') || query.includes('infarto')) {
            title = 'Sospecha de Síndrome Coronario Agudo (SICA)';
            differential = 'Angina inestable, Infarto agudo al miocardio con/sin elevación del ST, Tromboembolismo pulmonar, pericarditis aguda.';
            tests = 'Electrocardiograma de 12 derivaciones (inmediato), Troponinas ultrasensibles seriadas, enzimas cardíacas (CK-MB), Radiografía de tórax.';
            treatment = 'Derivar de inmediato a sala de urgencias. Administrar Oxígeno si SatO2 < 90%. Ácido acetilsalicílico (Aspirina) 160-325 mg masticable. Monitoreo ECG continuo.';
            severity = 'Crítica / Emergencia Médica';
            severityColor = 'text-rose-700 bg-rose-50 border-rose-250 animate-pulse';
        } else if (query.includes('dolor de cabeza') || query.includes('cefalea') || query.includes('luz') || query.includes('migraña')) {
            title = 'Cefalea Migrañosa / Migraña Aguda';
            differential = 'Cefalea tensional, cefalea en racimos, crisis de hipertensión arterial sistémica.';
            tests = 'No indicados de rutina a menos que existan signos de alarma (ej. inicio súbito atípico, déficit neurológico focal).';
            treatment = 'Sumatriptán 50mg VO al inicio del dolor. Paracetamol 1g + Cafeína VO si es leve. Reposo en habitación oscura y silenciosa.';
            severity = 'Moderada';
            severityColor = 'text-amber-700 bg-amber-50 border-amber-250';
        } else if (query.includes('dolor abdominal') && (query.includes('fosa') || query.includes('derecha') || query.includes('apendicitis') || query.includes('vomito'))) {
            title = 'Sospecha de Apendicitis Aguda';
            differential = 'Adenitis mesentérica, diverticulitis de Meckel, quiste de ovario torcido o roto, embarazo ectópico.';
            tests = 'Ultrasonografía abdominal o TAC de abdomen con contraste, Hemograma completo (buscar leucocitosis con desviación a la izquierda), PCR.';
            treatment = 'Mantener en ayuno absoluto (NPO). Canalizar vía periférica para hidratación intravenosa. Evaluación urgente por cirujano general.';
            severity = 'Alta (Urgencia Quirúrgica)';
            severityColor = 'text-orange-700 bg-orange-50 border-orange-250';
        } else if (query.includes('sed') || query.includes('orina') || query.includes('poliuria') || query.includes('polidipsia') || query.includes('diabetes')) {
            title = 'Sospecha de Diabetes Mellitus Tipo 2';
            differential = 'Diabetes insípida, Polidipsia psicógena, hiperglucemia secundaria a uso de glucocorticoides.';
            tests = 'Glucemia plasmática en ayunas, Hemoglobina glicosilada (HbA1c), Examen general de orina (buscar glucosuria y cetonuria).';
            treatment = 'Establecer plan de alimentación hipocalórica y ejercicio aeróbico regular. Si HbA1c > 7.5%, considerar inicio de Metformina 850mg VO c/24h junto con los alimentos.';
            severity = 'Moderada / Control ambulatorio';
            severityColor = 'text-amber-700 bg-amber-50 border-amber-250';
        }

        diagnosisResult.value = {
            title,
            differential,
            tests,
            treatment,
            severity,
            severityColor
        };
        isLoadingAI.value = false;
    }, 800);
};

const resetAI = () => {
    symptomsInput.value = '';
    diagnosisResult.value = null;
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

            <!-- SVG Gradient Definitions -->
            <svg class="w-0 h-0 absolute">
                <defs>
                    <linearGradient id="chart-blue-grad" x1="0" y1="0" x2="0" y2="1">
                        <stop offset="0%" stop-color="#3b82f6" stop-opacity="0.3" />
                        <stop offset="100%" stop-color="#3b82f6" stop-opacity="0" />
                    </linearGradient>
                    <linearGradient id="chart-emerald-grad" x1="0" y1="0" x2="0" y2="1">
                        <stop offset="0%" stop-color="#10b981" stop-opacity="0.3" />
                        <stop offset="100%" stop-color="#10b981" stop-opacity="0" />
                    </linearGradient>
                </defs>
            </svg>

            <!-- Sección de Gráficos e Insights Analíticos -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                <!-- Gráfico 1: Evolución de Consultas (Semanal) -->
                <div class="bg-white rounded-xl border border-slate-200 p-6 shadow-md">
                    <div class="flex items-center justify-between mb-4">
                        <div>
                            <h3 class="text-base font-extrabold text-slate-800">Evolución de Consultas</h3>
                            <p class="text-[11px] text-slate-400 font-medium">Volumen de citas atendidas en los últimos 6 días</p>
                        </div>
                        <span class="text-xs font-bold text-blue-600 bg-blue-50 px-2.5 py-1 rounded-lg border border-blue-100">Vista Semanal</span>
                    </div>

                    <!-- SVG Line & Area Chart -->
                    <div class="relative pt-2">
                        <svg viewBox="0 0 500 180" class="w-full h-44 overflow-visible">
                            <!-- Grid Lines -->
                            <line x1="0" y1="150" x2="500" y2="150" stroke="#f1f5f9" stroke-width="1.5" />
                            <line x1="0" y1="100" x2="500" y2="100" stroke="#f1f5f9" stroke-width="1.5" />
                            <line x1="0" y1="50" x2="500" y2="50" stroke="#f1f5f9" stroke-width="1.5" />

                            <!-- Area Path -->
                            <path d="M 10 150 L 10 120 L 100 75 L 190 110 L 280 40 L 370 65 L 460 135 L 460 150 Z" fill="url(#chart-blue-grad)" />

                            <!-- Line Path -->
                            <path d="M 10 120 L 100 75 L 190 110 L 280 40 L 370 65 L 460 135" fill="none" stroke="#2563eb" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" />

                            <!-- Data Points & Tooltips -->
                            <!-- Lun (12) -->
                            <circle cx="10" cy="120" r="5" fill="#2563eb" stroke="#ffffff" stroke-width="2" class="cursor-pointer hover:r-7 transition-all" />
                            <!-- Mar (21) -->
                            <circle cx="100" cy="75" r="5" fill="#2563eb" stroke="#ffffff" stroke-width="2" class="cursor-pointer hover:r-7 transition-all" />
                            <!-- Mié (14) -->
                            <circle cx="190" cy="110" r="5" fill="#2563eb" stroke="#ffffff" stroke-width="2" class="cursor-pointer hover:r-7 transition-all" />
                            <!-- Jue (28) -->
                            <circle cx="280" cy="40" r="5" fill="#2563eb" stroke="#ffffff" stroke-width="2" class="cursor-pointer hover:r-7 transition-all" />
                            <!-- Vie (23) -->
                            <circle cx="370" cy="65" r="5" fill="#2563eb" stroke="#ffffff" stroke-width="2" class="cursor-pointer hover:r-7 transition-all" />
                            <!-- Sáb (9) -->
                            <circle cx="460" cy="135" r="5" fill="#2563eb" stroke="#ffffff" stroke-width="2" class="cursor-pointer hover:r-7 transition-all" />
                        </svg>
                        
                        <!-- Axis Labels -->
                        <div class="flex justify-between text-[10px] font-extrabold text-slate-400 tracking-wider mt-2 px-1">
                            <span>LUN (12)</span>
                            <span>MAR (21)</span>
                            <span>MIÉ (14)</span>
                            <span>JUE (28)</span>
                            <span>VIE (23)</span>
                            <span>SÁB (9)</span>
                        </div>
                    </div>
                </div>

                <!-- Gráfico 2: Diagnósticos / Categorías Más Comunes (Barras de Progreso e Histograma) -->
                <div class="bg-white rounded-xl border border-slate-200 p-6 shadow-md flex flex-col justify-between">
                    <div class="mb-4">
                        <h3 class="text-base font-extrabold text-slate-800">Especialidades con Mayor Demanda</h3>
                        <p class="text-[11px] text-slate-400 font-medium">Tipos de consultas y servicios médicos más solicitados</p>
                    </div>

                    <!-- Progress bars representing analytics -->
                    <div class="space-y-4">
                        <div class="space-y-1.5">
                            <div class="flex justify-between text-xs font-bold text-slate-600">
                                <span>Consulta de Control General</span>
                                <span>45%</span>
                            </div>
                            <div class="w-full h-2.5 bg-slate-100 rounded-full overflow-hidden">
                                <div class="bg-blue-600 h-full rounded-full transition-all duration-1000" style="width: 45%"></div>
                            </div>
                        </div>

                        <div class="space-y-1.5">
                            <div class="flex justify-between text-xs font-bold text-slate-600">
                                <span>Seguimientos de Hipertensión</span>
                                <span>28%</span>
                            </div>
                            <div class="w-full h-2.5 bg-slate-100 rounded-full overflow-hidden">
                                <div class="bg-emerald-500 h-full rounded-full transition-all duration-1000" style="width: 28%"></div>
                            </div>
                        </div>

                        <div class="space-y-1.5">
                            <div class="flex justify-between text-xs font-bold text-slate-600">
                                <span>Evaluaciones Preventivas</span>
                                <span>17%</span>
                            </div>
                            <div class="w-full h-2.5 bg-slate-100 rounded-full overflow-hidden">
                                <div class="bg-amber-500 h-full rounded-full transition-all duration-1000" style="width: 17%"></div>
                            </div>
                        </div>

                        <div class="space-y-1.5">
                            <div class="flex justify-between text-xs font-bold text-slate-600">
                                <span>Procedimientos Quirúrgicos Menores</span>
                                <span>10%</span>
                            </div>
                            <div class="w-full h-2.5 bg-slate-100 rounded-full overflow-hidden">
                                <div class="bg-rose-500 h-full rounded-full transition-all duration-1000" style="width: 10%"></div>
                            </div>
                        </div>
                    </div>
                </div>
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

                <!-- Tabla de Citas Pendientes de Confirmar (Futuras y Hoy) -->
                <div v-if="citasPendientes.length > 0" class="xl:col-span-2 bg-white rounded-xl border border-slate-200 overflow-hidden flex flex-col shadow-md">
                    <div class="p-6 border-b border-slate-200 flex items-center justify-between">
                        <div>
                            <h3 class="text-lg font-bold text-slate-800">Citas por Confirmar (Pendientes)</h3>
                            <p class="text-xs text-slate-500 mt-1">Citas agendadas por pacientes esperando tu confirmación.</p>
                        </div>
                        <span class="px-2.5 py-1 text-xs font-bold text-amber-700 bg-amber-50 border border-amber-200 rounded-lg">{{ citasPendientes.length }} Pendientes</span>
                    </div>
                    <div class="overflow-x-auto flex-1">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="bg-slate-50 border-b border-slate-200">
                                    <th class="px-6 py-4 text-xs font-bold uppercase tracking-wider text-slate-400">Paciente</th>
                                    <th class="px-6 py-4 text-xs font-bold uppercase tracking-wider text-slate-400">Fecha / Hora</th>
                                    <th class="px-6 py-4 text-xs font-bold uppercase tracking-wider text-slate-400">Motivo / Síntomas</th>
                                    <th class="px-6 py-4 text-xs font-bold uppercase tracking-wider text-slate-400">Acciones</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                <tr v-for="cita in citasPendientes" :key="cita.id_cita" class="hover:bg-slate-50/50 transition">
                                    <td class="px-6 py-4 font-bold text-slate-800 text-sm">{{ cita.paciente }}</td>
                                    <td class="px-6 py-4 text-sm font-semibold text-slate-550">
                                        <div class="text-slate-800">{{ cita.fecha }}</div>
                                        <div class="text-[11px] text-slate-400 font-medium">{{ cita.hora }}</div>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-slate-600 truncate max-w-xs" :title="cita.motivo">{{ cita.motivo }}</td>
                                    <td class="px-6 py-4">
                                        <div class="flex items-center gap-2">
                                            <button 
                                                @click="updateCitaStatus(cita.id_cita, 'Confirmada')"
                                                class="px-3 py-1.5 bg-emerald-500 hover:bg-emerald-600 text-white text-[10px] font-black rounded-lg shadow-sm transition cursor-pointer"
                                            >
                                                Confirmar
                                            </button>
                                            <button 
                                                @click="updateCitaStatus(cita.id_cita, 'Cancelada')"
                                                class="px-3 py-1.5 bg-rose-500 hover:bg-rose-600 text-white text-[10px] font-black rounded-lg shadow-sm transition cursor-pointer"
                                            >
                                                Rechazar
                                            </button>
                                        </div>
                                    </td>
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

                        <!-- Asistente de Diagnóstico Clínico (IA) -->
                        <div @click="showAIModal = true" class="flex items-center justify-between p-4 rounded-xl border border-slate-150 hover:border-violet-250 hover:bg-violet-50/30 transition group cursor-pointer text-left">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-lg bg-violet-50 group-hover:bg-violet-100 flex items-center justify-center text-violet-650 transition duration-200">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M9.813 15.904L9 21l-.813-5.096L3 15l5.187-.813L9 9l.813 5.187L15 15l-5.187.813z" />
                                    </svg>
                                </div>
                                <div>
                                    <h4 class="text-sm font-bold text-slate-800 group-hover:text-violet-850">Asistente Clínico IA</h4>
                                    <p class="text-[11px] text-slate-400 mt-0.5 font-medium">Sugerencia de diagnósticos</p>
                                </div>
                            </div>
                            <span class="text-slate-300 group-hover:text-violet-600 transition font-bold">&rarr;</span>
                        </div>
                    </div>
                </div>
            </div>
            <!-- AI Diagnostic Assistant Modal -->
            <div v-if="showAIModal" class="fixed inset-0 z-50 overflow-y-auto flex items-center justify-center p-6 bg-slate-900/60 backdrop-blur-sm select-none">
                <div class="relative bg-white max-w-lg w-full rounded-2xl p-6 shadow-2xl border border-slate-150 space-y-5 text-left">
                    <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                        <div class="flex items-center gap-2 text-violet-600">
                            <div class="w-8 h-8 rounded-lg bg-violet-50 flex items-center justify-center">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-4 h-4">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9.813 15.904L9 21l-.813-5.096L3 15l5.187-.813L9 9l.813 5.187L15 15l-5.187.813z" />
                                </svg>
                            </div>
                            <h3 class="text-base font-extrabold text-slate-800">Asistente Clínico IA</h3>
                        </div>
                        <button @click="showAIModal = false" class="p-1 rounded-lg text-slate-400 hover:bg-slate-100 hover:text-slate-650 transition">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-4 h-4">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                    <div class="space-y-4">
                        <div class="space-y-1.5">
                            <label class="text-xs font-bold text-slate-500 uppercase tracking-wide">Ingrese los Síntomas del Paciente</label>
                            <textarea
                                v-model="symptomsInput"
                                rows="3"
                                placeholder="Ej: fiebre alta, tos seca, dolor de pecho al respirar..."
                                class="w-full rounded-xl border-slate-200 text-xs font-medium focus:border-violet-500 focus:ring-violet-500 shadow-sm"
                            ></textarea>
                            <span class="text-[10px] text-slate-400 block font-medium">Sugerencia: Escriba palabras clave como 'fiebre', 'pecho', 'cabeza', 'orina' o 'abdominal' para la simulación.</span>
                        </div>

                        <div class="flex justify-end gap-2">
                            <button
                                @click="resetAI"
                                class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-650 rounded-xl text-xs font-bold transition cursor-pointer"
                            >
                                Limpiar
                            </button>
                            <button
                                @click="analyzeSymptoms"
                                :disabled="!symptomsInput.trim() || isLoadingAI"
                                class="px-5 py-2 bg-violet-650 hover:bg-violet-750 text-white rounded-xl text-xs font-bold shadow-md shadow-violet-500/10 active:scale-[0.98] transition cursor-pointer disabled:opacity-50"
                            >
                                <span v-if="isLoadingAI">Analizando...</span>
                                <span v-else>Analizar Síntomas</span>
                            </button>
                        </div>

                        <!-- Results display -->
                        <div v-if="diagnosisResult" class="border border-slate-150 rounded-xl p-4 bg-slate-50 space-y-4 animate-fade-in text-xs font-semibold">
                            <div class="flex items-center justify-between border-b border-slate-200 pb-2">
                                <div>
                                    <span class="text-[9px] font-bold text-slate-400 uppercase tracking-wide">Diagnóstico Presuntivo</span>
                                    <h4 class="text-sm font-extrabold text-slate-800 mt-0.5">{{ diagnosisResult.title }}</h4>
                                </div>
                                <span class="px-2.5 py-0.5 rounded text-[10px] font-extrabold border" :class="diagnosisResult.severityColor">
                                    {{ diagnosisResult.severity }}
                                </span>
                            </div>

                            <div class="space-y-3">
                                <div>
                                    <span class="text-[9px] font-bold text-slate-400 uppercase tracking-wide block">Diagnóstico Diferencial sugerido</span>
                                    <span class="text-slate-650 block mt-0.5 font-medium">{{ diagnosisResult.differential }}</span>
                                </div>
                                <div>
                                    <span class="text-[9px] font-bold text-slate-400 uppercase tracking-wide block">Exámenes Clínicos Recomendados</span>
                                    <span class="text-slate-650 block mt-0.5 font-medium">{{ diagnosisResult.tests }}</span>
                                </div>
                                <div>
                                    <span class="text-[9px] font-bold text-slate-400 uppercase tracking-wide block">Pauta de Tratamiento Sugerida</span>
                                    <span class="text-slate-650 block mt-0.5 font-medium whitespace-pre-line">{{ diagnosisResult.treatment }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
