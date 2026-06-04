@extends('layouts.app')

@section('title', 'AI Chat Prediksi')
@section('page-title', 'AI Chat Prediksi Penggunaan Listrik')

@section('content')
<style>
    @keyframes dots {
        0%, 20% { content: '.'; }
        40% { content: '..'; }
        60% { content: '...'; }
        80%, 100% { content: ''; }
    }
    .loading-dots:after {
        content: '.';
        animation: dots 1.5s steps(5, end) infinite;
    }
</style>

<div class="flex flex-col gap-6 max-w-4xl mx-auto">
    <!-- Info Banner -->
    <div class="bg-gradient-to-r from-blue-600 to-blue-700 rounded-3xl p-5 flex items-center gap-4 text-white shadow-lg shadow-blue-600/20">
        <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center shrink-0">
            <i data-lucide="bot" class="w-6 h-6 text-white"></i>
        </div>
        <div>
            <h2 class="font-bold text-lg">AI WattCare Assistant</h2>
            <p class="text-blue-100 text-sm">Tanyakan apapun tentang estimasi konsumsi listrik perangkat Anda.</p>
        </div>
        <div class="ml-auto shrink-0 w-2.5 h-2.5 bg-emerald-400 rounded-full shadow-[0_0_0_4px_rgba(52,211,153,0.3)]"></div>
    </div>

    <!-- Chat Window -->
    <div class="bg-white rounded-3xl overflow-hidden border border-slate-100 shadow-[0_4px_20px_rgb(0,0,0,0.04)] flex flex-col" style="height: 62vh;">
        <!-- Chat Body -->
        <div id="chat-box" class="flex-1 overflow-y-auto p-6 flex flex-col gap-4 bg-slate-50/50">
            <!-- Initial AI bubble -->
            <div class="flex items-start gap-3 max-w-[85%]">
                <div class="w-8 h-8 rounded-full bg-blue-100 flex items-center justify-center shrink-0 mt-1">
                    <i data-lucide="bot" class="w-4 h-4 text-blue-600"></i>
                </div>
                <div class="bg-white rounded-2xl rounded-tl-sm px-4 py-3 shadow-[0_2px_8px_rgb(0,0,0,0.06)] border border-slate-100">
                    <p class="text-sm font-bold text-blue-600 mb-1">🤖 AI WattCare</p>
                    <p class="text-slate-700 text-sm leading-relaxed">
                        Halo! Ceritakan penggunaan alat elektronik yang ingin Anda gunakan, dan saya akan bantu hitung estimasi konsumsinya.
                    </p>
                    <p class="text-slate-400 text-xs italic mt-2">Contoh: "Saya pakai 2 lampu 30 watt selama 3 jam, dan 1 kulkas 160 watt selama 10 jam."</p>
                </div>
            </div>
        </div>

        <!-- Input Area -->
        <form id="chat-form" class="flex gap-3 p-4 bg-white border-t border-slate-100">
            <input type="text" id="chat-input"
                   class="flex-1 bg-slate-50 border border-slate-200 text-slate-900 rounded-2xl pl-5 pr-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all"
                   placeholder="Ketik pesan Anda di sini..."
                   autocomplete="off" required>
            <button type="submit" id="chat-submit"
                    class="w-12 h-12 bg-blue-600 hover:bg-blue-700 text-white rounded-2xl flex items-center justify-center transition-all shadow-sm shadow-blue-600/30 disabled:bg-slate-300 disabled:shadow-none disabled:cursor-not-allowed shrink-0">
                <i data-lucide="send" class="w-5 h-5"></i>
            </button>
        </form>
    </div>

    <!-- Hint Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        @foreach([
            ['icon' => 'lightbulb', 'title' => 'Lampu & Pencahayaan', 'example' => '"3 lampu 20W, 8 jam/hari, berapa kWh sebulan?"'],
            ['icon' => 'tv-2', 'title' => 'Elektronik Rumah', 'example' => '"TV 100W 1 unit + AC 900W 1 unit tiap hari 5 jam, estimasinya?"'],
            ['icon' => 'zap', 'title' => 'Tips Hemat Energi', 'example' => '"Bagaimana cara menghemat listrik kulkas?"'],
        ] as $hint)
        <button type="button" onclick="document.getElementById('chat-input').value='{{ $hint['example'] }}'; document.getElementById('chat-input').focus();"
                class="text-left bg-white rounded-2xl p-4 border border-slate-100 hover:border-blue-200 hover:bg-blue-50/30 transition-all group shadow-sm">
            <div class="flex items-center gap-2 mb-2">
                <div class="w-8 h-8 bg-blue-50 rounded-lg flex items-center justify-center group-hover:bg-blue-100 transition-colors">
                    <i data-lucide="{{ $hint['icon'] }}" class="w-4 h-4 text-blue-600"></i>
                </div>
                <span class="text-sm font-semibold text-slate-900">{{ $hint['title'] }}</span>
            </div>
            <p class="text-xs text-slate-500 leading-relaxed">{{ $hint['example'] }}</p>
        </button>
        @endforeach
    </div>
