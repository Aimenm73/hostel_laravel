@extends('layouts.admin')
@section('title', 'Live Chat - Admin')
@section('page-title', 'Live Chat')
@section('content')
<div class="chat-layout">
    {{-- Conversation List --}}
    <div class="chat-sidebar" id="chatSidebar">
        <div class="chat-sidebar-header">
            <h3><i class="fas fa-comments"></i> Conversations</h3>
            <span class="chat-count">{{ count($conversations) }}</span>
        </div>
        <div class="chat-search">
            <i class="fas fa-search"></i>
            <input type="text" placeholder="Search students..." id="chatSearch" oninput="filterChats()">
        </div>
        <div class="chat-list" id="chatList">
            @forelse($conversations as $student)
                <div class="chat-contact {{ $student->unread_count > 0 ? 'has-unread' : '' }}"
                     data-id="{{ $student->id }}"
                     data-name="{{ strtolower($student->name) }}"
                     onclick="openChat({{ $student->id }}, '{{ addslashes($student->name) }}')">
                    <div class="contact-avatar">
                        @if($student->profile_pic)
                            <img src="{{ asset('storage/profiles/' . $student->profile_pic) }}" alt="">
                        @else
                            {{ $student->initial }}
                        @endif
                        <span class="online-dot"></span>
                    </div>
                    <div class="contact-info">
                        <div class="contact-name">{{ $student->name }}</div>
                        <div class="contact-preview">{{ $student->last_message ? \Illuminate\Support\Str::limit($student->last_message->message, 35) : 'No messages yet' }}</div>
                    </div>
                    <div class="contact-meta">
                        <div class="contact-time">{{ $student->last_message ? $student->last_message->created_at->diffForHumans(null, true) : '' }}</div>
                        @if($student->unread_count > 0)
                            <span class="unread-badge">{{ $student->unread_count }}</span>
                        @endif
                    </div>
                </div>
            @empty
                <div class="chat-empty-sidebar">
                    <i class="fas fa-inbox"></i>
                    <p>No conversations yet</p>
                </div>
            @endforelse
        </div>
    </div>

    {{-- Chat Area --}}
    <div class="chat-main" id="chatMain">
        <div class="chat-placeholder" id="chatPlaceholder">
            <div class="chat-placeholder-content">
                <i class="fas fa-comments"></i>
                <h3>Select a conversation</h3>
                <p>Choose a student from the sidebar to start chatting</p>
            </div>
        </div>
        <div class="chat-active" id="chatActive" style="display:none;">
            <div class="chat-header" id="chatHeader">
                <div class="chat-header-user">
                    <div class="contact-avatar" id="chatAvatar">?</div>
                    <div>
                        <div class="chat-header-name" id="chatName">Student</div>
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
            <div class="chat-typing" id="typingIndicator" style="display:none;">
                <div class="typing-dots"><span></span><span></span><span></span></div>
                <span>typing...</span>
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

function filterChats() {
    const q = document.getElementById('chatSearch').value.toLowerCase();
    document.querySelectorAll('.chat-contact').forEach(c => {
        c.style.display = c.dataset.name.includes(q) ? '' : 'none';
    });
}

function openChat(studentId, name) {
    currentChatId = studentId;

    // Update UI
    document.querySelectorAll('.chat-contact').forEach(c => c.classList.remove('active'));
    document.querySelector(`.chat-contact[data-id="${studentId}"]`)?.classList.add('active');

    document.getElementById('chatPlaceholder').style.display = 'none';
    document.getElementById('chatActive').style.display = 'flex';
    document.getElementById('chatName').textContent = name;

    // Load messages
    loadMessages(studentId);

    // Start polling
    if (pollInterval) clearInterval(pollInterval);
    pollInterval = setInterval(() => loadMessages(studentId, true), 5000);
}

function loadMessages(studentId, silent = false) {
    if (!silent) {
        document.getElementById('chatMessages').innerHTML = '<div class="chat-loading"><i class="fas fa-spinner fa-spin"></i> Loading...</div>';
    }

    fetch(`/admin/chat/${studentId}/messages`, {
        headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content }
    })
    .then(r => r.json())
    .then(messages => {
        const container = document.getElementById('chatMessages');
        if (!silent || container.querySelector('.chat-loading')) {
            container.innerHTML = '';
        }

        const currentCount = container.children.length;
        if (silent && messages.length === currentCount) return;

        container.innerHTML = '';
        if (messages.length === 0) {
            container.innerHTML = '<div class="chat-empty-msg"><i class="fas fa-hand-peace"></i><p>Start the conversation!</p></div>';
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

        // Clear unread badge
        const contact = document.querySelector(`.chat-contact[data-id="${studentId}"]`);
        if (contact) {
            contact.classList.remove('has-unread');
            const badge = contact.querySelector('.unread-badge');
            if (badge) badge.remove();
        }
    });
}

function sendMessage(e) {
    e.preventDefault();
    const input = document.getElementById('chatInput');
    const msg = input.value.trim();
    if (!msg || !currentChatId) return;

    input.value = '';

    // Optimistic UI
    const container = document.getElementById('chatMessages');
    const emptyMsg = container.querySelector('.chat-empty-msg');
    if (emptyMsg) emptyMsg.remove();

    container.innerHTML += `
        <div class="chat-bubble mine sending">
            <div class="bubble-text">${escapeHtml(msg)}</div>
            <div class="bubble-meta"><span>now</span><i class="fas fa-clock"></i></div>
        </div>`;
    container.scrollTop = container.scrollHeight;

    fetch('/admin/chat/send', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content
        },
        body: JSON.stringify({ receiver_id: currentChatId, message: msg })
    })
    .then(r => r.json())
    .then(() => loadMessages(currentChatId, true));
}

function refreshMessages() {
    if (currentChatId) loadMessages(currentChatId);
}

function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}
</script>
@endsection
