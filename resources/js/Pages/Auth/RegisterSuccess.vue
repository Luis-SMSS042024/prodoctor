<script setup>
import { Head, Link } from '@inertiajs/vue3';

defineProps({
    nombre: {
        type: String,
        required: true,
    },
    correo: {
        type: String,
        required: true,
    },
});
</script>

<template>
    <Head title="Cuenta Creada - ProDoctor" />

    <div class="relative min-h-screen bg-[#040A18] flex flex-col items-center justify-center p-6 overflow-hidden select-none font-sans text-slate-300">
        
        <!-- Glowing background blobs -->
        <div class="absolute -top-40 -left-40 w-[500px] h-[500px] rounded-full bg-blue-600/10 blur-[150px] pointer-events-none"></div>
        <div class="absolute -bottom-40 -right-40 w-[500px] h-[500px] rounded-full bg-[#00dfb2]/5 blur-[150px] pointer-events-none"></div>

        <!-- Wizard progress header (outside the card) -->
        <div class="flex items-center justify-center gap-2 mb-6 text-xs font-semibold select-none z-10">
            <div class="flex flex-col items-center">
                <div class="w-8 h-8 rounded-full bg-emerald-500/10 border border-emerald-500 text-[#00dfb2] flex items-center justify-center font-bold">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-4 h-4">
                        <path fill-rule="evenodd" d="M16.704 4.153a.75.75 0 0 1 .143 1.052l-8 10.5a.75.75 0 0 1-1.127.075l-4.5-4.5a.75.75 0 0 1 1.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 0 1 1.05-.143Z" clip-rule="evenodd" />
                    </svg>
                </div>
                <span class="mt-1.5 text-[#00dfb2] tracking-wider uppercase text-[10px] font-bold">Datos</span>
            </div>
            <div class="h-[1px] w-12 bg-emerald-500/40 self-center -mt-4"></div>
            <div class="flex flex-col items-center">
                <div class="w-8 h-8 rounded-full bg-emerald-500/10 border border-emerald-500 text-[#00dfb2] flex items-center justify-center font-bold">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-4 h-4">
                        <path fill-rule="evenodd" d="M16.704 4.153a.75.75 0 0 1 .143 1.052l-8 10.5a.75.75 0 0 1-1.127.075l-4.5-4.5a.75.75 0 0 1 1.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 0 1 1.05-.143Z" clip-rule="evenodd" />
                    </svg>
                </div>
                <span class="mt-1.5 text-[#00dfb2] tracking-wider uppercase text-[10px] font-bold">Verificar</span>
            </div>
            <div class="h-[1px] w-12 bg-emerald-500/40 self-center -mt-4"></div>
            <div class="flex flex-col items-center">
                <div class="w-8 h-8 rounded-full bg-[#00dfb2] text-slate-950 flex items-center justify-center font-bold shadow-[0_0_15px_rgba(0,223,178,0.4)]">3</div>
                <span class="mt-1.5 text-[#00dfb2] tracking-wider uppercase text-[10px] font-bold">Listo</span>
            </div>
        </div>

        <!-- Card Container -->
        <div class="relative w-full max-w-md bg-[#07111e] border border-[#13283f] rounded-2xl shadow-[0_20px_50px_rgba(0,0,0,0.6)] p-8 z-10">
            
            <div class="text-center mb-6 flex flex-col items-center">
                <!-- Branding Logo -->
                <div class="inline-flex w-14 h-14 rounded-2xl bg-gradient-to-tr from-emerald-500/20 to-teal-500/10 border border-[#00dfb2]/20 items-center justify-center shadow-lg shadow-emerald-500/5 mb-4">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-7 h-7 text-[#00dfb2]">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 12h3.5l1.5-3.5 2.5 7 2-4.5 1.5 1H20" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v2m0 12v2M8 12H6m12 0h-2" />
                    </svg>
                </div>

                <!-- Animated success checkmark -->
                <div class="mb-4 flex justify-center">
                    <div class="w-16 h-16 rounded-full bg-emerald-500/10 border border-[#00dfb2]/30 flex items-center justify-center shadow-[0_0_20px_rgba(0,223,178,0.15)] animate-pulse">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="3" stroke="currentColor" class="w-8 h-8 text-[#00dfb2]">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" />
                        </svg>
                    </div>
                </div>

                <h1 class="text-2xl font-bold text-white tracking-tight">¡Cuenta creada!</h1>
                <p class="text-xs text-slate-400 mt-2 leading-relaxed px-4">
                    Tu cuenta de ProDoctor ha sido activada exitosamente. Ya puedes acceder a tu portal de paciente.
                </p>
            </div>

            <!-- Details Block -->
            <div class="mb-6 bg-[#040a12] border border-[#162d4a] rounded-xl divide-y divide-[#162d4a] overflow-hidden">
                <!-- Name Row -->
                <div class="flex items-center justify-between text-xs py-3 px-4">
                    <span class="font-bold text-slate-500 uppercase tracking-widest">Nombre</span>
                    <span class="font-bold text-white text-right">{{ nombre }}</span>
                </div>
                <!-- Email Row -->
                <div class="flex items-center justify-between text-xs py-3 px-4">
                    <span class="font-bold text-slate-500 uppercase tracking-widest">Correo</span>
                    <span class="font-bold text-white text-right truncate max-w-[220px]" :title="correo">{{ correo }}</span>
                </div>
                <!-- Status Row -->
                <div class="flex items-center justify-between text-xs py-3 px-4">
                    <span class="font-bold text-slate-500 uppercase tracking-widest">Estado</span>
                    <span class="font-bold text-[#00dfb2] flex items-center gap-1.5">
                        <span class="w-1.5 h-1.5 rounded-full bg-[#00dfb2]"></span>
                        Activo
                    </span>
                </div>
            </div>

            <!-- Portal Button -->
            <Link
                :href="route('dashboard')"
                class="block w-full py-3.5 bg-gradient-to-r from-[#00dfb2] to-[#00a887] hover:from-[#00ffd5] hover:to-[#00c79f] text-slate-950 font-extrabold rounded-lg shadow-lg shadow-[#00dfb2]/10 active:scale-[0.99] transition duration-150 text-center text-sm cursor-pointer"
            >
                Ir a mi portal de paciente →
            </Link>
        </div>
    </div>
</template>
