@extends('layouts.app')

@section('title', 'Pengingat Alat')
@section('page-title', 'Pengingat Penggunaan Alat Elektronik')

@section('content')
<style>
    @keyframes modalPop {
        0% { transform: scale(0.9); opacity: 0; }
        100% { transform: scale(1); opacity: 1; }
    }
    @keyframes pulse {
        0% { transform: scale(1); }
        50% { transform: scale(1.08); }
        100% { transform: scale(1); }
    }

    @keyframes slideIn {
        from { transform: translateY(10px); opacity: 0 }
        to { transform: translateY(0); opacity: 1 }
    }

    .toast {
        animation: modalPop 0.25s ease, slideIn 0.2s ease;
        border-radius: 12px;
        padding: 10px 14px;
        display: flex;
        gap: 10px;
        align-items: center;
        box-shadow: 0 8px 24px rgba(15,23,42,0.12);
        background: white;
        color: #0f172a;
    }

    .toast .icon {
        width: 36px;
        height: 36px;
        display: grid;
        place-items: center;
        border-radius: 9px;
        background: linear-gradient(135deg,#10b981,#06b6d4);
        color: white;
    }
</style>

<div class="max-w-5xl mx-auto space-y-8">
    <div class="bg-white rounded-3xl p-6 md:p-8 shadow-[0_4px_20px_rgb(0,0,0,0.03)] border border-slate-100">
        <div class="flex items-center gap-3 mb-2">
            <div class="w-10 h-10 rounded-xl bg-blue-50 flex items-center justify-center text-blue-600 shrink-0">
                <i data-lucide="clock" class="w-5 h-5"></i>
            </div>
            <h3 class="text-xl font-bold text-slate-900">Mulai Timer Baru</h3>
        </div>
        <p class="text-slate-500 text-sm mb-6 ml-13">Atur pengingat untuk alat yang sedang Anda gunakan. Anda dapat menjalankan banyak pengingat sekaligus.</p>

        <form id="reminder-form" class="ml-13 space-y-5">
            <div class="space-y-2">
                <label for="device_id" class="block text-sm font-semibold text-slate-700">Pilih Alat Elektronik</label>
                <div class="relative">
                        <select name="device_id" id="device_id" required aria-describedby="device_select_help"
                            class="w-full bg-slate-50 border border-slate-200 text-slate-900 rounded-xl pl-4 pr-4 py-3 appearance-none focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all font-medium cursor-pointer">
                        <option value="">-- Pilih Alat --</option>
                        @foreach($devices as $device)
                            <option value="{{ $device->id }}" data-name="{{ $device->nama_device }}">{{ $device->nama_device }} ({{ $device->daya_watt }} W)</option>
                        @endforeach
                    </select>
                    
                    <span id="device_select_help" class="sr-only">Pilih perangkat elektronik</span>
                </div>
            </div>

            <div class="flex gap-4">
                <div class="space-y-2 flex-2">
                    <label for="duration" class="block text-sm font-semibold text-slate-700">Durasi Waktu</label>
                    <input type="number" id="duration" min="1" step="1" required placeholder="Contoh: 30" 
                           class="w-full bg-slate-50 border border-slate-200 text-slate-900 rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all font-medium">
                </div>
                <div class="space-y-2 flex-1">
                    <label for="unit" class="block text-sm font-semibold text-slate-700">Satuan</label>
                    <div class="relative">
                        <select id="unit" aria-describedby="unit_select_help" class="w-full bg-slate-50 border border-slate-200 text-slate-900 rounded-xl pl-4 pr-4 py-3 appearance-none focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all font-medium cursor-pointer">
                            <option value="minutes">Menit</option>
                            <option value="hours">Jam</option>
                        </select>
                        
                        <span id="unit_select_help" class="sr-only">Pilih satuan waktu (menit atau jam)</span>
                    </div>
                </div>
            </div>

            <button type="submit" class="w-full py-3.5 px-4 bg-blue-600 hover:bg-blue-500 text-white font-semibold rounded-xl shadow-sm transition-all flex items-center justify-center gap-2">
                <i data-lucide="rocket" class="w-5 h-5"></i>
                Tambahkan Timer
            </button>
        </form>
    </div>

    <!-- Daftar Timer Aktif -->
    <div>
        <div class="flex items-center justify-between border-b border-slate-200 pb-4 mb-6">
            <h3 class="text-xl font-bold text-slate-900 flex items-center gap-2">
                <i data-lucide="activity" class="w-5 h-5 text-blue-600"></i>
                Daftar Pengingat Aktif
            </h3>
        </div>
        
        <div id="empty-state" class="bg-slate-50 border border-slate-200 border-dashed rounded-3xl p-12 text-center" style="display: none;">
            <div class="w-16 h-16 bg-white rounded-full flex items-center justify-center mx-auto mb-4 text-slate-400 shadow-sm">
                <i data-lucide="timer" class="w-8 h-8"></i>
            </div>
            <p class="text-slate-500 text-lg font-medium">Belum ada timer yang berjalan saat ini.</p>
        </div>

        <div id="timers-container" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <!-- Timer cards will be injected here by JS -->
        </div>
    </div>
</div>

<!-- Modal Notifikasi Timer Habis -->
<div id="timer-modal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(15,23,42,0.6); z-index: 9999; align-items: center; justify-content: center; backdrop-filter: blur(4px);">
    <div style="animation: modalPop 0.3s cubic-bezier(0.16, 1, 0.3, 1);" class="bg-white p-8 rounded-3xl w-11/12 max-w-sm text-center shadow-2xl relative overflow-hidden">
        <div class="w-20 h-20 bg-emerald-50 rounded-full flex items-center justify-center mx-auto mb-4 pulse">
        </div>
        <h2 class="text-2xl font-bold text-slate-900 mb-2">Waktu Habis!</h2>
        <p class="text-slate-500 text-sm mb-6 leading-relaxed">
            Pengingat untuk penggunaan alat <br>
            <strong id="modal-device-name" class="text-slate-900 text-lg block mt-1"></strong> <br>
            telah selesai.
        </p>
        <button type="button" id="btn-close-modal" class="w-full py-3 bg-emerald-600 hover:bg-emerald-700 text-white font-bold rounded-xl transition-all shadow-lg shadow-emerald-600/20">
            Oke, Mengerti
        </button>
    </div>
</div>

<!-- Toast container -->
<div id="toast-container" style="position: fixed; right: 20px; bottom: 24px; z-index: 99999; display: flex; flex-direction: column; gap: 12px;"></div>

<!-- Audio Notifikasi -->
<audio id="timerSound" preload="auto">
    <source src="{{ asset('sounds/myinstants.mp3') }}" type="audio/mpeg">
</audio>

@endsection

@stack('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('reminder-form');
    const deviceSelect = document.getElementById('device_id');
    const durationInput = document.getElementById('duration');
    const unitSelect = document.getElementById('unit');
    const timersContainer = document.getElementById('timers-container');
    const emptyState = document.getElementById('empty-state');
    
    // Modal elements
    const timerModal = document.getElementById('timer-modal');
    const modalDeviceName = document.getElementById('modal-device-name');
    const btnCloseModal = document.getElementById('btn-close-modal');

    let timers = [];
    let globalInterval = null;
    let modalQueue = [];

    btnCloseModal.addEventListener('click', () => {
        const sound = document.getElementById('timerSound');
        sound.pause();
        sound.currentTime = 0;
        timerModal.style.display = 'none';
        
        // Show next modal if queued
        if (modalQueue.length > 0) {
            const nextDevice = modalQueue.shift();
            setTimeout(() => {
                showModal(nextDevice);
            }, 300);
        }
    });

    function showModal(deviceName) {
        const sound = document.getElementById('timerSound');
        if (timerModal.style.display === 'flex') {
            modalQueue.push(deviceName);
        } else {
            modalDeviceName.innerText = deviceName;
            timerModal.style.display = 'flex';
            // Mainkan suara
            sound.currentTime = 0;
            sound.loop = true;
            sound.play().catch(error => {
                console.log('Autoplay dicegah browser:', error);
            });
            if(window.lucide) {
                window.lucide.createIcons();
            }
            // show a quick toast for visibility
            showToast(`${deviceName} telah selesai`);
        }
    }

    function showToast(message) {
        const container = document.getElementById('toast-container');
        if (!container) return;
        const el = document.createElement('div');
        el.className = 'toast';

        const iconWrap = document.createElement('div');
        iconWrap.className = 'icon';
        iconWrap.style.display = 'grid';
        iconWrap.style.placeItems = 'center';

        const icon = document.createElement('i');
        icon.setAttribute('data-lucide', 'timer');
        icon.className = 'w-5 h-5 text-white';
        iconWrap.appendChild(icon);

        const text = document.createElement('div');
        text.style.fontWeight = '600';
        text.style.fontSize = '14px';
        text.innerText = message;

        el.appendChild(iconWrap);
        el.appendChild(text);
        container.appendChild(el);

        if(window.lucide) window.lucide.createIcons();

        setTimeout(() => {
            el.style.transition = 'opacity 0.35s, transform 0.35s';
            el.style.opacity = '0';
            el.style.transform = 'translateY(10px)';
            setTimeout(() => el.remove(), 400);
        }, 3500);
    }

    // Load from local storage
    const savedState = localStorage.getItem('wattcare_timers_list');
    if (savedState) {
        timers = JSON.parse(savedState);
        renderTimers();
        startGlobalClock();
    } else {
        updateEmptyState();
    }

    form.addEventListener('submit', function(e) {
        e.preventDefault();
        
        const duration = parseFloat(durationInput.value);
        const unit = unitSelect.value;
        const selectedDeviceId = deviceSelect.value;
        const selectedDeviceName = deviceSelect.options[deviceSelect.selectedIndex].getAttribute('data-name');
        
        let seconds = 0;
        let hoursForLog = 0;
        
        if (unit === 'minutes') {
            seconds = duration * 60;
            hoursForLog = duration / 60;
        } else {
            seconds = duration * 3600;
            hoursForLog = duration;
        }

        const newTimer = {
            id: 'timer_' + Date.now() + Math.random().toString(36).substr(2, 5),
            deviceId: selectedDeviceId,
            deviceName: selectedDeviceName,
            endTime: Date.now() + (seconds * 1000),
            hoursForLog: hoursForLog,
            originalDuration: duration,
            originalUnit: unit === 'minutes' ? 'Menit' : 'Jam',
            isFinished: false,
            notified: false
        };
        
        timers.push(newTimer);
        saveTimers();
        renderTimers();
        startGlobalClock();
        
        // Reset form
        deviceSelect.value = '';
        durationInput.value = '';
    });

    function saveTimers() {
        localStorage.setItem('wattcare_timers_list', JSON.stringify(timers));
    }

    function removeTimer(id) {
        timers = timers.filter(t => t.id !== id);
        saveTimers();
        renderTimers();
        
        if (timers.length === 0 && globalInterval) {
            clearInterval(globalInterval);
            globalInterval = null;
        }
        updateEmptyState();
    }

    function updateEmptyState() {
        if (timers.length === 0) {
            emptyState.style.display = 'block';
        } else {
            emptyState.style.display = 'none';
        }
    }

    function renderTimers() {
        updateEmptyState();
        timersContainer.innerHTML = '';
        
        timers.forEach(timer => {
            const card = document.createElement('div');
            card.className = 'bg-white rounded-3xl p-6 shadow-sm border border-slate-100 flex flex-col items-center text-center transition-all relative overflow-hidden ' + 
                            (timer.isFinished ? 'border-emerald-200 bg-emerald-50 shadow-[0_8px_30px_rgb(16,185,129,0.15)]' : 'shadow-[0_4px_20px_rgb(0,0,0,0.03)]');
            card.id = `card-${timer.id}`;
            
            if(timer.isFinished) {
                const accentBar = document.createElement('div');
                accentBar.className = 'absolute top-0 left-0 w-full h-1.5 bg-gradient-to-r from-emerald-500 to-emerald-400';
                card.appendChild(accentBar);

                const topIcon = document.createElement('div');
                topIcon.style.width = '56px';
                topIcon.style.height = '56px';
                topIcon.style.display = 'grid';
                topIcon.style.placeItems = 'center';
                topIcon.innerHTML = '<i data-lucide="timer" class="w-6 h-6 text-emerald-500"></i>';
                card.appendChild(topIcon);
            }

            const nameEl = document.createElement('div');
            nameEl.className = 'font-bold text-lg text-slate-900 mb-2 truncate w-full';
            nameEl.innerText = timer.deviceName;
            
            const displayEl = document.createElement('div');
            displayEl.className = 'text-4xl font-mono font-bold tracking-widest my-4 ' + (timer.isFinished ? 'text-emerald-600' : 'text-slate-800');
            displayEl.id = `display-${timer.id}`;
            
            const statusEl = document.createElement('div');
            statusEl.className = 'text-xs font-semibold px-3 py-1 rounded-full mb-6 ' + (timer.isFinished ? 'bg-emerald-200 text-emerald-800' : 'bg-slate-100 text-slate-600');
            statusEl.id = `status-${timer.id}`;
            statusEl.innerText = `Target: ${timer.originalDuration} ${timer.originalUnit}`;

            const buttonsEl = document.createElement('div');
            buttonsEl.className = 'w-full flex gap-3 mt-auto';

            if (timer.isFinished) {
                displayEl.innerText = "00:00:00";
                statusEl.innerText = "Selesai!";
                
                const btnSave = document.createElement('button');
                btnSave.className = 'flex-1 py-2.5 px-4 bg-emerald-600 hover:bg-emerald-700 text-white font-semibold rounded-xl transition-all shadow-sm';
                btnSave.innerText = 'Catat';
                btnSave.onclick = () => saveRecord(timer.id, btnSave);
                
                const btnDismiss = document.createElement('button');
                btnDismiss.className = 'flex-1 py-2.5 px-4 bg-white border border-slate-200 hover:bg-slate-50 text-slate-700 font-semibold rounded-xl transition-all';
                btnDismiss.innerText = 'Batal';
                btnDismiss.onclick = () => removeTimer(timer.id);
                
                buttonsEl.appendChild(btnSave);
                buttonsEl.appendChild(btnDismiss);
            } else {
                displayEl.innerText = "--:--:--"; 
                statusEl.innerText = "Berjalan...";
                
                const btnCancel = document.createElement('button');
                btnCancel.className = 'w-full py-2.5 px-4 bg-red-50 hover:bg-red-100 text-red-600 font-semibold rounded-xl transition-all';
                btnCancel.innerText = 'Batalkan';
                btnCancel.onclick = () => removeTimer(timer.id);
                
                buttonsEl.appendChild(btnCancel);
            }

            card.appendChild(nameEl);
            card.appendChild(displayEl);
            card.appendChild(statusEl);
            card.appendChild(buttonsEl);
            
            timersContainer.appendChild(card);
        });
        
        tickClock();
    }

    function startGlobalClock() {
        if (!globalInterval && timers.length > 0) {
            globalInterval = setInterval(tickClock, 1000);
        }
    }

    function tickClock() {
        let needsRender = false;
        const now = Date.now();

        timers.forEach(timer => {
            if (timer.isFinished) return;
            
            const remainMs = timer.endTime - now;
            const displayEl = document.getElementById(`display-${timer.id}`);
            
            if (remainMs <= 0) {
                timer.isFinished = true;
                needsRender = true;
                
                if (!timer.notified) {
                    timer.notified = true;
                    
                    // Show centered modal
                    showModal(timer.deviceName);

                    if (Notification.permission === "granted") {
                        new Notification("Waktu Penggunaan Habis!", {
                            body: `Alat ${timer.deviceName} telah selesai digunakan.`,
                            icon: '/favicon.ico'
                        });
                    } else if (Notification.permission !== "denied") {
                        Notification.requestPermission().then(permission => {
                            if (permission === "granted") {
                                new Notification("Waktu Habis", { body: `Alat ${timer.deviceName} selesai.` });
                            }
                        });
                    }
                }
            } else {
                if (displayEl) {
                    const totalS = Math.floor(remainMs / 1000);
                    const h = String(Math.floor(totalS / 3600)).padStart(2, '0');
                    const m = String(Math.floor((totalS % 3600) / 60)).padStart(2, '0');
                    const s = String(totalS % 60).padStart(2, '0');
                    displayEl.innerText = `${h}:${m}:${s}`;
                }
            }
        });

        if (needsRender) {
            saveTimers();
            renderTimers();
        }
    }

    async function saveRecord(timerId, btnElement) {
        const timer = timers.find(t => t.id === timerId);
        if (!timer) return;

        const oldText = btnElement.innerText;
        btnElement.innerText = '...';
        btnElement.disabled = true;

        try {
            const response = await fetch('{{ route('reminder.store') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    device_id: timer.deviceId,
                    jam_pemakaian: timer.hoursForLog
                })
            });

            const result = await response.json();
            
            if (response.ok) {
                removeTimer(timerId);
            } else {
                alert('Gagal: ' + (result.message || 'Terjadi kesalahan.'));
                btnElement.innerText = oldText;
                btnElement.disabled = false;
            }
        } catch (error) {
            alert('Kesalahan jaringan. Gagal menghubungi server.');
            btnElement.innerText = oldText;
            btnElement.disabled = false;
        }
    }
});
</script>
