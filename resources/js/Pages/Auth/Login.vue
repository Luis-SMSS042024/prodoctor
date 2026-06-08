<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ref, watch } from 'vue';

defineProps({
    canResetPassword: {
        type: Boolean,
    },
    status: {
        type: String,
    },
});

const form = useForm({
    correo: '',
    contrasena: '',
    remember: false,
    login_2fa: false,
});

const showPassword = ref(false);

const submit = () => {
    form.post(route('login'), {
        onFinish: () => form.reset('contrasena'),
    });
};
</script>

<template>
    <Head title="Iniciar Sesión - ProDoctor" />

    <div class="relative min-h-screen bg-[#030914] flex flex-col md:flex-row overflow-hidden font-sans text-slate-300 select-none">
        
        <!-- Glowing background blobs -->
        <div class="absolute -top-40 -left-40 w-[600px] h-[600px] rounded-full bg-blue-600/5 blur-[150px] pointer-events-none"></div>
        <div class="absolute -bottom-40 -right-40 w-[600px] h-[600px] rounded-full bg-[#00dfb2]/5 blur-[150px] pointer-events-none"></div>

        <!-- PANEL IZQUIERDO: Branding y Características de la Plataforma -->
        <div class="hidden md:flex md:w-1/2 flex-col justify-between p-12 lg:p-20 relative bg-gradient-to-br from-[#060c18] to-[#030914] border-r border-[#13283f] z-10">
            <!-- Glowing blob in left panel -->
            <div class="absolute -top-40 -left-40 w-[450px] h-[450px] rounded-full bg-blue-600/5 blur-[130px] pointer-events-none"></div>
            
            <!-- Contenedor alineado a la derecha de su mitad para acercarlo al divisor central -->
            <div class="ml-auto w-full max-w-sm flex flex-col h-full justify-between relative z-10">
                <!-- Header: Logo ProDoctor -->
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl flex items-center justify-center bg-gradient-to-tr from-emerald-500/20 to-teal-500/10 border border-[#00dfb2]/20 shadow-lg shadow-emerald-500/5">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-5 h-5 text-[#00dfb2]">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 12h3.5l1.5-3.5 2.5 7 2-4.5 1.5 1H20" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v2m0 12v2" />
                        </svg>
                    </div>
                    <div>
                        <h1 class="text-xl font-extrabold tracking-tight text-white leading-none">ProDoctor</h1>
                        <span class="text-[9px] text-slate-500 font-bold tracking-widest uppercase block mt-1">GABINETE MÉDICO</span>
                    </div>
                </div>

                <!-- Cuerpo Central: Mensajes y Ventajas -->
                <div class="my-auto py-8 space-y-6">
                    <h2 class="text-3xl font-extrabold tracking-tight text-white leading-tight">
                        Conecta con <br>
                        tu <span class="text-[#00dfb2]">plataforma</span> <br>
                        de forma segura.
                    </h2>
                    <p class="text-xs text-slate-400 leading-relaxed">
                        Plataforma de gestión médica ProDoctor. <br>
                        Acceso seguro con cifrado de extremo a extremo.
                    </p>
                    
                    <!-- Lista de Características -->
                    <ul class="space-y-4 text-xs">
                        <li class="flex items-start gap-3">
                            <div class="w-1.5 h-1.5 rounded-full bg-[#00dfb2] mt-1.5 shrink-0"></div>
                            <div>
                                <h4 class="font-extrabold text-white">Expediente Digital</h4>
                                <p class="text-slate-400 mt-0.5">Historial clínico y recetas unificadas en un solo lugar.</p>
                            </div>
                        </li>
                        <li class="flex items-start gap-3">
                            <div class="w-1.5 h-1.5 rounded-full bg-[#00dfb2] mt-1.5 shrink-0"></div>
                            <div>
                                <h4 class="font-extrabold text-white">Agenda en Línea</h4>
                                <p class="text-slate-400 mt-0.5">Reserva y gestiona tus consultas médicas de forma ágil.</p>
                            </div>
                        </li>
                        <li class="flex items-start gap-3">
                            <div class="w-1.5 h-1.5 rounded-full bg-[#00dfb2] mt-1.5 shrink-0"></div>
                            <div>
                                <h4 class="font-extrabold text-white">Acceso Seguro</h4>
                                <p class="text-slate-400 mt-0.5">Exclusivo para miembros y pacientes autorizados.</p>
                            </div>
                        </li>
                    </ul>
                </div>

                <!-- Footer Izquierdo -->
                <div class="text-[10px] text-slate-550 font-bold uppercase tracking-widest">
                    © 2026 ProDoctor. Todos los derechos reservados.
                </div>
            </div>
        </div>

        <!-- PANEL DERECHO: Tarjeta de Login -->
        <div class="w-full md:w-1/2 flex items-center justify-center md:justify-start p-6 lg:p-12 relative min-h-screen bg-[#030914]">
            
            <!-- Contenedor del Formulario alineado a la izquierda en md: para acercarlo al divisor central -->
            <div class="relative w-full max-w-sm bg-[#07111e]/90 border border-[#13283f] rounded-2xl shadow-[0_20px_50px_rgba(0,0,0,0.65)] p-8 z-10 space-y-6 md:ml-12 lg:ml-16">
                <!-- Títulos -->
                <div class="space-y-1">
                    <!-- En móvil, logo de ProDoctor -->
                    <div class="flex md:hidden items-center gap-2 mb-4">
                        <div class="w-8 h-8 rounded-lg flex items-center justify-center bg-gradient-to-tr from-emerald-500/20 to-teal-500/10 border border-[#00dfb2]/20">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-4 h-4 text-[#00dfb2]">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M4 12h3.5l1.5-3.5 2.5 7 2-4.5 1.5 1H20" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v2m0 12v2" />
                            </svg>
                        </div>
                        <span class="text-sm font-extrabold text-white">ProDoctor</span>
                    </div>
                    <h3 class="text-2xl font-black text-white tracking-tight">Iniciar sesión</h3>
                    <p class="text-xs text-slate-450">Accede con tus credenciales ProDoctor</p>
                </div>
                
                <!-- Estado de Validación -->
                <div v-if="status" class="p-3.5 bg-emerald-500/10 border border-emerald-500/20 rounded-xl text-xs font-semibold text-emerald-400 text-center leading-relaxed">
                    {{ status }}
                </div>
                
                <!-- Formulario -->
                <form @submit.prevent="submit" class="space-y-4">
                    <!-- Correo Electrónico -->
                    <div class="space-y-1.5">
                        <label for="correo" class="text-[10px] font-bold text-slate-400 uppercase tracking-widest block">Correo Electrónico</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-[#00dfb2]">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 0 1-2.25 2.25h-15a2.25 2.25 0 0 1-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25m19.5 0v.243a2.25 2.25 0 0 1-1.07 1.916l-7.5 4.615a2.25 2.25 0 0 1-2.36 0l-7.5-4.615a2.25 2.25 0 0 1-1.07-1.916V6.75" />
                                </svg>
                            </div>
                            <input
                                id="correo"
                                type="email"
                                class="block w-full pl-10 pr-4 py-2.5 bg-[#040a12] border border-[#162d4a] rounded-xl text-white text-xs placeholder-slate-600 focus:outline-none focus:border-[#00dfb2] focus:ring-1 focus:ring-[#00dfb2]/30 transition"
                                v-model="form.correo"
                                required
                                autofocus
                                autocomplete="username"
                                placeholder="correo@organizacion.com"
                            />
                        </div>
                        <span v-if="form.errors.correo" class="text-[10px] text-rose-500 font-semibold block mt-1">{{ form.errors.correo }}</span>
                    </div>
                    
                    <!-- Contraseña -->
                    <div class="space-y-1.5">
                        <label for="contrasena" class="text-[10px] font-bold text-slate-400 uppercase tracking-widest block">Contraseña</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-[#00dfb2]">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 1 0-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 0 0 2.25-2.25v-6.75a2.25 2.25 0 0 0-2.25-2.25H6.75a2.25 2.25 0 0 0-2.25 2.25v6.75a2.25 2.25 0 0 0 2.25 2.25z" />
                                </svg>
                            </div>
                            <input
                                id="contrasena"
                                :type="showPassword ? 'text' : 'password'"
                                class="block w-full pl-10 pr-10 py-2.5 bg-[#040a12] border border-[#162d4a] rounded-xl text-white text-xs placeholder-slate-600 focus:outline-none focus:border-[#00dfb2] focus:ring-1 focus:ring-[#00dfb2]/30 transition"
                                v-model="form.contrasena"
                                required
                                autocomplete="current-password"
                                placeholder="••••••••"
                            />
                            <button
                                type="button"
                                @click="showPassword = !showPassword"
                                class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-slate-500 hover:text-[#00dfb2] transition cursor-pointer"
                            >
                                <svg v-if="!showPassword" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4.5 h-4.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" />
                                    <circle cx="12" cy="12" r="3" />
                                </svg>
                                <svg v-else xmlns="http://www.w3.org/2050/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4.5 h-4.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 0 0 1.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.45 10.45 0 0 1 12 4.5c4.756 0 8.773 3.162 10.065 7.498a10.523 10.523 0 0 1-4.293 5.774M6.228 6.228L3 3m3.228 3.228l3.65 3.65m7.895 7.895L21 21m-3.228-3.228l-3.65-3.65m0 0a3 3 0 1 0-4.243-4.243m4.242 4.242L9.88 9.88" />
                                </svg>
                            </button>
                        </div>
                        <span v-if="form.errors.contrasena" class="text-[10px] text-rose-500 font-semibold block mt-1">{{ form.errors.contrasena }}</span>
                    </div>
                    
                    <!-- Recordarme checkbox -->
                    <div class="flex items-center text-xs text-slate-450 select-none">
                        <label class="flex items-center cursor-pointer">
                            <input
                                type="checkbox"
                                name="remember"
                                v-model="form.remember"
                                class="sr-only peer"
                            />
                            <div class="w-4 h-4 mr-2 rounded border border-[#162d4a] bg-[#040a12] flex items-center justify-center text-transparent peer-checked:text-[#00dfb2] peer-checked:border-[#00dfb2] transition-all duration-200">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-3.5 h-3.5">
                                    <path fill-rule="evenodd" d="M16.704 4.153a.75.75 0 0 1 .143 1.052l-8 10.5a.75.75 0 0 1-1.127.075l-4.5-4.5a.75.75 0 0 1 1.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 0 1 1.05-.143Z" clip-rule="evenodd" />
                                </svg>
                            </div>
                            Recordarme
                        </label>
                    </div>
                    
                    <!-- Botonera -->
                    <div class="space-y-3 pt-2">
                        <!-- Iniciar Sesión Submit -->
                        <button
                            type="submit"
                            class="w-full py-3 bg-[#00dfb2] hover:bg-[#00ffd5] text-slate-950 font-black rounded-xl active:scale-[0.99] transition duration-150 text-center flex items-center justify-center gap-1 disabled:opacity-50 disabled:cursor-not-allowed text-xs cursor-pointer"
                            :disabled="form.processing"
                        >
                            <span v-if="form.processing">Procesando...</span>
                            <span v-else class="flex items-center gap-1 justify-center w-full">
                                Iniciar sesión <span class="text-sm font-bold">→</span>
                            </span>
                        </button>
                        
                        <!-- Registrarse (Outlined) -->
                        <Link
                            :href="route('register')"
                            class="w-full py-3 border border-[#162d4a] hover:bg-[#040a12]/50 text-slate-200 font-bold rounded-xl transition duration-150 text-center block text-xs cursor-pointer"
                        >
                            Registrarse
                        </Link>
                    </div>
                </form>
                
                <!-- Recuperar Contraseña -->
                <div class="text-center">
                    <Link
                        v-if="canResetPassword"
                        :href="route('password.request')"
                        class="text-xs text-slate-450 hover:text-slate-200 transition"
                    >
                        ¿Olvidaste tu contraseña? <span class="text-[#00dfb2] hover:underline font-bold">recupérala aquí</span>
                    </Link>
                </div>
                
                <!-- Divisor -->
                <div class="relative flex py-1 items-center">
                    <div class="flex-grow border-t border-[#12253b]"></div>
                    <span class="flex-shrink mx-3 text-[9px] text-slate-650 font-bold uppercase tracking-widest">acceso seguro</span>
                    <div class="flex-grow border-t border-[#12253b]"></div>
                </div>
                
                <!-- Footer Informativo -->
                <div class="flex items-center justify-center gap-1.5 text-[10px] text-slate-550 font-semibold">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                    Autenticación segura - ProDoctor
                </div>
            </div>
        </div>
    </div>
</template>
