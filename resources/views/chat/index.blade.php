@extends('layouts.app')

@section('title', 'AI Chat Prediksi')
@section('page-title', 'AI Chat Prediksi Penggunaan Listrik')

@section('content')
<style>
    .chat-container {
        display: flex;
        flex-direction: column;
        height: 65vh;
        background: #f0f2f5;
        border-radius: 12px;
        overflow: hidden;
        border: 1px solid #e5e7eb;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
    }

    .chat-box {
        flex-grow: 1;
        overflow-y: auto;
        padding: 1.5rem;
        display: flex;
        flex-direction: column;
        gap: 1rem;
    }

    .chat-bubble {
        max-width: 80%;
        padding: 0.8rem 1.2rem;
        border-radius: 16px;
        font-size: 0.95rem;
        line-height: 1.5;
        position: relative;
        word-wrap: break-word;
    }

    .chat-bubble-ai {
        align-self: flex-start;
        background-color: #ffffff;
        color: #1f2937;
        border-bottom-left-radius: 4px;
        box-shadow: 0 1px 2px rgba(0,0,0,0.1);
    }

    .chat-bubble-user {
        align-self: flex-end;
        background-color: #10b981; /* Tema hijau */
        color: #ffffff;
        border-bottom-right-radius: 4px;
        box-shadow: 0 1px 2px rgba(0,0,0,0.1);
    }

    .chat-input-area {
        display: flex;
        gap: 0.75rem;
        padding: 1rem;
        background: #ffffff;
        border-top: 1px solid #e5e7eb;
    }

    .chat-input {
        flex-grow: 1;
        padding: 0.8rem 1rem;
        border: 1px solid #d1d5db;
        border-radius: 24px;
        outline: none;
        transition: border-color 0.2s;
        font-size: 0.95rem;
    }

    .chat-input:focus {
        border-color: #10b981;
    }

    .chat-submit {
        background-color: #10b981;
        color: white;
        border: none;
        border-radius: 24px;
        padding: 0.8rem 1.5rem;
        cursor: pointer;
        font-weight: 600;
        transition: background-color 0.2s;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .chat-submit:hover {
        background-color: #059669;
    }

    .chat-submit:disabled {
        background-color: #9ca3af;
        cursor: not-allowed;
    }
    
    .loading-dots:after {
        content: '.';
        animation: dots 1.5s steps(5, end) infinite;
    }

    @keyframes dots {
        0%, 20% { content: '.'; }
        40% { content: '..'; }
        60% { content: '...'; }
        80%, 100% { content: ''; }
    }
</style>

<div class="chat-container">
    <div id="chat-box" class="chat-box">
        <div class="chat-bubble chat-bubble-ai">
            <strong>🤖 AI WattCare</strong><br>
            Halo! Ceritakan penggunaan alat elektronik yang ingin Anda gunakan, dan saya akan bantu hitung estimasi konsumsinya.<br><br>
            <em>Contoh: "Saya pakai 2 lampu 30 watt selama 3 jam, dan 1 kulkas 160 watt selama 10 jam."</em>
        </div>
    </div>

    <form id="chat-form" class="chat-input-area">
        <input type="text" id="chat-input" class="chat-input" placeholder="Ketik pesan Anda di sini..." autocomplete="off" required>
        <button type="submit" id="chat-submit" class="chat-submit">
            Kirim 
            <svg style="width: 18px; height: 18px; margin-left: 6px;" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path></svg>
        </button>
    </form>
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

        // Tampilkan pesan user di kanan
        appendBubble(message, 'user');
        
        chatInput.value = '';
        chatInput.disabled = true;
        submitBtn.disabled = true;
        
        // Tampilkan loading bubble di kiri
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
            
            // Hapus loading bubble
            const loadingBubble = document.getElementById(loadingId);
            if (loadingBubble) loadingBubble.remove();

            if (response.ok) {
                // Tampilkan pesan balasan AI
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
        const bubble = document.createElement('div');
        bubble.className = `chat-bubble chat-bubble-${senderType}`;
        if (id) bubble.id = id;
        
        if (senderType === 'ai') {
            const title = `<strong>🤖 AI WattCare</strong><br>`;
            bubble.innerHTML = isHtml ? title + text : title + escapeHtml(text);
        } else {
            bubble.innerHTML = isHtml ? text : escapeHtml(text);
        }
        
        chatBox.appendChild(bubble);
        
        // Auto scroll ke bawah
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
