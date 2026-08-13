@props(['name', 'title' => null, 'maxWidth' => '2xl'])

@php
    $maxWidthClass = match ($maxWidth) {
        'sm' => 'sm:max-w-sm',
        'md' => 'sm:max-w-md',
        'lg' => 'sm:max-w-lg',
        'xl' => 'sm:max-w-xl',
        '2xl' => 'sm:max-w-2xl',
        '3xl' => 'sm:max-w-3xl',
        '4xl' => 'sm:max-w-4xl',
        default => 'sm:max-w-2xl',
    };
@endphp

<div x-data="{ show: false }"
     x-on:open-modal.window="if ($event.detail === '{{ $name }}' || (Array.isArray($event.detail) && $event.detail[0] === '{{ $name }}') || $event.detail?.name === '{{ $name }}') show = true"
     x-on:close-modal.window="if ($event.detail === '{{ $name }}' || (Array.isArray($event.detail) && $event.detail[0] === '{{ $name }}') || $event.detail?.name === '{{ $name }}') show = false"
     x-on:keydown.escape.window="show = false"
     x-show="show"
     class="fixed inset-0 z-50 overflow-y-auto p-4 sm:p-6 flex items-start sm:items-center justify-center min-h-full py-6 sm:py-8"
     style="display: none;"
     x-cloak>

    <!-- Overlay -->
    <div x-show="show"
         x-transition:enter="ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         @click="show = false"
         class="fixed inset-0 bg-gray-900/75 backdrop-blur-xs transition-opacity"></div>

    <!-- Modal Box -->
    <div x-show="show"
         x-transition:enter="ease-out duration-200"
         x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
         x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
         x-transition:leave="ease-in duration-150"
         x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
         x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
         class="bg-white dark:bg-zinc-900 rounded-3xl border border-gray-200 dark:border-zinc-800 shadow-2xl overflow-hidden transform transition-all w-full {{ $maxWidthClass }} z-10 max-h-[80vh] flex flex-col my-auto">

        @if($title)
        <div class="px-6 py-4 border-b border-gray-100 dark:border-zinc-800 flex items-center justify-between shrink-0 bg-white dark:bg-zinc-900">
            <h3 class="text-base font-semibold text-gray-900 dark:text-white">{{ $title }}</h3>
            <button @click="show = false" class="text-gray-400 hover:text-gray-600 dark:hover:text-zinc-200 p-1 rounded-lg hover:bg-gray-100 dark:hover:bg-zinc-800 transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        @endif

        <div class="p-6 overflow-y-auto flex-1 min-h-0 space-y-6">
            {{ $slot }}
        </div>

        @if(isset($footer))
        <div class="px-6 py-4 bg-gray-50/80 dark:bg-zinc-950/60 border-t border-gray-100 dark:border-zinc-800 flex justify-end gap-3 shrink-0">
            {{ $footer }}
        </div>
        @endif

    </div>
</div>
