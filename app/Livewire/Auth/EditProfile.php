<?php

namespace App\Livewire\Auth;

use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class EditProfile extends Component
{
    use WithFileUploads;

    public string $name = '';
    public string $email = '';
    public ?string $nip = null;
    public $avatar = null;

    public bool $isNameLocked = false;
    public bool $isNipLocked = false;
    public bool $isAdmin = false;

    // Admin-only password change fields
    public string $current_password = '';
    public string $new_password = '';
    public string $new_password_confirmation = '';

    public function mount()
    {
        $user = auth()->user();
        $this->name = $user->name ?? '';
        $this->email = $user->email ?? '';
        $this->nip = $user->nip ?? null;
        $this->isAdmin = $user->isAdmin();

        // Lock Name if user is not admin and name is already filled & saved
        if (!$this->isAdmin && !empty($user->name_locked_at)) {
            $this->isNameLocked = true;
        }

        // Lock NIP if user is not admin and NIP is already filled & saved
        if (!$this->isAdmin && !empty($user->nip_locked_at)) {
            $this->isNipLocked = true;
        }
    }

    public function updateProfile()
    {
        $user = auth()->user();

        $rules = [
            'avatar' => 'nullable|image|max:2048', // 2MB Max
        ];

        if ($this->isAdmin) {
            $rules['name'] = 'required|string|max:255';
            $rules['email'] = ['required', 'email', 'max:255', Rule::unique('users')->ignore($user->id)];
            $rules['nip'] = 'nullable|string|max:50';
        } else {
            if (!$this->isNameLocked) {
                $rules['name'] = 'required|string|max:255';
            }
            if (!$this->isNipLocked) {
                $rules['nip'] = 'nullable|string|max:50';
            }
        }

        $this->validate($rules);

        // Handle Avatar Upload
        if ($this->avatar) {
            if ($user->avatar_path && Storage::disk('public')->exists($user->avatar_path)) {
                Storage::disk('public')->delete($user->avatar_path);
            }
            $user->avatar_path = $this->avatar->store('avatars', 'public');
        }

        // Update Name & lock if non-admin filled it
        if ($this->isAdmin || !$this->isNameLocked) {
            $user->name = $this->name;
            if (!$this->isAdmin && !empty($this->name)) {
                $user->name_locked_at = now();
                $this->isNameLocked = true;
            }
        }

        // Update NIP & lock if non-admin filled it
        if ($this->isAdmin || !$this->isNipLocked) {
            $user->nip = $this->nip;
            if (!$this->isAdmin && !empty($this->nip)) {
                $user->nip_locked_at = now();
                $this->isNipLocked = true;
            }
        }

        if ($this->isAdmin) {
            $user->email = $this->email;
        }

        $user->save();

        session()->flash('success', 'Profil Anda berhasil diperbarui!');
        return $this->redirect(route('dashboard'), navigate: true);
    }

    public function updatePassword()
    {
        if (!$this->isAdmin) {
            abort(403, 'Hanya Admin yang dapat mengubah kata sandi dari profil.');
        }

        $this->validate([
            'current_password' => 'required|string',
            'new_password' => 'required|string|min:8|confirmed',
        ], [
            'current_password.required' => 'Kata sandi saat ini wajib diisi.',
            'new_password.required' => 'Kata sandi baru wajib diisi.',
            'new_password.min' => 'Kata sandi baru minimal 8 karakter.',
            'new_password.confirmed' => 'Konfirmasi kata sandi baru tidak cocok.',
        ]);

        $user = auth()->user();

        if (!Hash::check($this->current_password, $user->password)) {
            $this->addError('current_password', 'Kata sandi saat ini tidak cocok.');
            return;
        }

        $user->password = Hash::make($this->new_password);
        $user->save();

        $this->reset(['current_password', 'new_password', 'new_password_confirmation']);
        session()->flash('success', 'Kata sandi Admin berhasil diperbarui!');
    }

    public function render()
    {
        return view('livewire.auth.edit-profile')
            ->layout('components.layouts.app', ['header' => 'Edit Profil']);
    }
}
