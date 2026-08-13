<?php

namespace App\Livewire\User;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\User;
use App\Models\Kebutuhan;
use App\Models\Rkas;
use App\Models\HasilKlasifikasi;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class UserManager extends Component
{
    use WithPagination;

    public string $search = '';
    public string $filterRole = '';

    // Bulk selection fields
    public array $selectedUsers = [];
    public bool $selectAll = false;

    // Form fields
    public bool $isEdit = false;
    public ?int $userId = null;
    public string $name = '';
    public string $email = '';
    public string $role = 'bendahara';
    public string $nip = '';
    public string $jabatan = '';
    public string $no_telepon = '';
    public string $password = '';
    public string $password_confirmation = '';

    protected function rules(): array
    {
        $rules = [
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . ($this->userId ?? 'NULL') . ',id',
            'role' => 'required|in:admin,kepala_sekolah,bendahara',
            'nip' => 'nullable|string|max:50',
            'jabatan' => 'nullable|string|max:100',
            'no_telepon' => 'nullable|string|max:20',
        ];

        if (!$this->isEdit) {
            $rules['password'] = ['required', 'string', 'confirmed', Password::min(8)];
        } else {
            $rules['password'] = ['nullable', 'string', 'confirmed', Password::min(8)];
        }

        return $rules;
    }

    public function updatedSelectAll($value)
    {
        if ($value) {
            $this->selectedUsers = User::query()
                ->when($this->search, function ($q) {
                    $q->where('name', 'like', "%{$this->search}%")
                      ->orWhere('email', 'like', "%{$this->search}%");
                })
                ->when($this->filterRole, fn ($q) => $q->where('role', $this->filterRole))
                ->pluck('id')
                ->map(fn ($id) => (string) $id)
                ->toArray();
        } else {
            $this->selectedUsers = [];
        }
    }

    public function updatingSearch()
    {
        $this->resetPage();
        $this->selectedUsers = [];
        $this->selectAll = false;
    }

    public function updatingFilterRole()
    {
        $this->resetPage();
        $this->selectedUsers = [];
        $this->selectAll = false;
    }

    public function openCreateModal()
    {
        $this->reset(['userId', 'name', 'email', 'role', 'nip', 'jabatan', 'no_telepon', 'password', 'password_confirmation', 'isEdit']);
        $this->role = 'bendahara';
        $this->resetValidation();
        $this->dispatch('open-modal', 'user-modal');
    }

    public function openEditModal(int $id)
    {
        $user = User::findOrFail($id);
        $this->userId = $user->id;
        $this->name = $user->name;
        $this->email = $user->email;
        $this->role = $user->role;
        $this->nip = $user->nip ?? '';
        $this->jabatan = $user->jabatan ?? '';
        $this->no_telepon = $user->no_telepon ?? '';
        $this->password = '';
        $this->password_confirmation = '';
        $this->isEdit = true;
        $this->resetValidation();
        $this->dispatch('open-modal', 'user-modal');
    }

    public function save()
    {
        if (!auth()->user()->isAdmin()) return;

        $this->validate();

        $data = [
            'name' => $this->name,
            'email' => $this->email,
            'role' => $this->role,
            'nip' => $this->nip,
            'jabatan' => $this->jabatan,
            'no_telepon' => $this->no_telepon,
        ];

        if (!empty($this->password)) {
            $data['password'] = Hash::make($this->password);
        }

        User::updateOrCreate(
            ['id' => $this->userId],
            $data
        );

        $this->dispatch('close-modal', 'user-modal');
        session()->flash('success', $this->isEdit ? 'Pengguna berhasil diperbarui!' : 'Pengguna baru berhasil ditambahkan!');
    }

    public function delete(int $id)
    {
        if (!auth()->user()->isAdmin()) return;

        if ($id === auth()->id()) {
            $this->dispatch('swal:error', [
                'title' => 'Tidak Dapat Menghapus Akun Sendiri',
                'text' => 'Anda tidak dapat menghapus akun Anda sendiri yang sedang digunakan saat ini.'
            ]);
            session()->flash('error', 'Anda tidak dapat menghapus akun Anda sendiri.');
            return;
        }

        $user = User::findOrFail($id);

        $hasData = Kebutuhan::where('user_id', $user->id)->exists()
            || Rkas::where('approved_by', $user->id)->exists()
            || HasilKlasifikasi::where('classified_by', $user->id)->exists();

        if ($hasData) {
            $this->dispatch('swal:error', [
                'title' => 'Pengguna Terikat Data',
                'text' => "Akun pengguna '{$user->name}' tidak dapat dihapus karena memiliki riwayat pengajuan, persetujuan RKAS, atau hasil klasifikasi di sistem."
            ]);
            session()->flash('error', "Pengguna '{$user->name}' tidak dapat dihapus karena memiliki riwayat data di sistem.");
            return;
        }

        $user->delete();
        $this->selectedUsers = array_diff($this->selectedUsers, [(string)$id]);
        session()->flash('success', 'Pengguna berhasil dihapus!');
    }

    public function deleteSelected()
    {
        if (!auth()->user()->isAdmin() || empty($this->selectedUsers)) return;

        $users = User::whereIn('id', $this->selectedUsers)->get();
        $cannotDelete = [];
        $deletedCount = 0;

        foreach ($users as $user) {
            if ($user->id === auth()->id()) {
                $cannotDelete[] = "{$user->name} (Akun Sendiri)";
                continue;
            }

            $hasData = Kebutuhan::where('user_id', $user->id)->exists()
                || Rkas::where('approved_by', $user->id)->exists()
                || HasilKlasifikasi::where('classified_by', $user->id)->exists();

            if ($hasData) {
                $cannotDelete[] = $user->name;
            } else {
                $user->delete();
                $deletedCount++;
            }
        }

        $this->selectedUsers = [];
        $this->selectAll = false;

        if (count($cannotDelete) > 0 && $deletedCount === 0) {
            $namaList = implode(', ', $cannotDelete);
            $this->dispatch('swal:error', [
                'title' => 'Seluruh Pilihan Memiliki Riwayat Data',
                'text' => "Seluruh pilihan pengguna tidak dapat dihapus karena semua akun yang Anda pilih ({$namaList}) sudah memiliki riwayat data/transaksi di sistem."
            ]);
            session()->flash('error', "Seluruh pilihan pengguna ({$namaList}) tidak dapat dihapus karena memiliki riwayat data.");
        } elseif (count($cannotDelete) > 0 && $deletedCount > 0) {
            $namaList = implode(', ', $cannotDelete);
            $this->dispatch('swal:error', [
                'title' => 'Sebagian Pengguna Memiliki Riwayat Data',
                'text' => "Berhasil menghapus {$deletedCount} pengguna. Namun, dalam pilihan Anda terdapat pengguna ({$namaList}) yang sudah memiliki riwayat data/transaksi sehingga tidak dapat dihapus."
            ]);
            session()->flash('warning', "Berhasil menghapus {$deletedCount} pengguna. Pengguna ({$namaList}) tidak dapat dihapus karena memiliki riwayat data.");
        } else {
            session()->flash('success', "Berhasil menghapus seluruh pengguna terpilih!");
        }
    }

    public function render()
    {
        $users = User::query()
            ->when($this->search, function ($q) {
                $q->where('name', 'like', "%{$this->search}%")
                  ->orWhere('email', 'like', "%{$this->search}%");
            })
            ->when($this->filterRole, fn ($q) => $q->where('role', $this->filterRole))
            ->orderBy('name', 'asc')
            ->paginate(10);

        return view('livewire.user.user-manager', compact('users'))
            ->layout('components.layouts.app', ['header' => 'Pengguna']);
    }
}
