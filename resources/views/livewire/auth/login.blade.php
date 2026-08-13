<div class="bg-white dark:bg-zinc-900 p-8 rounded-2xl shadow-xl border border-gray-100 dark:border-zinc-800 space-y-6">
    <div class="text-center space-y-2">
        <img src="{{ asset('images/logo.png') }}" alt="Logo SMP Negeri 40 Kota Bekasi" class="mx-auto mb-3 object-contain" style="max-height: 150px; width: auto;">
        <p class="text-sm text-gray-500 dark:text-zinc-400">Masukkan surel dan kata sandi Anda</p>
    </div>

    <form wire:submit="login" class="space-y-4">
        <div>
            <label for="email" class="block text-xs font-semibold uppercase tracking-wider text-gray-700 dark:text-zinc-300 mb-1">
                Surel
            </label>
            <input type="email" 
                   id="email" 
                   wire:model="email"
                   class="w-full px-4 py-2.5 rounded-xl border border-gray-300 dark:border-zinc-700 bg-white dark:bg-zinc-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm transition-all @error('email') border-rose-500 focus:ring-rose-500 @enderror"
                   placeholder="nama@sekolah.sch.id"
                   required autofocus>
            @error('email')
                @if(str_contains($message, 'diblokir'))
                <div class="p-3.5 rounded-xl bg-rose-50 dark:bg-rose-950/40 border border-rose-200 dark:border-rose-800 text-rose-800 dark:text-rose-300 text-xs flex items-start gap-2.5 shadow-2xs mt-2">
                    <svg class="w-4 h-4 text-rose-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <div class="space-y-0.5">
                        <p class="font-bold text-rose-900 dark:text-rose-200 text-xs">Akses Diblokir Sementara</p>
                        <p class="text-rose-700 dark:text-rose-300 leading-relaxed text-[11px]">{{ $message }}</p>
                    </div>
                </div>
                @else
                <p class="text-xs text-rose-500 mt-1 font-medium">{{ $message }}</p>
                @endif
            @enderror
        </div>

        <div>
            <label for="password" class="block text-xs font-semibold uppercase tracking-wider text-gray-700 dark:text-zinc-300 mb-1">
                Kata Sandi
            </label>
            <input type="password" 
                   id="password" 
                   wire:model="password"
                   class="w-full px-4 py-2.5 rounded-xl border border-gray-300 dark:border-zinc-700 bg-white dark:bg-zinc-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm transition-all @error('password') border-rose-500 focus:ring-rose-500 @enderror"
                   placeholder="••••••••"
                   required>
            @error('password')
                <p class="text-xs text-rose-500 mt-1 font-medium">{{ $message }}</p>
            @enderror
        </div>

        <div class="flex items-center justify-between pt-1">
            <label class="flex items-center gap-2 cursor-pointer">
                <input type="checkbox" wire:model="remember" class="w-4 h-4 rounded text-blue-600 focus:ring-blue-500 border-gray-300 dark:border-zinc-700">
                <span class="text-xs text-gray-600 dark:text-zinc-400">Ingat Saya</span>
            </label>
        </div>

        <button type="submit" 
                wire:loading.attr="disabled"
                class="w-full py-3 px-4 rounded-xl bg-blue-600 hover:bg-blue-500 active:bg-blue-700 text-white font-semibold text-sm shadow-md shadow-blue-500/20 transition-all flex items-center justify-center gap-2 disabled:opacity-50">
            <svg wire:loading class="animate-spin h-4 w-4 text-white" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
            <span wire:loading.remove>Masuk</span>
            <span wire:loading>Memproses...</span>
        </button>
    </form>
</div>
