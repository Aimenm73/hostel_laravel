@extends('layouts.student')
@section('title', 'Live Chat - Student')
@section('page-title', 'Live Chat')
@section('content')
<div class="chat-layout">
    {{-- Admin List --}}
    <div class="chat-sidebar" id="chatSidebar">
        <div class="chat-sidebar-header">
            <h3><i class="fas fa-comments"></i> Admin Support</h3>
        </div>
        <div class="chat-list" id="chatList">
            @forelse($admins as $admin)
                <div class="chat-contact {{ $admin->unread_count > 0 ? 'has-unread' : '' }}"
                     data-id="{{ $admin->id }}"
                     data-name="{{ strtolower($admin->name) }}"
                     onclick="openChat({{ $admin->id }}, '{{ addslashes($admin->name) }}')">
                    <div class="contact-avatar admin-avatar">
                        @if($admin->profile_pic)
                            <img src="{{ asset('storage/profiles/' . $admin->profile_pic) }}" alt="">
                        @else
                            {{ $admin->initial }}
                        @endif
                        <span class="online-dot"></span>
                    </div>
                    <div class="contact-info">
                        <div class="contact-name">{{ $admin->name }} <span class="admin-badge">Admin</span></div>
                        <div class="contact-preview">{{ $admin->last_message ? \Illuminate\Support\Str::limit($admin->last_message->message, 35) : 'Start a conversation' }}</div>
                    </div>
                    <div class="contact-meta">
                        <div class="contact-time">{{ $admin->last_message ? $admin->last_message->created_at->diffForHumans(null, true) : '' }}</div>
                        @if($admin->unread_count > 0)
                            <span class="unread-badge">{{ $admin->unread_count }}</span>
                        @endif
                    </div>
                </div>
            @empty
                <div class="chat-empty-sidebar">
                    <i class="fas fa-user-shield"></i>
                    <p>No admins available</p>
                </div>
            @endforelse
        </div>
    </div>

    {{-- Chat Area --}}
    <div class="chat-main" id="chatMain">
        <div class="chat-placeholder" id="chatPlaceholder">
            <div class="chat-placeholder-content">
                <i class="fas fa-headset"></i>
                <h3>Need Help?</h3>
                <p>Select an admin from the sidebar to start chatting</p>
            </div>
        </div>
        <div class="chat-active" id="chatActive" style="display:none;">
            <div class="chat-header" id="chatHeader">
                <div class="chat-header-user">
                    <div class="contact-avatar" id="chatAvatar">A</div>
                    <div>
                        <div class="chat-header-name" id="chatName">Admin</div>
                        <div class="chat-header-status"><span class="status-dot"></span> Online</div>
                    </div>
                </div>
                <div class="chat-header-actions">
                    <button class="btn btn-sm btn-outline" onclick="refreshMessages()"><i class="fas fa-sync-alt"></i></button>
                </div>
            </div>
            <div class="chat-messages" id="chatMessages">
                <div class="chat-loading"><i class="fas fa-spinner fa-spin"></i> Loading messages...</div>
            </div>
            <div class="chat-input-area">
                <form id="chatForm" onsubmit="sendMessage(event)">
                    <div class="chat-input-wrap">
                        <input type="text" id="chatInput" placeholder="Type a message..." maxlength="1000" autocomplete="off">
                        <button type="submit" class="chat-send-btn" id="sendBtn">
                            <i class="fas fa-paper-plane"></i>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
@section('scripts')
<script>
let currentChatId = null;
let pollInterval = null;

function openChat(adminId, name) {
    currentChatId = adminId;
    document.querySelectorAll('.chat-contact').forEach(c => c.classList.remove('active'));
    document.querySelector(`.chat-contact[data-id="${adminId}"]`)?.classList.add('active');
    document.getElementById('chatPlaceholder').style.display = 'none';
    document.getElementById('chatActive').style.display = 'flex';
    document.getElementById('chatName').textContent = name;
    loadMessages(adminId);
    if (pollInterval) clearInterval(pollInterval);
    pollInterval = setInterval(() => loadMessages(adminId, true), 5000);
}

function loadMessages(adminId, silent = false) {
    if (!silent) {
        document.getElementById('chatMessages').innerHTML = '<div class="chat-loading"><i class="fas fa-spinner fa-spin"></i> Loading...</div>';
    }
    fetch(`/student/chat/${adminId}/messages`, {
        headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content }
    })
    .then(r => r.json())
    .then(messages => {
        const container = document.getElementById('chatMessages');
        const currentCount = container.querySelectorAll('.chat-bubble').length;
        if (silent && messages.length === currentCount) return;
        container.innerHTML = '';
        if (messages.length === 0) {
            container.innerHTML = '<div class="chat-empty-msg"><i class="fas fa-hand-peace"></i><p>Say hello!</p></div>';
            return;
        }
        let lastDate = '';
        messages.forEach(msg => {
            if (msg.date !== lastDate) {
                container.innerHTML += `<div class="chat-date-sep"><span>${msg.date}</span></div>`;
                lastDate = msg.date;
            }
            container.innerHTML += `
                <div class="chat-bubble ${msg.is_mine ? 'mine' : 'theirs'}">
                    <div class="bubble-text">${escapeHtml(msg.message)}</div>
                    <div class="bubble-meta">
                        <span>${msg.time}</span>
                        ${msg.is_mine ? (msg.is_read ? '<i class="fas fa-check-double read"></i>' : '<i class="fas fa-check"></i>') : ''}
                    </div>
                </div>`;
        });
        container.scrollTop = container.scrollHeight;
        const contact = document.querySelector(`.chat-contact[data-id="${adminId}"]`);
        if (contact) { contact.classList.remove('has-unread'); const b = contact.querySelector('.unread-badge'); if(b) b.remove(); }
    });
}

function sendMessage(e) {
    e.preventDefault();
    const input = document.getElementById('chatInput');
    const msg = input.value.trim();
    if (!msg || !currentChatId) return;
    input.value = '';
    const container = document.getElementById('chatMessages');
    const empty = container.querySelector('.chat-empty-msg');
    if (empty) empty.remove();
    container.innerHTML += `<div class="chat-bubble mine sending"><div class="bubble-text">${escapeHtml(msg)}</div><div class="bubble-meta"><span>now</span><i class="fas fa-clock"></i></div></div>`;
    container.scrollTop = container.scrollHeight;
    fetch('/student/chat/send', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content },
        body: JSON.stringify({ receiver_id: currentChatId, message: msg })
    }).then(r => r.json()).then(() => loadMessages(currentChatId, true));
}

function refreshMessages() { if (currentChatId) loadMessages(currentChatId); }
function escapeHtml(t) { const d = document.createElement('div'); d.textContent = t; return d.innerHTML; }
</script>
@endsection
