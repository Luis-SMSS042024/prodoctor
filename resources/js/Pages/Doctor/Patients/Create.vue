<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, useForm, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

const props = defineProps({
    doctores: {
        type: Array,
        required: true,
    },
});

const page = usePage();
const currentDoctor = computed(() => {
    // Attempt to match current user to a doctor
    const userName = page.props.auth.user.nombre_usuario;
    return userName || 'Dr. Roberto Méndez';
});

const form = useForm({
    nombres: '',
    apellidos: '',
    dui: '',
    fecha_nacimiento: '',
    sexo: '',
    telefono: '',
    direccion: '',
    tipo_sangre: '',
    alergias: '',
    observaciones: '',
    contacto_emergencia_nombre: '',
    contacto_emergencia_parentesco: '',
    contacto_emergencia_telefono: '',
});

const submit = () => {
    form.post(route('doctor.patients.store'));
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
                    <span class="text-slate-800">Nuevo Registro</span>
                </div>
                
                <!-- Action Buttons -->
                <div class="flex items-center gap-3">
                    <Link
                        :href="route('doctor.patients.index')"
                        class="px-4 py-2.5 bg-white hover:bg-slate-50 border border-slate-200 text-slate-650 hover:text-slate-800 rounded-xl text-xs font-bold transition cursor-pointer"
                    >
                        Cancelar
                    </Link>
                    <button
                        @click="submit"
                        :disabled="form.processing"
                        class="px-5 py-2.5 bg-gradient-to-r from-blue-600 to-indigo-650 hover:from-blue-700 hover:to-indigo-755 text-white rounded-xl text-xs font-bold shadow-md shadow-blue-500/20 active:scale-[0.98] transition cursor-pointer disabled:opacity-50"
                    >
                        Guardar Paciente
                    </button>
                </div>
            </div>
        </template>

        <Head title="Registrar Paciente - ProDoctor" />

        <div class="select-none">
            <form @submit.prevent="submit" class="grid grid-cols-1 lg:grid-cols-3 gap-8 items-start">
                
                <!-- Columna Izquierda (Datos Personales - 2/3) -->
                <div class="lg:col-span-2 space-y-6">
                    <!-- Tarjeta Principal -->
                    <div class="bg-white rounded-xl border border-slate-200 p-8 shadow-sm space-y-8">
                        <div>
                            <h3 class="text-base font-bold text-slate-800 flex items-center gap-2">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5 text-blue-550">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" />
                                </svg>
                                Datos Personales
                            </h3>
                            <p class="text-xs text-slate-450 mt-1">Completa la información básica y de contacto del paciente.</p>
                        </div>

                        <!-- Sección: Identificación -->
                        <div class="space-y-4">
                            <h4 class="text-[10px] font-bold text-blue-600 tracking-wider uppercase border-b border-slate-100 pb-1.5">IDENTIFICACIÓN</h4>
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                <!-- Primer Nombre -->
                                <div class="space-y-1.5">
                                    <label class="text-[11px] font-bold text-slate-500 uppercase tracking-wide block">Nombres <span class="text-rose-500">*</span></label>
                                    <input
                                        type="text"
                                        v-model="form.nombres"
                                        required
                                        placeholder="Ej. Ana María"
                                        class="block w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-slate-800 text-xs placeholder-slate-400 focus:outline-none focus:bg-white focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 transition"
                                    />
                                    <span v-if="form.errors.nombres" class="text-[10px] font-semibold text-rose-500">{{ form.errors.nombres }}</span>
                                </div>

                                <!-- Apellidos -->
                                <div class="space-y-1.5">
                                    <label class="text-[11px] font-bold text-slate-500 uppercase tracking-wide block">Apellidos <span class="text-rose-500">*</span></label>
                                    <input
                                        type="text"
                                        v-model="form.apellidos"
                                        required
                                        placeholder="Ej. Martínez López"
                                        class="block w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-slate-800 text-xs placeholder-slate-400 focus:outline-none focus:bg-white focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 transition"
                                    />
                                    <span v-if="form.errors.apellidos" class="text-[10px] font-semibold text-rose-500">{{ form.errors.apellidos }}</span>
                                </div>

                                <!-- DUI -->
                                <div class="space-y-1.5">
                                    <label class="text-[11px] font-bold text-slate-500 uppercase tracking-wide block">DUI / Documento</label>
                                    <input
                                        type="text"
                                        v-model="form.dui"
                                        placeholder="00000000-0"
                                        class="block w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-slate-800 text-xs placeholder-slate-400 focus:outline-none focus:bg-white focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 transition"
                                    />
                                    <span v-if="form.errors.dui" class="text-[10px] font-semibold text-rose-500">{{ form.errors.dui }}</span>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                <!-- Fecha de Nacimiento -->
                                <div class="space-y-1.5">
                                    <label class="text-[11px] font-bold text-slate-500 uppercase tracking-wide block">Fecha de Nacimiento <span class="text-rose-500">*</span></label>
                                    <input
                                        type="date"
                                        v-model="form.fecha_nacimiento"
                                        required
                                        class="block w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-slate-800 text-xs focus:outline-none focus:bg-white focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 transition"
                                    />
                                    <span v-if="form.errors.fecha_nacimiento" class="text-[10px] font-semibold text-rose-500">{{ form.errors.fecha_nacimiento }}</span>
                                </div>

                                <!-- Género -->
                                <div class="space-y-1.5">
                                    <label class="text-[11px] font-bold text-slate-500 uppercase tracking-wide block">Género <span class="text-rose-500">*</span></label>
                                    <select
                                        v-model="form.sexo"
                                        required
                                        class="block w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-slate-800 text-xs focus:outline-none focus:bg-white focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 transition"
                                    >
                                        <option value="" disabled>Seleccionar...</option>
                                        <option value="Masculino">Masculino</option>
                                        <option value="Femenino">Femenino</option>
                                        <option value="Otro">Otro</option>
                                    </select>
                                    <span v-if="form.errors.sexo" class="text-[10px] font-semibold text-rose-500">{{ form.errors.sexo }}</span>
                                </div>

                                <!-- Tipo de Sangre -->
                                <div class="space-y-1.5">
                                    <label class="text-[11px] font-bold text-slate-500 uppercase tracking-wide block">Tipo de Sangre</label>
                                    <select
                                        v-model="form.tipo_sangre"
                                        class="block w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-slate-800 text-xs focus:outline-none focus:bg-white focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 transition"
                                    >
                                        <option value="">Desconocido</option>
                                        <option value="O+">O Positivo (O+)</option>
                                        <option value="O-">O Negativo (O-)</option>
                                        <option value="A+">A Positivo (A+)</option>
                                        <option value="A-">A Negativo (A-)</option>
                                        <option value="B+">B Positivo (B+)</option>
                                        <option value="B-">B Negativo (B-)</option>
                                        <option value="AB+">AB Positivo (AB+)</option>
                                        <option value="AB-">AB Negativo (AB-)</option>
                                    </select>
                                    <span v-if="form.errors.tipo_sangre" class="text-[10px] font-semibold text-rose-500">{{ form.errors.tipo_sangre }}</span>
                                </div>
                            </div>
                        </div>

                        <!-- Sección: Contacto -->
                        <div class="space-y-4">
                            <h4 class="text-[10px] font-bold text-blue-600 tracking-wider uppercase border-b border-slate-100 pb-1.5">CONTACTO</h4>
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                <!-- Teléfono -->
                                <div class="space-y-1.5 md:col-span-1">
                                    <label class="text-[11px] font-bold text-slate-500 uppercase tracking-wide block">Teléfono <span class="text-rose-500">*</span></label>
                                    <input
                                        type="text"
                                        v-model="form.telefono"
                                        required
                                        placeholder="Ej. 7000-0000"
                                        class="block w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-slate-800 text-xs placeholder-slate-400 focus:outline-none focus:bg-white focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 transition"
                                    />
                                    <span v-if="form.errors.telefono" class="text-[10px] font-semibold text-rose-500">{{ form.errors.telefono }}</span>
                                </div>

                                <!-- Dirección -->
                                <div class="space-y-1.5 md:col-span-2">
                                    <label class="text-[11px] font-bold text-slate-500 uppercase tracking-wide block">Dirección Residencia <span class="text-rose-500">*</span></label>
                                    <input
                                        type="text"
                                        v-model="form.direccion"
                                        required
                                        placeholder="Ej. Colonia, municipio, departamento"
                                        class="block w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-slate-800 text-xs placeholder-slate-400 focus:outline-none focus:bg-white focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 transition"
                                    />
                                    <span v-if="form.errors.direccion" class="text-[10px] font-semibold text-rose-500">{{ form.errors.direccion }}</span>
                                </div>
                            </div>
                        </div>

                        <!-- Sección: Información Clínica -->
                        <div class="space-y-4">
                            <h4 class="text-[10px] font-bold text-blue-600 tracking-wider uppercase border-b border-slate-100 pb-1.5">INFORMACIÓN CLÍNICA</h4>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <!-- Médico Responsable -->
                                <div class="space-y-1.5">
                                    <label class="text-[11px] font-bold text-slate-500 uppercase tracking-wide block">Médico Responsable</label>
                                    <input
                                        type="text"
                                        disabled
                                        :value="currentDoctor"
                                        class="block w-full px-3.5 py-2.5 bg-slate-100 border border-slate-200 rounded-xl text-slate-400 text-xs select-none"
                                    />
                                </div>

                                <!-- Estado del Registro -->
                                <div class="space-y-1.5">
                                    <label class="text-[11px] font-bold text-slate-500 uppercase tracking-wide block">Estado Expediente</label>
                                    <select
                                        disabled
                                        class="block w-full px-3.5 py-2.5 bg-slate-100 border border-slate-200 rounded-xl text-slate-400 text-xs select-none"
                                    >
                                        <option selected>Activo</option>
                                    </select>
                                </div>
                            </div>

                            <!-- Alergias Conocidas -->
                            <div class="space-y-1.5">
                                <label class="text-[11px] font-bold text-slate-500 uppercase tracking-wide block">Alergias Conocidas</label>
                                <textarea
                                    v-model="form.alergias"
                                    rows="2"
                                    placeholder="Ej. Penicilina, látex, mariscos, etc. Si no posee, indicar 'Ninguna conocida'"
                                    class="block w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-slate-800 text-xs placeholder-slate-400 focus:outline-none focus:bg-white focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 transition resize-none"
                                ></textarea>
                                <span v-if="form.errors.alergias" class="text-[10px] font-semibold text-rose-500">{{ form.errors.alergias }}</span>
                            </div>

                            <!-- Observaciones Iniciales -->
                            <div class="space-y-1.5">
                                <label class="text-[11px] font-bold text-slate-500 uppercase tracking-wide block">Observaciones o Notas Iniciales</label>
                                <textarea
                                    v-model="form.observaciones"
                                    rows="3"
                                    placeholder="Detalles o notas clínicas iniciales del paciente para el expediente..."
                                    class="block w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-slate-800 text-xs placeholder-slate-400 focus:outline-none focus:bg-white focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 transition resize-none"
                                ></textarea>
                                <span v-if="form.errors.observaciones" class="text-[10px] font-semibold text-rose-500">{{ form.errors.observaciones }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Columna Derecha (Foto y Contacto de Emergencia - 1/3) -->
                <div class="space-y-6">
                    <!-- Foto y Metadatos del Expediente -->
                    <div class="bg-white rounded-xl border border-slate-200 p-6 flex flex-col items-center shadow-sm">
                        <!-- Photo Box -->
                        <div class="w-28 h-28 rounded-full border-4 border-slate-100 bg-slate-50 flex items-center justify-center text-slate-400 relative overflow-hidden shadow-inner group cursor-pointer mb-4">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-12 h-12 group-hover:scale-110 transition duration-200">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6.827 6.175A2.31 2.31 0 015.186 7.23c-.38.054-.757.112-1.134.175C2.999 7.58 2.25 8.507 2.25 9.574V18a2.25 2.25 0 002.25 2.25h15A2.25 2.25 0 0021.75 18V9.574c0-1.067-.75-1.994-1.802-2.169a47.865 47.865 0 00-1.134-.175 2.31 2.31 0 01-1.64-1.055l-.822-1.316a2.192 2.192 0 00-1.736-1.039 48.774 48.774 0 00-5.232 0 2.192 2.192 0 00-1.736 1.039l-.821 1.316z" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 12.75a4.5 4.5 0 11-9 0 4.5 4.5 0 019 0zM18.75 10.5h.008v.008h-.008V10.5z" />
                            </svg>
                        </div>
                        <span class="text-xs font-semibold text-slate-400 uppercase tracking-wide">FOTO DEL PACIENTE</span>
                        <span class="text-[10px] text-slate-400 mt-1">JPG o PNG, máx. 2MB</span>
                        
                        <!-- Expediente Tag -->
                        <div class="mt-6 w-full pt-6 border-t border-slate-100 flex flex-col items-center space-y-2">
                            <span class="text-[11px] font-bold text-slate-400 tracking-wider">ID EXPEDIENTE</span>
                            <span class="px-3 py-1 bg-blue-50 text-blue-700 font-extrabold text-xs rounded border border-blue-200 uppercase tracking-widest">#PAC-NUEVO</span>
                            <div class="flex flex-col items-center pt-3 text-[10px] text-slate-400 space-y-1 font-semibold">
                                <span>Registrado por: {{ currentDoctor }}</span>
                                <span>Expediente: Nuevo Registro</span>
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

                        <!-- Nombre del Contacto -->
                        <div class="space-y-1.5">
                            <label class="text-[11px] font-bold text-slate-500 uppercase tracking-wide block">Nombre Completo</label>
                            <input
                                type="text"
                                v-model="form.contacto_emergencia_nombre"
                                placeholder="Ej. Juan Martínez"
                                class="block w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-slate-800 text-xs placeholder-slate-400 focus:outline-none focus:bg-white focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 transition"
                            />
                            <span v-if="form.errors.contacto_emergencia_nombre" class="text-[10px] font-semibold text-rose-500">{{ form.errors.contacto_emergencia_nombre }}</span>
                        </div>

                        <!-- Parentesco -->
                        <div class="space-y-1.5">
                            <label class="text-[11px] font-bold text-slate-500 uppercase tracking-wide block">Parentesco</label>
                            <select
                                v-model="form.contacto_emergencia_parentesco"
                                class="block w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-slate-800 text-xs focus:outline-none focus:bg-white focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 transition"
                            >
                                <option value="">Seleccionar...</option>
                                <option value="Cónyuge">Cónyuge</option>
                                <option value="Madre">Madre</option>
                                <option value="Padre">Padre</option>
                                <option value="Hermano/a">Hermano/a</option>
                                <option value="Hijo/a">Hijo/a</option>
                                <option value="Tutor Legal">Tutor Legal</option>
                                <option value="Otro">Otro Familiar/Amigo</option>
                            </select>
                            <span v-if="form.errors.contacto_emergencia_parentesco" class="text-[10px] font-semibold text-rose-500">{{ form.errors.contacto_emergencia_parentesco }}</span>
                        </div>

                        <!-- Teléfono del Contacto -->
                        <div class="space-y-1.5">
                            <label class="text-[11px] font-bold text-slate-500 uppercase tracking-wide block">Teléfono de Contacto</label>
                            <input
                                type="text"
                                v-model="form.contacto_emergencia_telefono"
                                placeholder="Ej. 7000-0000"
                                class="block w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-slate-800 text-xs placeholder-slate-400 focus:outline-none focus:bg-white focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 transition"
                            />
                            <span v-if="form.errors.contacto_emergencia_telefono" class="text-[10px] font-semibold text-rose-500">{{ form.errors.contacto_emergencia_telefono }}</span>
                        </div>
                    </div>
                </div>

            </form>
        </div>
    </AuthenticatedLayout>
</template>
