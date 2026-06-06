<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="COMSATS Hostel Management System - Admin Panel">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin - COMSATS Hostel')</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <link rel="stylesheet" href="{{ asset('css/premium.css') }}">
    <link rel="stylesheet" href="{{ asset('css/advanced.css') }}">
</head>
<body>
<div class="app">
    {{-- SIDEBAR --}}
    <aside class="sidebar">
        <div class="sidebar-header">
            <h2><i class="fas fa-building"></i> COMSATS Hostel</h2>
            <span>Management System</span>
        </div>
        <div class="sidebar-user">
            <div class="avatar">
                @if(auth()->user()->profile_pic)
                    <img src="{{ asset('storage/profiles/' . auth()->user()->profile_pic) }}" alt="Avatar">
                @else
                    {{ auth()->user()->initial }}
                @endif
            </div>
            <div class="user-info">
                <div class="name">{{ auth()->user()->name }}</div>
                <div class="role">{{ auth()->user()->role }}</div>
            </div>
        </div>
        <nav class="sidebar-nav">
            <a href="{{ route('admin.dashboard') }}" class="nav-item {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                <i class="fas fa-th-large"></i> Dashboard
            </a>
            <a href="{{ route('admin.complaints.index') }}" class="nav-item {{ request()->routeIs('admin.complaints.*') ? 'active' : '' }}">
                <i class="fas fa-exclamation-triangle"></i> Complaints
            </a>
            <a href="{{ route('admin.students.index') }}" class="nav-item {{ request()->routeIs('admin.students.*') ? 'active' : '' }}">
                <i class="fas fa-users"></i> Students
            </a>
            <a href="{{ route('admin.rooms.index') }}" class="nav-item {{ request()->routeIs('admin.rooms.*') ? 'active' : '' }}">
                <i class="fas fa-door-open"></i> Rooms
            </a>
            <a href="{{ route('admin.leaveRequests.index') }}" class="nav-item {{ request()->routeIs('admin.leaveRequests.*') ? 'active' : '' }}">
                <i class="fas fa-calendar-minus"></i> Leave Requests
            </a>
            <a href="{{ route('admin.events.index') }}" class="nav-item {{ request()->routeIs('admin.events.*') ? 'active' : '' }}">
                <i class="fas fa-calendar-alt"></i> Events
            </a>
            <a href="{{ route('admin.payments.index') }}" class="nav-item {{ request()->routeIs('admin.payments.*') ? 'active' : '' }}">
                <i class="fas fa-credit-card"></i> Payments
            </a>
            <a href="{{ route('admin.announcements.index') }}" class="nav-item {{ request()->routeIs('admin.announcements.*') ? 'active' : '' }}">
                <i class="fas fa-bullhorn"></i> Announcements
            </a>
            <a href="{{ route('admin.messMenu.index') }}" class="nav-item {{ request()->routeIs('admin.messMenu.*') ? 'active' : '' }}">
                <i class="fas fa-utensils"></i> Mess Menu
            </a>
            <a href="{{ route('admin.visitorPasses.index') }}" class="nav-item {{ request()->routeIs('admin.visitorPasses.*') ? 'active' : '' }}">
                <i class="fas fa-id-card"></i> Visitor Passes
            </a>
            <a href="{{ route('admin.maintenance.index') }}" class="nav-item {{ request()->routeIs('admin.maintenance.*') ? 'active' : '' }}">
                <i class="fas fa-calendar-alt"></i> Maintenance
            </a>
            <a href="{{ route('admin.noticeBoard.index') }}" class="nav-item {{ request()->routeIs('admin.noticeBoard.*') ? 'active' : '' }}">
                <i class="fas fa-thumbtack"></i> Notice Board
            </a>
            <a href="{{ route('admin.rollCall.index') }}" class="nav-item premium-highlight {{ request()->routeIs('admin.rollCall.*') ? 'active' : '' }}">
                <i class="fas fa-clipboard-check"></i> Roll Call
            </a>
            <a href="{{ route('admin.fees.index') }}" class="nav-item {{ request()->routeIs('admin.fees.*') ? 'active' : '' }}">
                <i class="fas fa-wallet"></i> Fee Ledger
            </a>
            <a href="{{ route('admin.messFeedback.index') }}" class="nav-item {{ request()->routeIs('admin.messFeedback.*') ? 'active' : '' }}">
                <i class="fas fa-star"></i> Mess Ratings
            </a>
            <a href="{{ route('admin.chat.index') }}" class="nav-item premium-highlight {{ request()->routeIs('admin.chat.*') ? 'active' : '' }}">
                <i class="fas fa-comments"></i> Live Chat
                @php $chatUnread = \App\Models\ChatMessage::where('receiver_id', auth()->id())->where('is_read', false)->count(); @endphp
                @if($chatUnread > 0)<span class="badge-count" style="position:relative;top:-1px;margin-left:auto;">{{ $chatUnread }}</span>@endif
            </a>
            <a href="{{ route('admin.roomHeatmap.index') }}" class="nav-item {{ request()->routeIs('admin.roomHeatmap.*') ? 'active' : '' }}">
                <i class="fas fa-fire"></i> Room Heat Map
            </a>
            <a href="{{ route('admin.reports.index') }}" class="nav-item {{ request()->routeIs('admin.reports.*') ? 'active' : '' }}">
                <i class="fas fa-file-export"></i> Reports
            </a>
            <a href="{{ route('admin.profile.edit') }}" class="nav-item {{ request()->routeIs('admin.profile.*') ? 'active' : '' }}">
                <i class="fas fa-user"></i> Profile
            </a>
            <form method="POST" action="{{ route('logout') }}" id="logout-form">
                @csrf
                <a href="#" class="nav-item logout" onclick="event.preventDefault();document.getElementById('logout-form').submit();">
                    <i class="fas fa-sign-out-alt"></i> Logout
                </a>
            </form>
        </nav>
    </aside>

    {{-- MAIN --}}
    <div class="main">
        {{-- TOPBAR --}}
        <header class="topbar">
            <h1 class="page-title">@yield('page-title', 'Dashboard')</h1>
            <div class="topbar-right">
                <form method="GET" action="{{ route('admin.search') }}" class="topbar-search">
                    <i class="fas fa-search"></i>
                    <input type="text" name="q" placeholder="Search..." aria-label="Search">
                </form>
                <button class="theme-toggle" onclick="toggleTheme()"><i class="fas fa-moon" id="theme-icon"></i></button>

                @php $unreadCount = auth()->user()->unreadNotificationsCount(); @endphp
                <div class="notification-bell" id="notifBell">
                    <button class="bell-btn" onclick="toggleNotifDropdown()">
                        <i class="fas fa-bell"></i>
                        @if($unreadCount > 0)<span class="badge-count">{{ $unreadCount }}</span>@endif
                    </button>
                    <div class="notification-dropdown" id="notifDropdown">
                        <div class="dropdown-header">
                            <span>Notifications</span>
                            @if($unreadCount > 0)
                            <form method="POST" action="{{ route('admin.notifications.readAll') }}" style="display:inline;">
                                @csrf @method('PATCH')
                                <button type="submit">Mark all read</button>
                            </form>
                            @endif
                        </div>
                        <div class="dropdown-body">
                            @php $notifs = auth()->user()->notifications()->orderBy('created_at','desc')->limit(10)->get(); @endphp
                            @forelse($notifs as $n)
                                <div class="notif-item {{ $n->is_read ? '' : 'unread' }}">
                                    <div class="notif-msg">{{ $n->message }}</div>
                                    <div class="notif-time">{{ $n->created_at ? $n->created_at->diffForHumans() : '' }}</div>
                                </div>
                            @empty
                                <div class="notif-empty">No notifications</div>
                            @endforelse
                        </div>
                    </div>
                </div>

                <div class="profile-dropdown">
                    <button class="profile-btn" onclick="toggleProfileMenu()">
                        <div class="avatar-sm">
                            @if(auth()->user()->profile_pic)
                                <img src="{{ asset('storage/profiles/' . auth()->user()->profile_pic) }}" alt="">
                            @else
                                {{ auth()->user()->initial }}
                            @endif
                        </div>
                        {{ auth()->user()->name }}
                        <i class="fas fa-chevron-down" style="font-size:10px;"></i>
                    </button>
                    <div class="profile-menu" id="profileMenu">
                        <a href="{{ route('admin.profile.edit') }}"><i class="fas fa-user"></i> Profile</a>
                        <a href="#" class="logout-link" onclick="event.preventDefault();document.getElementById('logout-form').submit();"><i class="fas fa-sign-out-alt"></i> Logout</a>
                    </div>
                </div>
            </div>
        </header>

        {{-- FLASH MESSAGES --}}
        <div class="content">
            @if(session('success'))
                <div class="alert alert-success"><i class="fas fa-check-circle"></i>{{ session('success') }}<button class="alert-close" onclick="this.parentElement.remove()">&times;</button></div>
            @endif
            @if(session('error'))
                <div class="alert alert-error"><i class="fas fa-exclamation-circle"></i>{{ session('error') }}<button class="alert-close" onclick="this.parentElement.remove()">&times;</button></div>
            @endif
            @if($errors->any())
                <div class="alert alert-error"><i class="fas fa-exclamation-circle"></i>{{ $errors->first() }}<button class="alert-close" onclick="this.parentElement.remove()">&times;</button></div>
            @endif

            @yield('content')
        </div>
    </div>
