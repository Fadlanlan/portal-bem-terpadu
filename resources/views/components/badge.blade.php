@props(['type' => 'default'])

@php
    $classes = match($type) {
        'success', 'Selesai' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
        'warning', 'Berlangsung' => 'bg-amber-50 text-amber-800 border-amber-200',
        'info', 'Rencana' => 'bg-sky-50 text-sky-700 border-sky-200',
        'purple' => 'bg-purple-50 text-purple-700 border-purple-200',
        'danger' => 'bg-rose-50 text-rose-700 border-rose-200',
        default => 'bg-slate-100 text-slate-700 border-slate-200',
    };
@endphp

<span {{ $attributes->merge(['class' => "inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold border {$classes}"]) }}>
    {{ $slot }}
</span>
