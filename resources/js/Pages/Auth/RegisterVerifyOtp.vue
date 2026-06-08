<script setup>
import { Head, Link, useForm, router } from '@inertiajs/vue3';
import { ref, computed, onMounted, onUnmounted } from 'vue';
import axios from 'axios';

const props = defineProps({
    status: {
        type: String,
    },
    email: {
        type: String,
        required: true,
    },
});

const form = useForm({
    code: '',
});

const digits = ref(['', '', '', '', '', '']);
const inputRefs = ref([]);

const isResending = ref(false);
const resendStatus = ref('');
const devOtp = ref('');

const maskedEmail = computed(() => {
    const parts = props.email.split('@');
    if (parts.length !== 2) return props.email;
    const name = parts[0];
    const domain = parts[1];
    if (name.length <= 2) {
        return `${name}***@${domain}`;
    }
    return `${name.substring(0, 1)}***${name.substring(name.length - 1)}@${domain}`;
});

const emailDomain = computed(() => {
    const parts = props.email.split('@');
    return parts.length === 2 ? parts[1] : 'correo';
});

// Resend OTP timer logic
const timer = ref(60);
let intervalId = null;

const startTimer = () => {
    clearInterval(intervalId);
    timer.value = 60;
    intervalId = setInterval(() => {
        if (timer.value > 0) {
            timer.value--;
        } else {
            clearInterval(intervalId);
        }
    }, 1000);
};

onMounted(() => {
    startTimer();
});

onUnmounted(() => {
    clearInterval(intervalId);
});

// Focus first input automatically
onMounted(() => {
    setTimeout(() => {
        if (inputRefs.value[0]) {
            inputRefs.value[0].focus();
        }
    }, 100);
});

const handleInput = (index, event) => {
    const val = event.target.value;
    
    // Only accept numeric digit
    if (!/^[0-9]$/.test(val)) {
        digits.value[index] = '';
        form.code = digits.value.join('');
        return;
    }
    
    digits.value[index] = val;
    form.code = digits.value.join('');
    
    // Auto-focus next input
    if (index < 5) {
        inputRefs.value[index + 1].focus();
    }
};

const handleKeydown = (index, event) => {
    if (event.key === 'Backspace') {
        if (digits.value[index] === '' && index > 0) {
            digits.value[index - 1] = '';
            form.code = digits.value.join('');
            inputRefs.value[index - 1].focus();
        } else {
            digits.value[index] = '';
            form.code = digits.value.join('');
        }
    }
};

const handlePaste = (event) => {
    event.preventDefault();
    const pasteData = event.clipboardData.getData('text').trim();
    if (/^\d{6}$/.test(pasteData)) {
        for (let i = 0; i < 6; i++) {
            digits.value[i] = pasteData[i];
        }
        form.code = digits.value.join('');
        if (inputRefs.value[5]) {
            inputRefs.value[5].focus();
        }
    }
};

const resendCode = () => {
    if (timer.value > 0 || isResending.value) return;
    
    isResending.value = true;
    resendStatus.value = '';
    
    axios.post(route('register.resend-otp'))
        .then(response => {
            if (response.data.success) {
                resendStatus.value = response.data.message;
                if (response.data.otp) {
                    devOtp.value = response.data.otp;
                }
                startTimer();
            } else {
                alert(response.data.message || 'Error al reenviar el código.');
            }
        })
        .catch(error => {
            alert(error.response?.data?.message || 'Error al reenviar el código.');
        })
        .finally(() => {
            isResending.value = false;
        });
};

const submit = () => {
    if (form.code.length !== 6) return;
    form.post(route('register.check-otp'));
};
</script>

