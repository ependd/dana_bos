@props(['title', 'value', 'description' => null, 'icon' => null, 'color' => 'blue'])

@php
    $colorClasses = match($color) {
        'emerald', 'success' => 'bg-emerald-50 text-emerald-600 border-emerald-200 dark:bg-emerald-950/40 dark:text-emerald-400 dark:border-emerald-800',
        'rose', 'danger' => 'bg-rose-50 text-rose-600 border-rose-200 dark:bg-rose-950/40 dark:text-rose-400 dark:border-rose-800',
        'amber', 'warning' => 'bg-amber-50 text-amber-600 border-amber-200 dark:bg-amber-950/40 dark:text-amber-400 dark:border-amber-800',
        'sky', 'info' => 'bg-sky-50 text-sky-600 border-sky-200 dark:bg-sky-950/40 dark:text-sky-400 dark:border-sky-800',
        'purple' => 'bg-purple-50 text-purple-600 border-purple-200 dark:bg-purple-950/40 dark:text-purple-400 dark:border-purple-800',
        default => 'bg-blue-50 text-blue-600 border-blue-200 dark:bg-blue-950/40 dark:text-blue-400 dark:border-blue-800',
    };
@endphp

<div {{ $attributes->merge(['class' => 'bg-white dark:bg-zinc-900 rounded-xl border border-gray-200 dark:border-zinc-800 p-6 shadow-sm flex items-center justify-between']) }}>
    <div>
        <p class="text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-zinc-400">{{ $title }}</p>
        <p class="text-2xl font-bold text-gray-900 dark:text-white mt-1">{{ $value }}</p>
        @if($description)
        <p class="text-xs text-gray-500 dark:text-zinc-400 mt-1">{{ $description }}</p>
        @endif
    </div>
    @if($icon)
    <div class="w-12 h-12 rounded-xl border flex items-center justify-center shrink-0 {{ $colorClasses }}">
        {{ $icon }}
    </div>
    @endif
</div>
