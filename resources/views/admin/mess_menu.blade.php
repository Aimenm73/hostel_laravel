@extends('layouts.admin')
@section('title', 'Mess Menu - Admin')
@section('page-title', 'Mess Menu')
@section('content')
<p style="color:var(--text-light);margin-bottom:24px;font-size:14px;">Manage weekly dining schedule. Students see today highlighted on their portal.</p>

<div class="mess-week-grid">
    @foreach($menus as $menu)
        <div class="mess-day-card">
            <div class="mess-day-header">
                <h3>{{ $menu->day_label }}</h3>
            </div>
            <form method="POST" action="{{ route('admin.messMenu.update', $menu) }}">
                @csrf @method('PUT')
                <div class="mess-meal">
                    <label><i class="fas fa-sun"></i> Breakfast</label>
                    <textarea name="breakfast" class="form-control" rows="2" placeholder="e.g. Eggs, paratha, tea">{{ $menu->breakfast }}</textarea>
                </div>
                <div class="mess-meal">
                    <label><i class="fas fa-cloud-sun"></i> Lunch</label>
                    <textarea name="lunch" class="form-control" rows="2" placeholder="e.g. Biryani, salad">{{ $menu->lunch }}</textarea>
                </div>
                <div class="mess-meal">
                    <label><i class="fas fa-moon"></i> Dinner</label>
                    <textarea name="dinner" class="form-control" rows="2" placeholder="e.g. Daal, roti, vegetables">{{ $menu->dinner }}</textarea>
                </div>
                <button type="submit" class="btn btn-primary btn-sm" style="width:100%;margin-top:8px;"><i class="fas fa-save"></i> Save Day</button>
            </form>
        </div>
    @endforeach
</div>
@endsection
