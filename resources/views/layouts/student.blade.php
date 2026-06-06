<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="COMSATS Hostel Management System - Student Portal">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Student - COMSATS Hostel')</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <link rel="stylesheet" href="{{ asset('css/premium.css') }}">
    <link rel="stylesheet" href="{{ asset('css/advanced.css') }}">
</head>
<body>
<div class="app">
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
            <a href="{{ route('student.dashboard') }}" class="nav-item {{ request()->routeIs('student.dashboard') ? 'active' : '' }}">
                <i class="fas fa-th-large"></i> Dashboard
            </a>
            <a href="{{ route('student.complaints.index') }}" class="nav-item {{ request()->routeIs('student.complaints.*') ? 'active' : '' }}">
                <i class="fas fa-exclamation-triangle"></i> My Complaints
            </a>
            <a href="{{ route('student.leaveRequests.index') }}" class="nav-item {{ request()->routeIs('student.leaveRequests.*') ? 'active' : '' }}">
                <i class="fas fa-calendar-minus"></i> Leave Requests
            </a>
            <a href="{{ route('student.events.index') }}" class="nav-item {{ request()->routeIs('student.events.*') ? 'active' : '' }}">
                <i class="fas fa-calendar-alt"></i> Events
            </a>
            <a href="{{ route('student.announcements.index') }}" class="nav-item {{ request()->routeIs('student.announcements.*') ? 'active' : '' }}">
                <i class="fas fa-bullhorn"></i> Announcements
            </a>
            <a href="{{ route('student.messMenu.index') }}" class="nav-item {{ request()->routeIs('student.messMenu.*') ? 'active' : '' }}">
                <i class="fas fa-utensils"></i> Mess Menu
            </a>
            <a href="{{ route('student.visitorPasses.index') }}" class="nav-item {{ request()->routeIs('student.visitorPasses.*') ? 'active' : '' }}">
                <i class="fas fa-id-card"></i> Visitor Passes
            </a>
            <a href="{{ route('student.maintenance.index') }}" class="nav-item {{ request()->routeIs('student.maintenance.*') ? 'active' : '' }}">
                <i class="fas fa-wrench"></i> Maintenance
            </a>
            <a href="{{ route('student.noticeBoard.index') }}" class="nav-item {{ request()->routeIs('student.noticeBoard.*') ? 'active' : '' }}">
                <i class="fas fa-comments"></i> Notice Board
            </a>
            <a href="{{ route('student.attendance.index') }}" class="nav-item premium-highlight {{ request()->routeIs('student.attendance.*') ? 'active' : '' }}">
                <i class="fas fa-clipboard-check"></i> Attendance
            </a>
            <a href="{{ route('student.fees.index') }}" class="nav-item {{ request()->routeIs('student.fees.*') ? 'active' : '' }}">
                <i class="fas fa-wallet"></i> My Fees
            </a>
            <a href="{{ route('student.chat.index') }}" class="nav-item premium-highlight {{ request()->routeIs('student.chat.*') ? 'active' : '' }}">
                <i class="fas fa-headset"></i> Live Chat
                @php $stuChatUnread = \App\Models\ChatMessage::where('receiver_id', auth()->id())->where('is_read', false)->count(); @endphp
                @if($stuChatUnread > 0)<span class="badge-count" style="position:relative;top:-1px;margin-left:auto;">{{ $stuChatUnread }}</span>@endif
            </a>
            <a href="{{ route('student.profile.edit') }}" class="nav-item {{ request()->routeIs('student.profile.*') ? 'active' : '' }}">
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

    <div class="main">
        <header class="topbar">
            <h1 class="page-title">@yield('page-title', 'Dashboard')</h1>
            <div class="topbar-right">
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
                            <form method="POST" action="{{ route('student.notifications.readAll') }}" style="display:inline;">
                                @csrf @method('PATCH')
                                <button type="submit">Mark all read</button>
                            </form>
                            @endif
                        </div>
                        <div class="dropdown-body">
                            @php $notifs = auth()->user()->notifications()->orderBy('created_at','desc')->limit(10)->get(); @endphp
                            @forelse($notifs as $n)
                                <div class="notif-item {{ $n->is_read ? '' : 'unread' }}" onclick="markNotifRead({{ $n->id }})">
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
                        <a href="{{ route('student.profile.edit') }}"><i class="fas fa-user"></i> Profile</a>
                        <a href="#" class="logout-link" onclick="event.preventDefault();document.getElementById('logout-form').submit();"><i class="fas fa-sign-out-alt"></i> Logout</a>
                    </div>
                </div>
            </div>
        </header>

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

<script>
function toggleTheme(){
    const body=document.body;const icon=document.getElementById('theme-icon');
    if(body.getAttribute('data-theme')==='dark'){body.removeAttribute('data-theme');icon.className='fas fa-moon';localStorage.setItem('theme','light');}
    else{body.setAttribute('data-theme','dark');icon.className='fas fa-sun';localStorage.setItem('theme','dark');}
}
(function(){if(localStorage.getItem('theme')==='dark'){document.body.setAttribute('data-theme','dark');const i=document.getElementById('theme-icon');if(i)i.className='fas fa-sun';}})();
function toggleNotifDropdown(){document.getElementById('notifDropdown').classList.toggle('show');document.getElementById('profileMenu').classList.remove('show');}
function toggleProfileMenu(){document.getElementById('profileMenu').classList.toggle('show');document.getElementById('notifDropdown').classList.remove('show');}
document.addEventListener('click',function(e){
    if(!e.target.closest('.notification-bell'))document.getElementById('notifDropdown').classList.remove('show');
    if(!e.target.closest('.profile-dropdown'))document.getElementById('profileMenu').classList.remove('show');
});
function markNotifRead(id){
    fetch('/student/notifications/'+id+'/read',{method:'PATCH',headers:{'X-CSRF-TOKEN':document.querySelector('meta[name=csrf-token]').content}}).then(()=>location.reload());
}
function openModal(id){document.getElementById(id).classList.add('show');}
function closeModal(id){document.getElementById(id).classList.remove('show');}
</script>
@yield('scripts')
</body>
</html>
