@extends('layouts.app')

@section('title', 'Attendance Check-In')

@section('content')
<div
    x-data="checkInApp()"
    x-init="init()"
    class="max-w-2xl mx-auto space-y-6"
>
    {{-- Status Alerts --}}
    @if (session('success'))
        <div class="rounded-xl border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-700 dark:border-green-800 dark:bg-green-900/30 dark:text-green-300">
            {{ session('success') }}
        </div>
    @endif

    @if ($checkedOut)
        <div class="rounded-xl border border-blue-200 bg-blue-50 px-4 py-3 text-sm text-blue-700 dark:border-blue-800 dark:bg-blue-900/30 dark:text-blue-300">
            You have completed your attendance for today. Check-in: {{ $todayAttendance->clock_in?->format('H:i') }} | Check-out: {{ $todayAttendance->clock_out?->format('H:i') }}
        </div>
    @endif

    {{-- Work Location Info --}}
    @if ($workLocationCheck)
    <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 p-5 shadow-sm">
        <div class="flex items-start gap-4">
            <div class="w-10 h-10 bg-indigo-100 dark:bg-indigo-900/50 rounded-xl flex items-center justify-center shrink-0">
                <svg class="w-5 h-5 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
            </div>
            <div>
                <h3 class="text-sm font-semibold text-gray-900 dark:text-white">{{ $workLocationCheck['name'] }}</h3>
                @if ($workLocationCheck['address'])
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">{{ $workLocationCheck['address'] }}</p>
                @endif
                <p class="text-xs text-gray-400 dark:text-gray-500 mt-0.5">Radius: {{ $workLocationCheck['radius'] }}m</p>
            </div>
        </div>
    </div>
    @endif

    {{-- Main Card --}}
    <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 shadow-sm overflow-hidden">
        {{-- Header --}}
        <div class="px-6 pt-6 pb-4 border-b border-gray-100 dark:border-gray-700">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-xl font-bold text-gray-900 dark:text-white">
                        {{ $checkedIn ? 'Check Out' : 'Check In' }}
                    </h2>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                        {{ now()->format('l, d F Y') }}
                    </p>
                </div>
                <div class="text-right">
                    <p class="text-2xl font-bold text-indigo-600 dark:text-indigo-400" x-text="currentTime"></p>
                    <p class="text-xs text-gray-400 dark:text-gray-500">Current time</p>
                </div>
            </div>
        </div>

        <div class="p-6 space-y-6">
            {{-- Camera Section --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">Take a Selfie</label>
                <div class="relative bg-gray-900 rounded-xl overflow-hidden aspect-[4/3] flex items-center justify-center">
                    {{-- Camera Stream --}}
                    <video
                        x-ref="video"
                        x-show="!capturedPhoto && cameraActive"
                        autoplay
                        playsinline
                        class="w-full h-full object-cover"
                    ></video>

                    {{-- Captured Photo Preview --}}
                    <template x-if="capturedPhoto">
                        <img :src="capturedPhoto" class="w-full h-full object-cover" />
                    </template>

                    {{-- Camera Loading / Off --}}
                    <div
                        x-show="!cameraActive && !capturedPhoto"
                        class="text-center text-gray-400"
                    >
                        <svg class="w-16 h-16 mx-auto mb-3 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z"/>
                        </svg>
                        <p class="text-sm" x-text="cameraError || 'Camera not available'"></p>
                    </div>

                    {{-- Camera Controls Overlay --}}
                    <div class="absolute bottom-4 left-0 right-0 flex justify-center gap-3">
                        <template x-if="cameraActive && !capturedPhoto">
                            <button
                                type="button"
                                @click="capturePhoto()"
                                class="w-14 h-14 rounded-full bg-white shadow-lg flex items-center justify-center hover:scale-105 transition-transform"
                            >
                                <div class="w-10 h-10 rounded-full border-2 border-gray-800"></div>
                            </button>
                        </template>
                        <template x-if="capturedPhoto">
                            <button
                                type="button"
                                @click="retakePhoto()"
                                class="px-4 py-2 bg-white/90 backdrop-blur rounded-lg text-sm font-medium text-gray-800 shadow hover:bg-white transition-colors"
                            >
                                Retake
                            </button>
                        </template>
                    </div>
                </div>
            </div>

            {{-- GPS Status --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">Location</label>
                <div class="bg-gray-50 dark:bg-gray-900/50 rounded-xl p-4 space-y-2">
                    {{-- GPS Loading --}}
                    <div x-show="!gpsReady && !gpsError" class="flex items-center gap-3 text-sm text-gray-500 dark:text-gray-400">
                        <svg class="w-5 h-5 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        Acquiring GPS location...
                    </div>

                    {{-- GPS Error --}}
                    <div x-show="gpsError" class="flex items-center gap-3 text-sm text-red-600 dark:text-red-400">
                        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                        </svg>
                        <span x-text="gpsError"></span>
                    </div>

                    {{-- GPS Ready --}}
                    <template x-if="gpsReady">
                        <div class="space-y-1.5">
                            <div class="flex items-center gap-2 text-sm">
                                <span class="w-2 h-2 rounded-full bg-green-500 shrink-0"></span>
                                <span class="text-gray-700 dark:text-gray-300 font-medium">Location acquired</span>
                            </div>
                            <p class="text-xs text-gray-500 dark:text-gray-400">
                                Lat: <span x-text="gpsLatitude?.toFixed(6)"></span> | Lng: <span x-text="gpsLongitude?.toFixed(6)"></span>
                                | Accuracy: <span x-text="gpsAccuracy?.toFixed(1)"></span>m
                            </p>

                            {{-- Work Location Range Check --}}
                            @if ($workLocationCheck)
                            <div x-show="gpsReady">
                                <template x-if="withinRange === true">
                                    <p class="text-xs text-green-600 dark:text-green-400 mt-1">You are within the allowed location range.</p>
                                </template>
                                <template x-if="withinRange === false">
                                    <p class="text-xs text-red-600 dark:text-red-400 mt-1" x-text="rangeMessage"></p>
                                </template>
                                <template x-if="withinRange === null && gpsReady">
                                    <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">Checking location range...</p>
                                </template>
                            </div>
                            @endif

                            {{-- Fake GPS Warning --}}
                            <template x-if="mockLocationDetected">
                                <p class="text-xs text-red-600 dark:text-red-400 mt-1 flex items-center gap-1">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                                    </svg>
                                    Suspicious GPS activity detected. Please disable any GPS mocking apps.
                                </p>
                            </template>
                            <template x-if="gpsReadings > 1 && !mockLocationDetected">
                                <p class="text-xs text-green-600 dark:text-green-400 mt-1">GPS readings verified (<span x-text="gpsReadings"></span> samples). Location looks authentic.</p>
                            </template>
                        </div>
                    </template>
                </div>
            </div>

            {{-- Hidden Form --}}
            <form
                method="POST"
                action="{{ route('attendances.check-in.store') }}"
                enctype="multipart/form-data"
                id="checkin-form"
            >
                @csrf
                <input type="hidden" name="action" x-model="formAction">
                <input type="hidden" name="latitude" :value="gpsLatitude">
                <input type="hidden" name="longitude" :value="gpsLongitude">
                <input type="hidden" name="gps_accuracy" :value="gpsAccuracy">
                <input type="hidden" name="is_mock_location" :value="mockLocationDetected">
                <input type="file" name="selfie" id="selfie-input" accept="image/jpeg,image/png" class="hidden">

                {{-- Submit --}}
                <button
                    type="button"
                    @click="submitCheckIn()"
                    :disabled="submitting || !canSubmit"
                    class="w-full rounded-xl px-6 py-3.5 text-base font-semibold text-white shadow-sm transition-all focus:outline-none focus:ring-2 focus:ring-offset-2 disabled:cursor-not-allowed"
                    :class="submitting || !canSubmit
                        ? 'bg-gray-300 dark:bg-gray-600 text-gray-500 dark:text-gray-400'
                        : formAction === 'check_out'
                            ? 'bg-amber-600 hover:bg-amber-700 focus:ring-amber-500'
                            : 'bg-indigo-600 hover:bg-indigo-700 focus:ring-indigo-500'
                    "
                >
                    <span x-show="!submitting" x-text="formAction === 'check_out' ? 'Check Out' : 'Check In'"></span>
                    <span x-show="submitting">Processing...</span>
                </button>
            </form>
        </div>
    </div>

    {{-- History Link --}}
    <div class="text-center">
        <a href="{{ route('attendances.check-in.history') }}" class="text-sm font-medium text-indigo-600 dark:text-indigo-400 hover:text-indigo-500 dark:hover:text-indigo-300 transition-colors">
            View your check-in history
        </a>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function checkInApp() {
        return {
            currentTime: '',
            cameraActive: false,
            cameraError: '',
            capturedPhoto: null,
            stream: null,

            gpsReady: false,
            gpsError: '',
            gpsLatitude: null,
            gpsLongitude: null,
            gpsAccuracy: null,
            gpsReadings: 0,
            previousCoordinates: [],

            mockLocationDetected: false,
            withinRange: null,
            rangeMessage: '',

            submitting: false,
            formAction: '{{ $checkedIn ? "check_out" : "check_in" }}',

            get canSubmit() {
                return this.capturedPhoto && this.gpsReady && !this.submitting;
            },

            init() {
                this.updateClock();
                setInterval(() => this.updateClock(), 1000);
                this.startCamera();
                this.acquireGPS();
            },

            updateClock() {
                const now = new Date();
                this.currentTime = now.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit', second: '2-digit', hour12: false });
            },

            startCamera() {
                if (!navigator.mediaDevices?.getUserMedia) {
                    this.cameraError = 'Camera not supported on this device/browser.';
                    return;
                }

                navigator.mediaDevices.getUserMedia({ video: { facingMode: 'user', width: { ideal: 640 }, height: { ideal: 480 } } })
                    .then((stream) => {
                        this.stream = stream;
                        this.cameraActive = true;
                        this.cameraError = '';
                        this.$nextTick(() => {
                            const video = this.$refs.video;
                            if (video) {
                                video.srcObject = stream;
                            }
                        });
                    })
                    .catch((err) => {
                        if (err.name === 'NotAllowedError') {
                            this.cameraError = 'Camera access denied. Please allow camera access.';
                        } else if (err.name === 'NotFoundError') {
                            this.cameraError = 'No camera found on this device.';
                        } else {
                            this.cameraError = 'Camera error: ' + err.message;
                        }
                    });
            },

            capturePhoto() {
                const video = this.$refs.video;
                if (!video) return;

                const canvas = document.createElement('canvas');
                canvas.width = video.videoWidth;
                canvas.height = video.videoHeight;
                const ctx = canvas.getContext('2d');
                ctx.drawImage(video, 0, 0);
                this.capturedPhoto = canvas.toDataURL('image/jpeg', 0.85);

                canvas.toBlob((blob) => {
                    const file = new File([blob], 'selfie.jpg', { type: 'image/jpeg' });
                    const dataTransfer = new DataTransfer();
                    dataTransfer.items.add(file);
                    document.getElementById('selfie-input').files = dataTransfer.files;
                }, 'image/jpeg', 0.85);

                if (this.stream) {
                    this.stream.getTracks().forEach(track => track.stop());
                }
                this.cameraActive = false;
            },

            retakePhoto() {
                this.capturedPhoto = null;
                document.getElementById('selfie-input').value = '';
                this.startCamera();
            },

            acquireGPS() {
                if (!navigator.geolocation) {
                    this.gpsError = 'Geolocation is not supported by this browser.';
                    return;
                }

                const takeReading = () => {
                    navigator.geolocation.getCurrentPosition(
                        (position) => {
                            const { latitude, longitude, accuracy } = position.coords;

                            this.gpsLatitude = latitude;
                            this.gpsLongitude = longitude;
                            this.gpsAccuracy = accuracy;
                            this.gpsReadings++;

                            this.previousCoordinates.push({ latitude, longitude });

                            if (this.gpsReadings >= 3) {
                                this.detectMockGPS();
                                this.checkLocationRange();
                            }

                            this.gpsReady = true;
                            this.gpsError = '';
                        },
                        (error) => {
                            let message = 'Unable to retrieve your location.';
                            if (error.code === error.PERMISSION_DENIED) {
                                message = 'Location access denied. Please allow location access.';
                            } else if (error.code === error.TIMEOUT) {
                                message = 'GPS timeout. Please try again.';
                            } else if (error.code === error.POSITION_UNAVAILABLE) {
                                message = 'GPS unavailable. Try moving to an open area.';
                            }
                            this.gpsError = message;
                        },
                        {
                            enableHighAccuracy: true,
                            timeout: 15000,
                            maximumAge: 0,
                        }
                    );
                };

                takeReading();
                setTimeout(takeReading, 2000);
                setTimeout(takeReading, 4000);
            },

            detectMockGPS() {
                const coords = this.previousCoordinates;

                if (coords.length < 2) return;

                const allExactMatch = coords.every((c, i, arr) => {
                    if (i === 0) return true;
                    return c.latitude === arr[0].latitude && c.longitude === arr[0].longitude;
                });

                if (allExactMatch && coords.length >= 2) {
                    this.mockLocationDetected = true;
                    return;
                }

                if (this.gpsAccuracy !== null && this.gpsAccuracy < 3) {
                    this.mockLocationDetected = true;
                    return;
                }

                this.mockLocationDetected = false;
            },

            checkLocationRange() {
                @if ($workLocationCheck)
                const targetLat = {{ $workLocationCheck['latitude'] ?? 'null' }};
                const targetLng = {{ $workLocationCheck['longitude'] ?? 'null' }};
                const maxRadius = {{ $workLocationCheck['radius'] ?? 100 }};

                if (targetLat === null || targetLng === null || this.gpsLatitude === null) {
                    this.withinRange = null;
                    return;
                }

                const distance = this.haversineDistance(
                    this.gpsLatitude, this.gpsLongitude,
                    targetLat, targetLng
                );

                this.withinRange = distance <= maxRadius;
                this.rangeMessage = distance <= maxRadius
                    ? 'You are within the allowed location range.'
                    : `You are ${distance.toFixed(0)}m away from your work location (allowed: ${maxRadius}m).`;
                @endif
            },

            haversineDistance(lat1, lng1, lat2, lng2) {
                const R = 6371000;
                const dLat = (lat2 - lat1) * Math.PI / 180;
                const dLng = (lng2 - lng1) * Math.PI / 180;
                const a = Math.sin(dLat / 2) * Math.sin(dLat / 2)
                    + Math.cos(lat1 * Math.PI / 180) * Math.cos(lat2 * Math.PI / 180)
                    * Math.sin(dLng / 2) * Math.sin(dLng / 2);
                const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
                return R * c;
            },

            submitCheckIn() {
                if (!this.canSubmit) return;
                this.submitting = true;
                document.getElementById('checkin-form').submit();
            },
        };
    }
</script>
@endpush
