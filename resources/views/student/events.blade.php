@extends('layouts.student')
@section('title', 'Events')
@section('page-title', 'Events')
@section('content')

{{-- Filter Tabs --}}
<div class="filter-tabs" style="margin-bottom:24px;">
    <div class="filter-tab active" onclick="filterEvents('all', this)">All Events</div>
    <div class="filter-tab" onclick="filterEvents('upcoming', this)">Upcoming</div>
    <div class="filter-tab" onclick="filterEvents('ongoing', this)">Ongoing</div>
    <div class="filter-tab" onclick="filterEvents('completed', this)">Completed</div>
    <div class="filter-tab" onclick="filterEvents('registered', this)">My Registrations</div>
</div>

{{-- Event Cards Grid --}}
<div class="event-cards-grid" id="eventGrid">
    @forelse($events as $e)
        @php
            $isRegistered = in_array($e->id, $myRegistrations);
            $seatsLeft = $e->max_seats - $e->booked;
            $seatPercent = $e->max_seats > 0 ? round(($e->booked / $e->max_seats) * 100) : 0;
            $isFull = $seatsLeft <= 0;
            $eventDateTime = $e->date ? $e->date->format('Y-m-d') . 'T' . $e->time : '';
        @endphp
        <div class="event-card {{ $isRegistered ? 'registered' : '' }}" 
             data-status="{{ $e->status }}" 
             data-registered="{{ $isRegistered ? '1' : '0' }}"
             style="animation-delay: {{ $loop->index * 0.08 }}s;">
            
            {{-- Card Header with Status --}}
            <div class="event-card-header">
                <div class="event-card-badge">
                    <span class="badge badge-{{ $e->status }}">{{ ucfirst($e->status) }}</span>
                    @if($isRegistered)
                        <span class="badge badge-approved"><i class="fas fa-check-circle"></i> Registered</span>
                    @endif
                </div>
                <div class="event-card-icon">
                    @if($e->status === 'upcoming')
                        <i class="fas fa-rocket"></i>
                    @elseif($e->status === 'ongoing')
                        <i class="fas fa-play-circle"></i>
                    @elseif($e->status === 'completed')
                        <i class="fas fa-flag-checkered"></i>
                    @else
                        <i class="fas fa-ban"></i>
                    @endif
                </div>
            </div>

            {{-- Event Info --}}
            <div class="event-card-body">
                <h3 class="event-card-title">{{ $e->title }}</h3>
                @if($e->description)
                    <p class="event-card-desc">{{ Str::limit($e->description, 80) }}</p>
                @endif

                <div class="event-card-details">
                    <div class="event-detail">
                        <i class="fas fa-map-marker-alt"></i>
                        <span>{{ $e->venue ?? 'TBA' }}</span>
                    </div>
                    <div class="event-detail">
                        <i class="fas fa-calendar"></i>
                        <span>{{ $e->date ? $e->date->format('M d, Y') : 'TBA' }}</span>
                    </div>
                    <div class="event-detail">
                        <i class="fas fa-clock"></i>
                        <span>{{ $e->time ? \Carbon\Carbon::parse($e->time)->format('h:i A') : 'TBA' }}</span>
                    </div>
                </div>

                {{-- Seats Progress --}}
                <div class="event-seats">
                    <div class="event-seats-header">
                        <span><i class="fas fa-users"></i> Seats</span>
                        <span class="{{ $isFull ? 'seats-full' : '' }}">{{ $seatsLeft }} / {{ $e->max_seats }} left</span>
                    </div>
                    <div class="event-seats-bar">
                        <div class="event-seats-fill {{ $seatPercent >= 90 ? 'almost-full' : ($seatPercent >= 60 ? 'filling' : '') }}" 
                             style="width:{{ $seatPercent }}%"></div>
                    </div>
                </div>

                {{-- Countdown Timer (for upcoming events) --}}
                @if($e->status === 'upcoming' && $eventDateTime)
                    <div class="event-countdown" data-target="{{ $eventDateTime }}">
                        <div class="countdown-label"><i class="fas fa-hourglass-half"></i> Starts in</div>
                        <div class="countdown-boxes">
                            <div class="countdown-box"><span class="cd-days">--</span><small>Days</small></div>
                            <div class="countdown-sep">:</div>
                            <div class="countdown-box"><span class="cd-hours">--</span><small>Hrs</small></div>
                            <div class="countdown-sep">:</div>
                            <div class="countdown-box"><span class="cd-mins">--</span><small>Min</small></div>
                            <div class="countdown-sep">:</div>
                            <div class="countdown-box"><span class="cd-secs">--</span><small>Sec</small></div>
                        </div>
                    </div>
                @endif
            </div>

            {{-- Card Footer with Action --}}
            <div class="event-card-footer">
                @if($isRegistered)
                    <form method="POST" action="{{ route('student.events.cancel', $e->id) }}" class="event-form">
                        @csrf @method('DELETE')
                        <button type="submit" class="btn btn-danger event-btn">
                            <i class="fas fa-times-circle"></i> Cancel Registration
                        </button>
                    </form>
                @elseif($e->status === 'upcoming' && !$isFull)
                    <form method="POST" action="{{ route('student.events.register', $e->id) }}" class="event-form" 
                          onsubmit="triggerConfetti(this, event)">
                        @csrf
                        <button type="submit" class="btn btn-success event-btn pulse-btn">
                            <i class="fas fa-check-circle"></i> Register Now
                        </button>
                    </form>
                @elseif($isFull)
                    <button class="btn event-btn btn-disabled" disabled>
                        <i class="fas fa-lock"></i> Fully Booked
                    </button>
                @else
                    <button class="btn event-btn btn-disabled" disabled>
                        <i class="fas fa-info-circle"></i> {{ ucfirst($e->status) }}
                    </button>
                @endif
            </div>
        </div>
    @empty
        <div class="event-empty">
            <i class="fas fa-calendar-times"></i>
            <h3>No Events Yet</h3>
            <p>Check back later for upcoming events!</p>
        </div>
    @endforelse
