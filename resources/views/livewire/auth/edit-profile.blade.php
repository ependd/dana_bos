<div class="max-w-4xl mx-auto space-y-8">
    
    <!-- Profile Info Card -->
    <x-card title="Informasi Profil" description="Perbarui foto profil dan identitas akun Anda">
        <form wire:submit.prevent="updateProfile" class="space-y-6">
            
            <!-- Avatar Upload Preview (Bebas Diubah Kapan Saja) -->
            <div class="flex items-center gap-6 p-4 rounded-xl bg-gray-50 dark:bg-zinc-800/50 border border-gray-100 dark:border-zinc-800">
                <div class="relative shrink-0">
                    @if ($avatar)
                        <img class="w-20 h-20 rounded-full object-cover border-2 border-blue-500 shadow-md" src="{{ $avatar->temporaryUrl() }}" alt="Preview">
                    @else
                        <img class="w-20 h-20 rounded-full object-cover border-2 border-gray-200 dark:border-zinc-700" src="{{ auth()->user()->getFilamentAvatarUrl() }}" alt="Avatar">
                    @endif
                </div>

                <div class="space-y-1.5 flex-1">
                    <div class="flex items-center justify-between">
                        <label class="block text-xs font-semibold uppercase tracking-wider text-gray-700 dark:text-zinc-300">Foto Profil</label>
                        <span class="text-[11px] text-emerald-600 dark:text-emerald-400 font-medium">Bebas diubah kapan saja</span>
                    </div>
                    <input type="file" wire:model="avatar" accept="image/*" class="text-xs text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 dark:file:bg-zinc-800 dark:file:text-zinc-300">
                    @error('avatar') <p class="text-xs text-rose-500 mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Name -->
                <div>
                    <div class="flex items-center justify-between mb-1">
                        <label for="name" class="block text-xs font-semibold uppercase tracking-wider text-gray-700 dark:text-zinc-300">Nama Lengkap</label>
                        @if($isNameLocked && !$isAdmin)
                            <span class="inline-flex items-center gap-1 text-[11px] font-medium text-amber-700 dark:text-amber-400">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                                Terkunci
                            </span>
                        @endif
                    </div>
                    <input type="text" 
                           id="name" 
                           wire:model="name" 
                           @disabled($isNameLocked && !$isAdmin)
                           class="w-full px-4 py-2.5 rounded-xl border border-gray-300 dark:border-zinc-700 bg-white dark:bg-zinc-800 text-gray-900 dark:text-white text-sm focus:ring-2 focus:ring-blue-500 disabled:bg-gray-100 dark:disabled:bg-zinc-900 disabled:text-gray-500">
                    @if($isNameLocked && !$isAdmin)
                        <p class="text-[11px] text-gray-400 mt-1">Nama telah dikunci. Hubungi Admin jika ada kesalahan data.</p>
                    @endif
                    @error('name') <p class="text-xs text-rose-500 mt-1">{{ $message }}</p> @enderror
                </div>

                <!-- NIP -->
                <div>
                    <div class="flex items-center justify-between mb-1">
                        <label for="nip" class="block text-xs font-semibold uppercase tracking-wider text-gray-700 dark:text-zinc-300">NIP (Nomor Induk Pegawai)</label>
                        @if($isNipLocked && !$isAdmin)
                            <span class="inline-flex items-center gap-1 text-[11px] font-medium text-amber-700 dark:text-amber-400">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                                Terkunci
                            </span>
                        @endif
                    </div>
                    <input type="text" 
                           id="nip" 
                           wire:model="nip" 
                           @disabled($isNipLocked && !$isAdmin)
                           class="w-full px-4 py-2.5 rounded-xl border border-gray-300 dark:border-zinc-700 bg-white dark:bg-zinc-800 text-gray-900 dark:text-white text-sm focus:ring-2 focus:ring-blue-500 disabled:bg-gray-100 dark:disabled:bg-zinc-900 disabled:text-gray-500" 
                           placeholder="19800101...">
                    @if($isNipLocked && !$isAdmin)
                        <p class="text-[11px] text-gray-400 mt-1">NIP telah dikunci. Hubungi Admin jika ada kesalahan data.</p>
                    @endif
                    @error('nip') <p class="text-xs text-rose-500 mt-1">{{ $message }}</p> @enderror
                </div>

                <!-- Email (Disabled for non-admin) -->
                <div class="md:col-span-2">
                    <div class="flex items-center justify-between mb-1">
                        <label for="email" class="block text-xs font-semibold uppercase tracking-wider text-gray-700 dark:text-zinc-300">Surel Akun</label>
                        @if(!$isAdmin)
                            <span class="inline-flex items-center gap-1 text-[11px] font-medium text-gray-500">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                                Dikelola Admin
                            </span>
                        @endif
                    </div>
                    <input type="email" 
                           id="email" 
                           wire:model="email" 
                           @disabled(!$isAdmin)
                           class="w-full px-4 py-2.5 rounded-xl border border-gray-300 dark:border-zinc-700 bg-white dark:bg-zinc-800 text-gray-900 dark:text-white text-sm focus:ring-2 focus:ring-blue-500 disabled:bg-gray-100 dark:disabled:bg-zinc-900 disabled:text-gray-500">
                    @if(!$isAdmin)
                        <p class="text-[11px] text-gray-400 mt-1">Perubahan email akun hanya dapat dilakukan melalui Admin/Bendahara.</p>
                    @endif
                    @error('email') <p class="text-xs text-rose-500 mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            <div class="flex justify-end pt-2">
                <button type="submit" 
                        wire:loading.attr="disabled" 
                        wire:target="updateProfile, avatar"
                        class="px-5 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-500 disabled:opacity-50 disabled:cursor-not-allowed text-white font-semibold text-sm shadow-md transition-all flex items-center justify-center gap-2">
                    <svg wire:loading wire:target="updateProfile, avatar" class="animate-spin w-4 h-4 text-white" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                    <span>Simpan Profil</span>
                </button>
            </div>
        </form>
    </x-card>

    @if($isAdmin)
    <!-- Password Change Card (Khusus Admin) -->
    <x-card title="Ubah Kata Sandi Admin" description="Pastikan akun Admin menggunakan kata sandi yang aman">
        <form wire:submit="updatePassword" class="space-y-6">
            
            <div class="space-y-4 max-w-md">
                <div>
                    <label for="current_password" class="block text-xs font-semibold uppercase tracking-wider text-gray-700 dark:text-zinc-300 mb-1">Kata Sandi Saat Ini</label>
                    <input type="password" id="current_password" wire:model="current_password" class="w-full px-4 py-2.5 rounded-xl border border-gray-300 dark:border-zinc-700 bg-white dark:bg-zinc-800 text-gray-900 dark:text-white text-sm">
                    @error('current_password') <p class="text-xs text-rose-500 mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="new_password" class="block text-xs font-semibold uppercase tracking-wider text-gray-700 dark:text-zinc-300 mb-1">Kata Sandi Baru</label>
                    <input type="password" id="new_password" wire:model="new_password" class="w-full px-4 py-2.5 rounded-xl border border-gray-300 dark:border-zinc-700 bg-white dark:bg-zinc-800 text-gray-900 dark:text-white text-sm">
                    @error('new_password') <p class="text-xs text-rose-500 mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="new_password_confirmation" class="block text-xs font-semibold uppercase tracking-wider text-gray-700 dark:text-zinc-300 mb-1">Konfirmasi Kata Sandi Baru</label>
                    <input type="password" id="new_password_confirmation" wire:model="new_password_confirmation" class="w-full px-4 py-2.5 rounded-xl border border-gray-300 dark:border-zinc-700 bg-white dark:bg-zinc-800 text-gray-900 dark:text-white text-sm">
                </div>
            </div>

            <div class="flex justify-end pt-2">
                <button type="submit" wire:loading.attr="disabled" class="px-5 py-2.5 rounded-xl bg-zinc-800 hover:bg-zinc-700 text-white font-semibold text-sm shadow-md transition-all">
                    Ubah Kata Sandi
                </button>
            </div>
        </form>
    </x-card>
    @else
    <!-- Informasi Manajemen Akun untuk User Biasa -->
    <div class="p-4 rounded-xl bg-amber-50 dark:bg-amber-950/40 border border-amber-200 dark:border-amber-800 text-amber-800 dark:text-amber-300 text-xs flex items-center gap-3">
        <svg class="w-5 h-5 text-amber-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        <span>Perubahan email, kata sandi, serta perbaikan Nama/NIP yang telah dikunci hanya dapat dilakukan oleh **Admin/Bendahara Sekolah**.</span>
    </div>
    @endif

</div>
