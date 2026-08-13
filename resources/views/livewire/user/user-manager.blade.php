<div class="space-y-6">
    
    <!-- Top Action & Search Bar -->
    <div class="flex flex-col sm:flex-row items-center justify-between gap-4">
        <div class="w-full sm:w-80">
            <input type="text" 
                   wire:model.live.debounce.300ms="search" 
                   placeholder="Cari nama, email, atau NIP..." 
                   class="w-full px-4 py-2.5 rounded-xl border border-gray-300 dark:border-zinc-700 bg-white dark:bg-zinc-800 text-gray-900 dark:text-white text-sm focus:ring-2 focus:ring-blue-500 shadow-xs">
        </div>

        <div class="w-full sm:w-auto flex items-center justify-end gap-3">
            @if(count($selectedUsers) > 0)
            <button type="button"
                    @click="confirmDelete('Hapus {{ count($selectedUsers) }} Pengguna', 'Apakah Anda yakin ingin menghapus {{ count($selectedUsers) }} pengguna terpilih?', () => $wire.deleteSelected())"
                    class="px-4 py-2.5 rounded-xl bg-rose-600 hover:bg-rose-500 text-white font-semibold text-sm shadow-md transition-all flex items-center gap-2 cursor-pointer">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                <span>Hapus Terpilih ({{ count($selectedUsers) }})</span>
            </button>
            @endif

            <button wire:click="openCreateModal" 
                    class="px-5 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-500 text-white font-semibold text-sm shadow-md shadow-blue-500/20 transition-all flex items-center justify-center cursor-pointer">
                <span>Tambah</span>
            </button>
        </div>
    </div>

    <!-- Data Table -->
    <x-card class="p-0 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-gray-600 dark:text-zinc-300">
                <thead class="bg-gray-50 dark:bg-zinc-800/60 text-xs font-semibold text-gray-500 dark:text-zinc-400 uppercase tracking-wider border-b border-gray-100 dark:border-zinc-800">
                    <tr>
                        <th class="px-4 py-4 w-10 text-center">
                            <input type="checkbox" wire:model.live="selectAll" class="w-4 h-4 rounded border-gray-300 dark:border-zinc-700 text-blue-600 focus:ring-blue-500 cursor-pointer">
                        </th>
                        <th class="px-6 py-4">Pengguna</th>
                        <th class="px-6 py-4">NIP</th>
                        <th class="px-6 py-4">Peran</th>
                        <th class="px-6 py-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-zinc-800/80">
                    @forelse($users as $u)
                    @php
                        $hasUserData = \App\Models\Kebutuhan::where('user_id', $u->id)->exists()
                            || \App\Models\Rkas::where('approved_by', $u->id)->exists()
                            || \App\Models\HasilKlasifikasi::where('classified_by', $u->id)->exists();
                    @endphp
                    <tr class="hover:bg-gray-50/50 dark:hover:bg-zinc-800/40 transition-colors {{ in_array((string)$u->id, $selectedUsers) ? 'bg-blue-50/40 dark:bg-blue-950/20' : '' }}">
                        <td class="px-4 py-4 text-center">
                            @if($u->id !== auth()->id())
                            <input type="checkbox" wire:model.live="selectedUsers" value="{{ $u->id }}" class="w-4 h-4 rounded border-gray-300 dark:border-zinc-700 text-blue-600 focus:ring-blue-500 cursor-pointer">
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <img class="w-9 h-9 rounded-full object-cover border border-gray-200 dark:border-zinc-700" 
                                     src="{{ $u->getFilamentAvatarUrl() }}" alt="{{ $u->name }}">
                                <div>
                                    <p class="font-semibold text-gray-900 dark:text-white">{{ $u->name }}</p>
                                    <p class="text-xs text-gray-500 dark:text-zinc-400">{{ $u->email }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-xs font-mono">
                            {{ $u->nip ?? '-' }}
                        </td>
                        <td class="px-6 py-4">
                            <x-badge :color="match($u->role) {
                                'admin' => 'purple',
                                'kepala_sekolah' => 'warning',
                                default => 'info',
                            }">
                                {{ match($u->role) {
                                    'admin' => 'Admin / Bendahara',
                                    'kepala_sekolah' => 'Kepala Sekolah',
                                    'guru' => 'Guru',
                                    default => $u->role,
                                } }}
                            </x-badge>
                        </td>
                        <td class="px-6 py-4 text-right whitespace-nowrap">
                            <div class="flex items-center justify-end gap-2">
                                <button wire:click="openEditModal({{ $u->id }})" title="Edit Pengguna" class="p-1.5 text-blue-600 hover:text-blue-800 dark:text-blue-400 hover:bg-blue-50 dark:hover:bg-zinc-800 rounded-lg transition-colors cursor-pointer">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                </button>
                                @if($u->id !== auth()->id())
                                <button type="button" 
                                        @click="if ({{ $hasUserData ? 'true' : 'false' }}) {
                                            Swal.fire({
                                                title: 'Pengguna Terikat Data',
                                                text: 'Akun pengguna \'{{ addslashes($u->name) }}\' tidak dapat dihapus karena memiliki riwayat transaksi/pengajuan di sistem.',
                                                icon: 'warning',
                                                confirmButtonColor: '#ef4444',
                                                confirmButtonText: 'Mengerti',
                                                customClass: {
                                                    popup: 'rounded-3xl border border-gray-200 dark:border-zinc-800 shadow-2xl dark:bg-zinc-900 dark:text-white',
                                                    title: 'text-lg font-bold text-gray-900 dark:text-white',
                                                    confirmButton: 'px-5 py-2.5 rounded-xl font-semibold text-sm shadow-md cursor-pointer'
                                                }
                                            });
                                        } else {
                                            confirmDelete('Hapus Pengguna', 'Apakah Anda yakin ingin menghapus akun {{ addslashes($u->name) }}?', () => $wire.delete({{ $u->id }}));
                                        }" 
                                        title="Hapus Pengguna"
                                        class="p-1.5 text-rose-600 hover:text-rose-800 dark:text-rose-400 hover:bg-rose-50 dark:hover:bg-zinc-800 rounded-lg transition-colors cursor-pointer">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                </button>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-8 text-center text-gray-500 dark:text-zinc-400 text-sm">
                            Belum ada data pengguna.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="px-6 py-4 border-t border-gray-100 dark:border-zinc-800">
            {{ $users->links() }}
        </div>
    </x-card>

    <!-- Create/Edit Modal -->
    <x-modal name="user-modal" :title="$isEdit ? 'Edit Data Pengguna' : 'Tambah Pengguna Baru'">
        <form wire:submit.prevent="save" class="space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-gray-700 dark:text-zinc-300 mb-1">Nama Lengkap <span class="text-rose-500">*</span></label>
                    <input type="text" wire:model="name" required placeholder="Nama Guru/Staf..." class="w-full px-4 py-2.5 rounded-xl border border-gray-300 dark:border-zinc-700 bg-white dark:bg-zinc-800 text-gray-900 dark:text-white text-sm">
                    @error('name') <p class="text-xs text-rose-500 mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-gray-700 dark:text-zinc-300 mb-1">NIP (Nomor Induk Pegawai)</label>
                    <input type="text" wire:model="nip" placeholder="19800101..." class="w-full px-4 py-2.5 rounded-xl border border-gray-300 dark:border-zinc-700 bg-white dark:bg-zinc-800 text-gray-900 dark:text-white text-sm">
                    @error('nip') <p class="text-xs text-rose-500 mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-gray-700 dark:text-zinc-300 mb-1">Surel <span class="text-rose-500">*</span></label>
                    <input type="email" wire:model="email" required placeholder="nama@sekolah.sch.id" class="w-full px-4 py-2.5 rounded-xl border border-gray-300 dark:border-zinc-700 bg-white dark:bg-zinc-800 text-gray-900 dark:text-white text-sm">
                    @error('email') <p class="text-xs text-rose-500 mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-gray-700 dark:text-zinc-300 mb-1">Peran <span class="text-rose-500">*</span></label>
                    <select wire:model="role" required class="w-full px-4 py-2.5 rounded-xl border border-gray-300 dark:border-zinc-700 bg-white dark:bg-zinc-800 text-gray-900 dark:text-white text-sm">
                        <option value="guru">Guru</option>
                        <option value="admin">Admin / Bendahara</option>
                        <option value="kepala_sekolah">Kepala Sekolah</option>
                    </select>
                    @error('role') <p class="text-xs text-rose-500 mt-1">{{ $message }}</p> @enderror
                </div>

                <div class="md:col-span-2">
                    <label class="block text-xs font-semibold uppercase tracking-wider text-gray-700 dark:text-zinc-300 mb-1">
                        {{ $isEdit ? 'Kata Sandi Baru (Kosongkan jika tidak ingin diubah)' : 'Kata Sandi' }}
                    </label>
                    <input type="password" wire:model="password" placeholder="••••••••" class="w-full px-4 py-2.5 rounded-xl border border-gray-300 dark:border-zinc-700 bg-white dark:bg-zinc-800 text-gray-900 dark:text-white text-sm">
                    @error('password') <p class="text-xs text-rose-500 mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            <!-- Modal Footer Buttons (Inside Form) -->
            <div class="pt-4 border-t border-gray-100 dark:border-zinc-800 flex items-center justify-end gap-3">
                <button type="button" @click="$dispatch('close-modal', 'user-modal')" class="px-4 py-2 rounded-xl bg-gray-100 hover:bg-gray-200 dark:bg-zinc-800 dark:hover:bg-zinc-700 text-gray-700 dark:text-zinc-300 text-sm font-semibold cursor-pointer">
                    Batal
                </button>
                <button type="submit" class="px-4 py-2 rounded-xl bg-blue-600 hover:bg-blue-500 text-white text-sm font-semibold shadow-md cursor-pointer">
                    Simpan
                </button>
            </div>
        </form>
    </x-modal>

</div>