</div>

@endsection

@stack('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const chatForm = document.getElementById('chat-form');
    const chatInput = document.getElementById('chat-input');
    const chatBox = document.getElementById('chat-box');
    const submitBtn = document.getElementById('chat-submit');

    chatForm.addEventListener('submit', async function(e) {
        e.preventDefault();

        const message = chatInput.value.trim();
        if (!message) return;

        // Show user message on the right
        appendBubble(message, 'user');

        chatInput.value = '';
        chatInput.disabled = true;
        submitBtn.disabled = true;

        // Show loading bubble on the left
        const loadingId = 'loading-' + Date.now();
        appendBubble('<span class="loading-dots">.</span>', 'ai', loadingId, true);

        try {
            const response = await fetch('{{ route('chat.send') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({ message: message })
            });

            const data = await response.json();

            // Remove loading bubble
            const loadingBubble = document.getElementById(loadingId);
            if (loadingBubble) loadingBubble.remove();

            if (response.ok) {
                appendBubble(data.reply, 'ai', null, true);
            } else {
                appendBubble('Terjadi kesalahan sistem.', 'ai');
            }
        } catch (error) {
            const loadingBubble = document.getElementById(loadingId);
            if (loadingBubble) loadingBubble.remove();
            appendBubble('Koneksi gagal. Silakan coba lagi.', 'ai');
        } finally {
            chatInput.disabled = false;
            submitBtn.disabled = false;
            chatInput.focus();
        }
    });

    function appendBubble(text, senderType, id = null, isHtml = false) {
        const wrapper = document.createElement('div');

        if (senderType === 'ai') {
            wrapper.className = 'flex items-start gap-3 max-w-[85%]';
            wrapper.innerHTML = `
                <div class="w-8 h-8 rounded-full bg-blue-100 flex items-center justify-center shrink-0 mt-1">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#2563eb" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 8V4H8"/><rect width="16" height="12" x="4" y="8" rx="2"/><path d="M2 14h2"/><path d="M20 14h2"/><path d="M15 13v2"/><path d="M9 13v2"/></svg>
                </div>
                <div class="bg-white rounded-2xl rounded-tl-sm px-4 py-3 shadow-[0_2px_8px_rgb(0,0,0,0.06)] border border-slate-100">
                    <p class="text-sm font-bold text-blue-600 mb-1">🤖 AI WattCare</p>
                    <div class="text-slate-700 text-sm leading-relaxed">${isHtml ? text : escapeHtml(text)}</div>
                </div>
            `;
        } else {
            wrapper.className = 'flex justify-end';
            wrapper.innerHTML = `
                <div class="bg-blue-600 text-white rounded-2xl rounded-tr-sm px-4 py-3 max-w-[85%] shadow-sm">
                    <p class="text-sm leading-relaxed">${isHtml ? text : escapeHtml(text)}</p>
                </div>
            `;
        }

        // If an ID is provided (used for loading bubble), assign it to the wrapper
        if (id) {
            wrapper.id = id;
        }

        chatBox.appendChild(wrapper);
        chatBox.scrollTop = chatBox.scrollHeight;
    }

    function escapeHtml(unsafe) {
        return unsafe
            .replace(/&/g, "&amp;")
            .replace(/</g, "&lt;")
            .replace(/>/g, "&gt;")
            .replace(/"/g, "&quot;")
            .replace(/'/g, "&#039;");
    }
});
</script>
