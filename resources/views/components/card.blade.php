@props(['title' => null, 'description' => null, 'actions' => null])

<div {{ $attributes->merge(['class' => 'bg-white dark:bg-zinc-900 rounded-xl border border-gray-200 dark:border-zinc-800 shadow-sm overflow-hidden']) }}>
    @if($title || $actions)
    <div class="px-6 py-4 border-b border-gray-100 dark:border-zinc-800 flex items-center justify-between gap-4">
        <div>
            @if($title)
            <h3 class="text-base font-semibold text-gray-900 dark:text-white">{{ $title }}</h3>
            @endif
            @if($description)
            <p class="text-xs text-gray-500 dark:text-zinc-400 mt-0.5">{{ $description }}</p>
            @endif
        </div>
        @if($actions)
        <div class="flex items-center gap-2">
            {{ $actions }}
        </div>
        @endif
    </div>
    @endif

    <div class="p-6">
        {{ $slot }}
    </div>

    @if(isset($footer))
    <div class="px-6 py-3 bg-gray-50/50 dark:bg-zinc-950/40 border-t border-gray-100 dark:border-zinc-800">
        {{ $footer }}
    </div>
    @endif
</div>
