@extends('layouts.guest')

@section('title', 'Verify OTP - ' . config('app.name'))

@section('content')
    <div class="min-h-screen flex items-center justify-center px-4 py-12">
        <div class="w-full max-w-md">
            {{-- Header --}}
            <div class="text-center mb-8">
                <a href="{{ route('dashboard') }}" class="text-2xl font-bold text-indigo-600 dark:text-indigo-400 tracking-tight">
                    {{ config('app.name') }}
                </a>
            </div>

            {{-- Error Messages --}}
            @error('otp_code')
                <div class="mb-4 p-4 bg-red-50 dark:bg-red-900/30 border border-red-200 dark:border-red-800 rounded-xl text-sm text-red-700 dark:text-red-300">
                    {{ $message }}
                </div>
            @enderror

            @session('error')
                <div class="mb-4 p-4 bg-red-50 dark:bg-red-900/30 border border-red-200 dark:border-red-800 rounded-xl text-sm text-red-700 dark:text-red-300">
                    {{ $value }}
                </div>
            @endsession

            {{-- Card --}}
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 p-6 sm:p-8">
                <div class="text-center mb-6">
                    <h1 class="text-xl font-bold text-gray-900 dark:text-white">Check Your Email</h1>
                    <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                        We sent a verification code to
                        <span class="font-medium text-gray-900 dark:text-gray-200">
                            {{ substr($email, 0, 1) . '***@' . substr(strstr($email, '@'), 1) }}
                        </span>
                    </p>
                </div>

                <form
                    method="POST"
                    action="{{ route('otp.verify') }}"
                    data-otp-form
                    class="space-y-6"
                >
                    @csrf

                    <div class="flex items-center justify-center gap-2 sm:gap-3">
                        @for ($index = 0; $index < 6; $index++)
                            <input
                                data-otp-input
                                type="text"
                                inputmode="numeric"
                                maxlength="1"
                                autocomplete="one-time-code"
                                class="w-11 h-12 sm:w-12 sm:h-14 text-center text-lg font-semibold text-gray-900 dark:text-gray-100 bg-white dark:bg-gray-900 border-2 rounded-xl transition-all duration-150 outline-none border-gray-300 dark:border-gray-600 hover:border-gray-400 dark:hover:border-gray-500 focus:border-indigo-500 dark:focus:border-indigo-400 focus:ring-2 focus:ring-indigo-200 dark:focus:ring-indigo-800"
                                aria-label="OTP digit {{ $index + 1 }}"
                            >
                        @endfor
                    </div>

                    <input type="hidden" name="otp_code" data-otp-hidden>

                    <button
                        type="submit"
                        class="w-full px-4 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold rounded-xl shadow-sm shadow-indigo-200 dark:shadow-indigo-900/30 transition-all hover:shadow-md text-sm"
                    >
                        Verify
                    </button>
                </form>

                {{-- Resend --}}
                <div
                    data-resend-otp
                    data-resend-seconds="{{ $resendRemainingSeconds }}"
                    data-resend-url="{{ route('otp.resend') }}"
                    class="mt-6 text-center"
                >
                    <p class="text-sm text-gray-600 dark:text-gray-400">
                        <span data-resend-countdown>
                            Resend code in
                            <span class="font-medium text-gray-900 dark:text-gray-200" data-resend-seconds-text>{{ $resendRemainingSeconds }}</span>
                            seconds
                        </span>
                        <button
                            data-resend-button
                            type="button"
                            class="hidden font-medium text-indigo-600 dark:text-indigo-400 hover:text-indigo-500 dark:hover:text-indigo-300 transition-colors"
                        >
                            Resend Code
                        </button>
                    </p>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        (() => {
            const otpForm = document.querySelector('[data-otp-form]');

            if (otpForm) {
                const inputs = [...otpForm.querySelectorAll('[data-otp-input]')];
                const hiddenInput = otpForm.querySelector('[data-otp-hidden]');

                const syncOtp = () => {
                    hiddenInput.value = inputs.map((input) => input.value).join('');
                };

                const focusInput = (index) => {
                    inputs[Math.max(0, Math.min(index, inputs.length - 1))]?.focus();
                };

                const fillFrom = (startIndex, value) => {
                    const digits = value.replace(/\D/g, '').slice(0, inputs.length - startIndex).split('');

                    digits.forEach((digit, offset) => {
                        inputs[startIndex + offset].value = digit;
                    });

                    syncOtp();
                    focusInput(Math.min(startIndex + digits.length, inputs.length - 1));
                };

                inputs.forEach((input, index) => {
                    input.addEventListener('input', (event) => {
                        const value = event.target.value.replace(/\D/g, '');

                        if (value.length > 1) {
                            fillFrom(index, value);
                            return;
                        }

                        event.target.value = value;
                        syncOtp();

                        if (value && index < inputs.length - 1) {
                            focusInput(index + 1);
                        }
                    });

                    input.addEventListener('keydown', (event) => {
                        if (event.key === 'Backspace') {
                            event.preventDefault();

                            if (input.value) {
                                input.value = '';
                                syncOtp();
                                return;
                            }

                            if (index > 0) {
                                inputs[index - 1].value = '';
                                syncOtp();
                                focusInput(index - 1);
                            }
                        }

                        if (event.key === 'ArrowLeft' && index > 0) {
                            event.preventDefault();
                            focusInput(index - 1);
                        }

                        if (event.key === 'ArrowRight' && index < inputs.length - 1) {
                            event.preventDefault();
                            focusInput(index + 1);
                        }
                    });

                    input.addEventListener('paste', (event) => {
                        event.preventDefault();
                        fillFrom(index, event.clipboardData.getData('text'));
                    });
                });

                otpForm.addEventListener('submit', syncOtp);
                focusInput(0);
            }

            const resendContainer = document.querySelector('[data-resend-otp]');

            if (resendContainer) {
                const countdownText = resendContainer.querySelector('[data-resend-seconds-text]');
                const countdownWrapper = resendContainer.querySelector('[data-resend-countdown]');
                const resendButton = resendContainer.querySelector('[data-resend-button]');
                let remainingSeconds = Number(resendContainer.dataset.resendSeconds || 0);
                let timer = null;

                const renderResend = () => {
                    if (remainingSeconds > 0) {
                        countdownText.textContent = remainingSeconds;
                        countdownWrapper.classList.remove('hidden');
                        resendButton.classList.add('hidden');
                        return;
                    }

                    countdownWrapper.classList.add('hidden');
                    resendButton.classList.remove('hidden');
                };

                const startCountdown = (seconds) => {
                    remainingSeconds = Number(seconds || 0);
                    clearInterval(timer);
                    renderResend();

                    if (remainingSeconds <= 0) {
                        return;
                    }

                    timer = setInterval(() => {
                        remainingSeconds -= 1;
                        renderResend();

                        if (remainingSeconds <= 0) {
                            clearInterval(timer);
                        }
                    }, 1000);
                };

                resendButton.addEventListener('click', async () => {
                    if (remainingSeconds > 0) {
                        return;
                    }

                    const response = await fetch(resendContainer.dataset.resendUrl, {
                        method: 'POST',
                        headers: {
                            'Accept': 'application/json',
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        },
                    });

                    if (response.status === 429) {
                        const data = await response.json();
                        startCountdown(data.remaining_seconds || 60);
                        return;
                    }

                    if (response.ok) {
                        startCountdown(60);
                    }
                });

                startCountdown(remainingSeconds);
            }
        })();
    </script>
@endpush