<template>
    <Head title="Verificar Correo - ProDoctor" />

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
                <div class="w-8 h-8 rounded-full bg-[#00dfb2] text-slate-950 flex items-center justify-center font-bold shadow-[0_0_15px_rgba(0,223,178,0.4)]">2</div>
                <span class="mt-1.5 text-[#00dfb2] tracking-wider uppercase text-[10px] font-bold">Verificar</span>
            </div>
            <div class="h-[1px] w-12 bg-slate-800 self-center -mt-4"></div>
            <div class="flex flex-col items-center">
                <div class="w-8 h-8 rounded-full border border-slate-700 text-slate-500 flex items-center justify-center font-bold bg-[#040A18]">3</div>
                <span class="mt-1.5 text-slate-500 tracking-wider uppercase text-[10px] font-bold">Listo</span>
            </div>
        </div>

        <!-- Validation Error Banner (above card) -->
        <div v-if="form.errors.code || form.errors.correo" class="w-full max-w-md mb-4 p-3.5 bg-rose-500/10 border border-rose-500/20 rounded-xl text-xs font-semibold text-rose-400 flex items-center gap-2.5 z-10 shadow-lg">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-5 h-5 text-rose-500 flex-shrink-0">
                <path fill-rule="evenodd" d="M18 10a8 8 0 1 1-16 0 8 8 0 0 1 16 0Zm-8-5a.75.75 0 0 1 .75.75v4.5a.75.75 0 0 1-1.5 0v-4.5A.75.75 0 0 1 10 5Zm0 10a1 1 0 1 0 0-2 1 1 0 0 0 0 2Z" clip-rule="evenodd" />
            </svg>
            <span>{{ form.errors.code || form.errors.correo }}</span>
        </div>

        <!-- Dev OTP Helper Status (above card) -->
        <div v-if="status || resendStatus || devOtp" class="w-full max-w-md mb-4 p-3.5 bg-amber-500/10 border border-amber-500/20 rounded-xl text-xs font-semibold text-amber-400 flex flex-col items-center justify-center gap-1.5 z-10">
            <span v-if="resendStatus || status">{{ resendStatus || status }}</span>
            <span v-if="devOtp" class="font-extrabold text-[#00dfb2]">Código enviado. (Para desarrollo: {{ devOtp }})</span>
        </div>

        <!-- Card Container -->
        <div class="relative w-full max-w-md bg-[#07111e] border border-[#13283f] rounded-2xl shadow-[0_20px_50px_rgba(0,0,0,0.6)] p-8 z-10">
            
            <div class="text-center mb-6 flex flex-col items-center">
                <!-- Mailbox Icon -->
                <div class="inline-flex p-3 bg-emerald-500/10 border border-[#00dfb2]/10 rounded-xl text-[#00dfb2] mb-4">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-6 h-6">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 0 1-2.25 2.25h-15a2.25 2.25 0 0 1-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0l-7.5-4.615a2.25 2.25 0 01-1.07-1.916V6.75" />
                    </svg>
                </div>

                <h1 class="text-2xl font-bold text-white tracking-tight">Verifica tu correo</h1>
                <p class="text-xs text-slate-400 mt-2.5 leading-relaxed px-2">
                    Ingresa el código de 6 dígitos que enviamos a <span class="text-[#00dfb2] font-semibold tracking-wide">{{ maskedEmail }}</span> para activar tu cuenta.
                </p>
            </div>

            <!-- Notice Banner -->
            <div class="mb-6 p-4 rounded-xl bg-emerald-950/20 border border-emerald-500/25 flex gap-3 select-none">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5 text-[#00dfb2] flex-shrink-0 mt-0.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M11.25 11.25l.041-.02a.75.75 0 111.063.852l-.708 2.836a.75.75 0 001.063.852l.041-.021M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9-3.75h.008v.008H12V8.25z" />
                </svg>
                <p class="text-[11px] text-slate-400 leading-relaxed">
                    El código fue enviado a tu correo <span class="text-slate-300 font-medium">{{ emailDomain }}</span>. Revisa también tu carpeta de spam. El código expira en <span class="text-[#00dfb2] font-bold">10 minutos</span>.
                </p>
            </div>

            <!-- Verification form -->
            <form @submit.prevent="submit" class="space-y-6">
                <!-- 6 Digit Input Boxes -->
                <div class="flex justify-between gap-2.5" @paste="handlePaste">
                    <input
                        v-for="(digit, idx) in digits"
                        :key="idx"
                        type="text"
                        maxlength="1"
                        pattern="[0-9]*"
                        inputmode="numeric"
                        class="w-12 h-14 bg-[#040a12] border border-[#162d4a] rounded-lg text-white font-extrabold text-xl text-center focus:outline-none focus:border-[#00dfb2] focus:ring-1 focus:ring-[#00dfb2]/30 transition uppercase"
                        v-model="digits[idx]"
                        @input="handleInput(idx, $event)"
                        @keydown="handleKeydown(idx, $event)"
                        :ref="(el) => { if (el) inputRefs[idx] = el; }"
                        required
                    />
                </div>

                <!-- Timer & Resend code -->
                <div class="text-center text-xs select-none">
                    <span class="text-slate-500">¿No recibiste el código?</span>
                    <button
                        type="button"
                        @click="resendCode"
                        class="ml-1.5 font-bold transition"
                        :class="[timer > 0 || isResending ? 'text-slate-600 cursor-not-allowed' : 'text-[#00dfb2] hover:text-[#00ffd5] hover:underline']"
                        :disabled="timer > 0 || form.processing || isResending"
                    >
                        <span v-if="isResending">Enviando...</span>
                        <span v-else>Reenviar <span v-if="timer > 0">({{ timer }}s)</span></span>
                    </button>
                </div>

                <!-- Submit Button -->
                <button
                    type="submit"
                    :disabled="form.processing || form.code.length !== 6"
                    class="w-full py-3.5 bg-gradient-to-r from-[#00dfb2] to-[#00a887] hover:from-[#00ffd5] hover:to-[#00c79f] text-slate-950 font-extrabold rounded-lg shadow-lg shadow-[#00dfb2]/10 active:scale-[0.99] transition duration-150 text-center flex items-center justify-center gap-1.5 disabled:opacity-50 disabled:cursor-not-allowed text-sm cursor-pointer"
                >
                    <span>{{ form.processing ? 'Verificando...' : 'Verificar y crear cuenta' }}</span>
                    <span v-if="!form.processing">→</span>
                </button>

                <!-- Back / Change Details -->
                <div class="text-center border-t border-[#12253b] pt-5">
                    <Link
                        :href="route('register')"
                        class="text-xs text-[#00dfb2] hover:text-[#00ffd5] font-bold transition flex items-center justify-center gap-1"
                    >
                        <span>← Volver y cambiar datos</span>
                    </Link>
                </div>
            </form>
        </div>
    </div>
</template>