</div>

<div class="pagination-wrapper" style="padding:16px 0;">{{ $events->links() }}</div>

{{-- Confetti Canvas --}}
<canvas id="confettiCanvas"></canvas>

@endsection

@section('scripts')
<script>
// ── Filter Events ──
function filterEvents(status, btn) {
    document.querySelectorAll('.filter-tab').forEach(t => t.classList.remove('active'));
    btn.classList.add('active');
    
    document.querySelectorAll('.event-card').forEach(card => {
        const cardStatus = card.dataset.status;
        const isRegistered = card.dataset.registered === '1';
        
        if (status === 'all') {
            card.style.display = '';
        } else if (status === 'registered') {
            card.style.display = isRegistered ? '' : 'none';
        } else {
            card.style.display = cardStatus === status ? '' : 'none';
        }
    });
}

// ── Countdown Timers ──
function initCountdowns() {
    document.querySelectorAll('.event-countdown').forEach(el => {
        const target = new Date(el.dataset.target).getTime();
        
        function tick() {
            const now = Date.now();
            const diff = target - now;
            
            if (diff <= 0) {
                el.querySelector('.countdown-label').innerHTML = '<i class="fas fa-check-circle"></i> Event has started!';
                el.querySelector('.countdown-boxes').style.display = 'none';
                return;
            }
            
            const days = Math.floor(diff / 86400000);
            const hours = Math.floor((diff % 86400000) / 3600000);
            const mins = Math.floor((diff % 3600000) / 60000);
            const secs = Math.floor((diff % 60000) / 1000);
            
            el.querySelector('.cd-days').textContent = String(days).padStart(2, '0');
            el.querySelector('.cd-hours').textContent = String(hours).padStart(2, '0');
            el.querySelector('.cd-mins').textContent = String(mins).padStart(2, '0');
            el.querySelector('.cd-secs').textContent = String(secs).padStart(2, '0');
            
            requestAnimationFrame(() => setTimeout(tick, 1000));
        }
        tick();
    });
}
initCountdowns();

