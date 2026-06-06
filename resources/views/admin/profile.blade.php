@extends('layouts.admin')
@section('title', 'Profile - Admin')
@section('page-title', 'Profile')
@section('content')
<div style="display:grid;grid-template-columns:1fr 1fr;gap:24px;">
    <div class="card">
        <div class="card-header"><h3>Profile Information</h3></div>
        <div class="card-body">
            {{-- Profile Photo Upload with Live Preview --}}
            <div class="photo-upload-section">
                <form method="POST" action="{{ route('admin.profile.photo') }}" enctype="multipart/form-data" id="photoForm">
                    @csrf
                    <div class="photo-upload-wrapper" id="photoWrapper">
                        <div class="photo-preview" id="photoPreview">
                            @if(auth()->user()->profile_pic)
                                <img src="{{ asset('storage/profiles/' . auth()->user()->profile_pic) }}" alt="Profile" id="previewImg">
                            @else
                                <span class="photo-initial" id="previewInitial">{{ auth()->user()->initial }}</span>
                            @endif
                        </div>
                        <div class="photo-overlay" id="photoOverlay">
                            <i class="fas fa-camera"></i>
                            <span>Change Photo</span>
                        </div>
                        <input type="file" name="profile_pic" id="photoInput" accept="image/jpeg,image/png,image/gif,image/webp" class="photo-input-hidden">
                    </div>
                    <div class="photo-info">
                        <h3>{{ auth()->user()->name }}</h3>
                        <p>{{ auth()->user()->email }}</p>
                        <p class="photo-hint"><i class="fas fa-info-circle"></i> Click photo to change. Max 5MB.</p>
                    </div>
                    <div class="photo-actions" id="photoActions" style="display:none;">
                        <button type="submit" class="btn btn-primary btn-sm"><i class="fas fa-check"></i> Save Photo</button>
                        <button type="button" class="btn btn-outline btn-sm" onclick="cancelPhotoChange()"><i class="fas fa-times"></i> Cancel</button>
                    </div>
                </form>
            </div>

            <hr style="border:none;border-top:1px solid var(--border);margin:24px 0;">

            <form method="POST" action="{{ route('admin.profile.update') }}">
                @csrf @method('PATCH')
                <div class="form-group"><label>Name</label><input type="text" name="name" class="form-control" value="{{ auth()->user()->name }}" required></div>
                <div class="form-group"><label>Phone</label><input type="text" name="phone" class="form-control" value="{{ auth()->user()->phone }}"></div>
                <button type="submit" class="btn btn-primary">Update Profile</button>
            </form>
        </div>
    </div>
    <div>
        <div class="card" style="margin-bottom:24px;">
            <div class="card-header"><h3>Change Password</h3></div>
            <div class="card-body">
                <form method="POST" action="{{ route('admin.profile.password') }}">
                    @csrf @method('PATCH')
                    <div class="form-group"><label>Current Password</label><input type="password" name="current_password" class="form-control" required></div>
                    <div class="form-group"><label>New Password</label><input type="password" name="new_password" class="form-control" required></div>
                    <div class="form-group"><label>Confirm New Password</label><input type="password" name="new_password_confirmation" class="form-control" required></div>
                    <button type="submit" class="btn btn-primary">Change Password</button>
                </form>
            </div>
        </div>
        <div class="card">
            <div class="card-header"><h3>Account Info</h3></div>
            <div class="card-body">
                <div class="account-info-row"><span class="label">Role</span><span class="badge badge-general">{{ ucfirst(auth()->user()->role) }}</span></div>
                <div class="account-info-row"><span class="label">Email</span><span>{{ auth()->user()->email }}</span></div>
                <div class="account-info-row"><span class="label">Member Since</span><span>{{ auth()->user()->created_at ? auth()->user()->created_at->format('M d, Y') : 'N/A' }}</span></div>
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