</div>

<div class="toast-container" id="toastContainer"></div>

<script>
function toggleTheme(){
    const body=document.body;
    const icon=document.getElementById('theme-icon');
    if(body.getAttribute('data-theme')==='dark'){
        body.removeAttribute('data-theme');
        icon.className='fas fa-moon';
        localStorage.setItem('theme','light');
    } else {
        body.setAttribute('data-theme','dark');
        icon.className='fas fa-sun';
        localStorage.setItem('theme','dark');
    }
}
(function(){
    if(localStorage.getItem('theme')==='dark'){
        document.body.setAttribute('data-theme','dark');
        const i=document.getElementById('theme-icon');if(i)i.className='fas fa-sun';
    }
})();

function toggleNotifDropdown(){
    document.getElementById('notifDropdown').classList.toggle('show');
    document.getElementById('profileMenu').classList.remove('show');
}
function toggleProfileMenu(){
    document.getElementById('profileMenu').classList.toggle('show');
    document.getElementById('notifDropdown').classList.remove('show');
}
document.addEventListener('click',function(e){
    if(!e.target.closest('.notification-bell'))document.getElementById('notifDropdown').classList.remove('show');
    if(!e.target.closest('.profile-dropdown'))document.getElementById('profileMenu').classList.remove('show');
});
function showToast(msg,type='success'){
    const c=document.getElementById('toastContainer');
    const t=document.createElement('div');
    t.className='toast '+type;
    t.innerHTML='<i class="fas fa-'+(type==='success'?'check-circle':'exclamation-circle')+'"></i><span>'+msg+'</span>';
    c.appendChild(t);
    setTimeout(()=>t.remove(),3000);
}
function openModal(id){document.getElementById(id).classList.add('show');}
function closeModal(id){document.getElementById(id).classList.remove('show');}
</script>
@yield('scripts')
</body>
</html>
