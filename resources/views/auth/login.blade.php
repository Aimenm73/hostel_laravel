@extends('layouts.guest')
@section('title', 'Login - COMSATS Hostel')
@section('content')
<div class="login-page">
    <div class="login-container">
        <div class="login-left">
            <div class="logo"><i class="fas fa-building"></i> COMSATS Hostel</div>
            <div class="tagline">Smart Living, Better Campus Life</div>
            <p class="desc">Manage your hostel experience efficiently with our comprehensive management system. From room assignments to event registrations, everything at your fingertips.</p>
            <ul class="features">
                <li><i class="fas fa-check-circle"></i> Email OTP Two-Factor Authentication</li>
                <li><i class="fas fa-check-circle"></i> Student ↔ Admin Live Chat System</li>
                <li><i class="fas fa-check-circle"></i> Interactive Room Occupancy Heat Map</li>
                <li><i class="fas fa-check-circle"></i> Online Payment Gateway & PDF Receipts</li>
                <li><i class="fas fa-check-circle"></i> Real-Time Dashboard with Live Analytics</li>
                <li><i class="fas fa-check-circle"></i> QR Night Roll Call & CSV/PDF Reports</li>
            </ul>
        </div>
        <div class="login-right">
            <div class="auth-tabs">
                <div class="auth-tab active" onclick="switchTab('login')" id="tab-login">Login</div>
                <div class="auth-tab" onclick="switchTab('register')" id="tab-register">Register</div>
            </div>

            @if(session('success'))
                <div class="alert alert-success"><i class="fas fa-check-circle"></i>{{ session('success') }}<button class="alert-close" onclick="this.parentElement.remove()">&times;</button></div>
            @endif
            @if(session('error'))
                <div class="alert alert-error"><i class="fas fa-exclamation-circle"></i>{{ session('error') }}<button class="alert-close" onclick="this.parentElement.remove()">&times;</button></div>
            @endif
            @if($errors->any())
                <div class="alert alert-error"><i class="fas fa-exclamation-circle"></i>{{ $errors->first() }}<button class="alert-close" onclick="this.parentElement.remove()">&times;</button></div>
            @endif

            <form class="auth-form active" id="form-login" method="POST" action="{{ route('login') }}">
                @csrf
                <div class="form-group">
                    <label>Email Address</label>
                    <input type="email" name="email" class="form-control" placeholder="Enter your email" value="{{ old('email') }}" required>
                </div>
                <div class="form-group">
                    <label>Password</label>
                    <input type="password" name="password" class="form-control" placeholder="Enter your password" required>
                </div>
                <button type="submit" class="btn btn-primary">Sign In</button>
            </form>

            <form class="auth-form" id="form-register" method="POST" action="{{ url('/register') }}">
                @csrf
                <div class="form-group">
                    <label>Full Name</label>
                    <input type="text" name="name" class="form-control" placeholder="Enter your full name" required>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label>Email Address</label>
                        <input type="email" name="email" class="form-control" placeholder="Email" required>
                    </div>
                    <div class="form-group">
                        <label>Phone</label>
                        <input type="text" name="phone" class="form-control" placeholder="Phone number">
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label>Roll Number</label>
                        <input type="text" name="roll_no" class="form-control" placeholder="e.g. SP24-BCS-062" required>
                    </div>
                    <div class="form-group">
                        <label>Department</label>
                        <input type="text" name="department" class="form-control" placeholder="e.g. Computer Science" required>
                    </div>
                </div>
                <div class="form-group">
                    <label>Year</label>
                    <select name="year" class="form-control" required>
                        <option value="1">1st Year</option>
                        <option value="2">2nd Year</option>
                        <option value="3">3rd Year</option>
                        <option value="4">4th Year</option>
                    </select>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label>Password</label>
                        <input type="password" name="password" class="form-control" placeholder="Min 6 characters" required>
                    </div>
                    <div class="form-group">
                        <label>Confirm Password</label>
                        <input type="password" name="password_confirmation" class="form-control" placeholder="Confirm password" required>
                    </div>
                </div>
                <button type="submit" class="btn btn-primary">Register</button>
            </form>

            <div class="demo-section">
                <p>Quick Demo Access</p>
                <div class="demo-chips">
                    <div class="demo-chip" onclick="fillDemo('admin@comsats.edu','123456')">Admin Demo</div>
                    <div class="demo-chip" onclick="fillDemo('rufah@student.com','123456')">Student Demo</div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
@section('scripts')
<script>
function switchTab(tab){
    document.querySelectorAll('.auth-tab').forEach(t=>t.classList.remove('active'));
    document.querySelectorAll('.auth-form').forEach(f=>f.classList.remove('active'));
    document.getElementById('tab-'+tab).classList.add('active');
    document.getElementById('form-'+tab).classList.add('active');
}
function fillDemo(email,pass){
    switchTab('login');
    document.querySelector('#form-login input[name=email]').value=email;
    document.querySelector('#form-login input[name=password]').value=pass;
}
(function(){if(localStorage.getItem('theme')==='dark'){document.body.setAttribute('data-theme','dark');}})();
</script>
@endsection
