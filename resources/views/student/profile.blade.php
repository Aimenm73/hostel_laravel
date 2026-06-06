@extends('layouts.student')
@section('title', 'Profile')
@section('page-title', 'Profile')
@section('content')
<div class="hostel-id-card-wrap">
    <div class="hostel-id-card" id="hostelIdCard">
        <div class="id-card-front">
            <div class="id-card-brand"><i class="fas fa-building"></i> COMSATS Hostel</div>
            <div class="id-card-photo">
                @if($user->profile_pic)
                    <img src="{{ asset('storage/profiles/' . $user->profile_pic) }}" alt="">
                @else
                    <span>{{ $user->initial }}</span>
                @endif
            </div>
            <div class="id-card-name">{{ $user->name }}</div>
            <div class="id-card-meta">{{ $user->studentDetail->roll_no ?? 'N/A' }} · {{ $user->studentDetail->department ?? '' }}</div>
            <div class="id-card-room">Room {{ $user->studentDetail->room->number ?? '—' }}</div>
        </div>
        <div class="id-card-back">
            <img src="https://api.qrserver.com/v1/create-qr-code/?size=140x140&data={{ urlencode($idCardData) }}" alt="QR Code" width="120" height="120">
            <p>Scan at hostel gate</p>
            <small>Valid while enrolled · ID #{{ str_pad($user->id, 5, '0', STR_PAD_LEFT) }}</small>
        </div>
    </div>
    <button type="button" class="btn btn-outline btn-sm id-card-flip-btn" onclick="document.getElementById('hostelIdCard').classList.toggle('flipped')">
        <i class="fas fa-sync-alt"></i> Flip Card
    </button>
</div>

