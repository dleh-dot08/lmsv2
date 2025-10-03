@props(['icon' => 'bx-home', 'color' => 'primary', 'title' => '', 'value' => ''])

<div class="card">
    <div class="card-body">
        <div class="d-flex align-items-start justify-content-between">
            <div class="avatar flex-shrink-0">
                <i class="bx {{ $icon }} text-{{ $color }} fs-3"></i>
            </div>
        </div>
        <span class="d-block mb-1">{{ $title }}</span>
        <h3 class="card-title mb-2">{{ $value }}</h3>
    </div>
</div>