<div class="page-hero">
    <div class="page-hero-glow"></div>
    <div class="page-hero-content">
        @if(!empty($icon))<div class="page-hero-icon"><i class="fas {{ $icon }}"></i></div>@endif
        <div>
            <h2>{{ $title }}</h2>
            @if(!empty($subtitle))<p>{{ $subtitle }}</p>@endif
        </div>
    </div>
    @if(!empty($actions)){!! $actions !!}@endif
</div>