<div style="display:grid;grid-template-columns:1fr 1fr;gap:24px;">
    <div class="card">
        <div class="card-header"><h3>Profile Information</h3></div>
        <div class="card-body">
            {{-- Profile Photo Upload with Live Preview --}}
            <div class="photo-upload-section">
                <form method="POST" action="{{ route('student.profile.photo') }}" enctype="multipart/form-data" id="photoForm">
                    @csrf
                    <div class="photo-upload-wrapper" id="photoWrapper">
                        <div class="photo-preview" id="photoPreview">
                            @if($user->profile_pic)
                                <img src="{{ asset('storage/profiles/' . $user->profile_pic) }}" alt="Profile" id="previewImg">
                            @else
                                <span class="photo-initial" id="previewInitial">{{ $user->initial }}</span>
                            @endif
                        </div>
                        <div class="photo-overlay" id="photoOverlay">
                            <i class="fas fa-camera"></i>
                            <span>Change Photo</span>
                        </div>
                        <input type="file" name="profile_pic" id="photoInput" accept="image/jpeg,image/png,image/gif,image/webp" class="photo-input-hidden">
                    </div>
                    <div class="photo-info">
                        <h3>{{ $user->name }}</h3>
                        <p>{{ $user->email }}</p>
                        @if($user->studentDetail)
                            <p class="photo-meta">{{ $user->studentDetail->roll_no }} · {{ $user->studentDetail->department }} · Year {{ $user->studentDetail->year }}</p>
                            <p class="photo-meta">Room: {{ $user->studentDetail->room ? $user->studentDetail->room->number : 'Not Assigned' }}</p>
                        @endif
                        <p class="photo-hint"><i class="fas fa-info-circle"></i> Click photo to change. Max 5MB.</p>
                    </div>
                    <div class="photo-actions" id="photoActions" style="display:none;">
                        <button type="submit" class="btn btn-primary btn-sm"><i class="fas fa-check"></i> Save Photo</button>
                        <button type="button" class="btn btn-outline btn-sm" onclick="cancelPhotoChange()"><i class="fas fa-times"></i> Cancel</button>
                    </div>
                </form>
            </div>

            <hr style="border:none;border-top:1px solid var(--border);margin:24px 0;">

            <form method="POST" action="{{ route('student.profile.update') }}">
                @csrf @method('PATCH')
                <div class="form-group"><label>Name</label><input type="text" name="name" class="form-control" value="{{ $user->name }}" required></div>
                <div class="form-group"><label>Phone</label><input type="text" name="phone" class="form-control" value="{{ $user->phone }}"></div>
                <div class="form-row">
                    <div class="form-group"><label>Roll No</label><input type="text" name="roll_no" class="form-control" value="{{ $user->studentDetail->roll_no ?? '' }}"></div>
                    <div class="form-group"><label>Department</label><input type="text" name="department" class="form-control" value="{{ $user->studentDetail->department ?? '' }}"></div>
                </div>
                <div class="form-group"><label>Year</label><select name="year" class="form-control"><option value="1" {{ ($user->studentDetail->year ?? 1)==1?'selected':'' }}>1</option><option value="2" {{ ($user->studentDetail->year ?? 1)==2?'selected':'' }}>2</option><option value="3" {{ ($user->studentDetail->year ?? 1)==3?'selected':'' }}>3</option><option value="4" {{ ($user->studentDetail->year ?? 1)==4?'selected':'' }}>4</option></select></div>
                <button type="submit" class="btn btn-primary">Update Profile</button>
            </form>
        </div>
    </div>
    <div>
        <div class="card" style="margin-bottom:24px;">
            <div class="card-header"><h3>Change Password</h3></div>
            <div class="card-body">
                <form method="POST" action="{{ route('student.profile.password') }}">
                    @csrf @method('PATCH')
                    <div class="form-group"><label>Current Password</label><input type="password" name="current_password" class="form-control" required></div>
                    <div class="form-group"><label>New Password</label><input type="password" name="new_password" class="form-control" required></div>
                    <div class="form-group"><label>Confirm</label><input type="password" name="new_password_confirmation" class="form-control" required></div>
                    <button type="submit" class="btn btn-primary">Change Password</button>
                </form>
            </div>
        </div>
        <div class="card">
            <div class="card-header"><h3>Account Info</h3></div>
            <div class="card-body">
                <div class="account-info-row"><span class="label">Role</span><span class="badge badge-general">{{ ucfirst($user->role) }}</span></div>
                <div class="account-info-row"><span class="label">Email</span><span>{{ $user->email }}</span></div>
                <div class="account-info-row"><span class="label">Member Since</span><span>{{ $user->created_at ? $user->created_at->format('M d, Y') : 'N/A' }}</span></div>
                @if($user->studentDetail && $user->studentDetail->room)
                    <div class="account-info-row"><span class="label">Room</span><span>{{ $user->studentDetail->room->number }} ({{ ucfirst($user->studentDetail->room->type) }})</span></div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
(function() {
    const input = document.getElementById('photoInput');
    const wrapper = document.getElementById('photoWrapper');
    const preview = document.getElementById('photoPreview');
    const actions = document.getElementById('photoActions');
    let originalHTML = preview.innerHTML;

    // Click on wrapper triggers file input
    wrapper.addEventListener('click', function() {
        input.click();
    });

    // Live preview on file select
    input.addEventListener('change', function() {
        const file = this.files[0];
        if (!file) return;

        // Validate client-side
        if (!file.type.match(/^image\/(jpeg|png|gif|webp)$/)) {
            alert('Please select a valid image (JPEG, PNG, GIF, or WebP).');
            this.value = '';
            return;
        }
        if (file.size > 5 * 1024 * 1024) {
            alert('File size must be less than 5MB.');
            this.value = '';
            return;
        }

        const reader = new FileReader();
        reader.onload = function(e) {
            preview.innerHTML = '<img src="' + e.target.result + '" alt="Preview" id="previewImg">';
            preview.classList.add('has-new-photo');
            actions.style.display = 'flex';
        };
        reader.readAsDataURL(file);
    });

    window.cancelPhotoChange = function() {
        input.value = '';
        preview.innerHTML = originalHTML;
        preview.classList.remove('has-new-photo');
        actions.style.display = 'none';
    };
})();
</script>
@endsection