// ── Confetti Effect ──
const canvas = document.getElementById('confettiCanvas');
const ctx = canvas.getContext('2d');
canvas.style.cssText = 'position:fixed;top:0;left:0;width:100%;height:100%;pointer-events:none;z-index:9999;';
let particles = [];
let animating = false;

function resizeCanvas() {
    canvas.width = window.innerWidth;
    canvas.height = window.innerHeight;
}
resizeCanvas();
window.addEventListener('resize', resizeCanvas);

class Particle {
    constructor(x, y) {
        this.x = x;
        this.y = y;
        this.vx = (Math.random() - 0.5) * 16;
        this.vy = Math.random() * -18 - 6;
        this.gravity = 0.45;
        this.drag = 0.98;
        this.size = Math.random() * 8 + 4;
        this.rotation = Math.random() * 360;
        this.rotSpeed = (Math.random() - 0.5) * 12;
        this.opacity = 1;
        this.color = ['#4361ee','#06d6a0','#ffd166','#ef476f','#7c9cff','#764ba2','#ff6b6b','#48dbfb'][Math.floor(Math.random()*8)];
        this.shape = Math.random() > 0.5 ? 'rect' : 'circle';
    }
    update() {
        this.vy += this.gravity;
        this.vx *= this.drag;
        this.x += this.vx;
        this.y += this.vy;
        this.rotation += this.rotSpeed;
        this.opacity -= 0.008;
    }
    draw() {
        ctx.save();
        ctx.translate(this.x, this.y);
        ctx.rotate((this.rotation * Math.PI) / 180);
        ctx.globalAlpha = Math.max(0, this.opacity);
        ctx.fillStyle = this.color;
        if (this.shape === 'rect') {
            ctx.fillRect(-this.size/2, -this.size/4, this.size, this.size/2);
        } else {
            ctx.beginPath();
            ctx.arc(0, 0, this.size/2, 0, Math.PI*2);
            ctx.fill();
        }
        ctx.restore();
    }
}

function animateConfetti() {
    if (!animating) return;
    ctx.clearRect(0, 0, canvas.width, canvas.height);
    particles = particles.filter(p => p.opacity > 0 && p.y < canvas.height + 50);
    particles.forEach(p => { p.update(); p.draw(); });
    if (particles.length === 0) { animating = false; return; }
    requestAnimationFrame(animateConfetti);
}

function launchConfetti(x, y) {
    for (let i = 0; i < 80; i++) {
        particles.push(new Particle(x, y));
    }
    if (!animating) { animating = true; animateConfetti(); }
}

function triggerConfetti(form, e) {
    e.preventDefault();
    const btn = form.querySelector('button');
    const rect = btn.getBoundingClientRect();
    const cx = rect.left + rect.width / 2;
    const cy = rect.top + rect.height / 2;
    
    // Visual feedback
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Registering...';
    btn.disabled = true;
    
    // Launch confetti from button position
    launchConfetti(cx, cy);
    // Additional bursts
    setTimeout(() => launchConfetti(cx - 60, cy - 20), 150);
    setTimeout(() => launchConfetti(cx + 60, cy - 20), 300);
    
    // Submit after confetti shows
    setTimeout(() => form.submit(), 900);
}

// ── If page loaded with success flash, show confetti ──
@if(session('success') && str_contains(session('success'), 'Registered'))
    setTimeout(() => {
        launchConfetti(window.innerWidth / 2, window.innerHeight / 2);
        setTimeout(() => launchConfetti(window.innerWidth / 3, window.innerHeight / 3), 200);
        setTimeout(() => launchConfetti(window.innerWidth * 2/3, window.innerHeight / 3), 400);
    }, 300);
@endif
</script>
@endsection
