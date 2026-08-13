<?php

namespace App\Livewire\Auth;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class Login extends Component
{
    public string $email = '';
    public string $password = '';
    public bool $remember = false;

    protected array $rules = [
        'email' => 'required|email',
        'password' => 'required|string',
    ];

    protected array $messages = [
        'email.required' => 'Surel wajib diisi.',
        'email.email' => 'Format surel tidak valid.',
        'password.required' => 'Kata sandi wajib diisi.',
    ];

    public function login()
    {
        $this->validate();

        $throttleKey = Str::lower($this->email) . '|' . request()->ip();

        // 1. Cek jika user sedang terblokir (sudah gagal 3 kali)
        if (RateLimiter::tooManyAttempts($throttleKey, 3)) {
            $seconds = RateLimiter::availableIn($throttleKey);
            $timeStr = $this->getTimeRemainingString($seconds);

            throw ValidationException::withMessages([
                'email' => "Terlalu banyak percobaan masuk yang gagal (3 kali). Akses Anda diblokir sementara. Silakan tunggu {$timeStr} lagi sebelum mencoba kembali.",
            ]);
        }

        // 2. Cek kredensial email & password
        if (!Auth::attempt(['email' => $this->email, 'password' => $this->password], $this->remember)) {
            RateLimiter::hit($throttleKey, 180); // Kunci selama 3 menit (180 detik) setelah 3x gagal

            $attempts = RateLimiter::attempts($throttleKey);

            if ($attempts >= 3) {
                $seconds = RateLimiter::availableIn($throttleKey);
                $timeStr = $this->getTimeRemainingString($seconds);

                throw ValidationException::withMessages([
                    'email' => "Terlalu banyak percobaan masuk yang gagal (3 kali). Akses Anda diblokir sementara. Silakan tunggu {$timeStr} lagi sebelum mencoba kembali.",
                ]);
            }

            $remaining = 3 - $attempts;

            throw ValidationException::withMessages([
                'email' => "Surel atau kata sandi yang Anda masukkan salah. (Sisa percobaan: {$remaining} kali)",
            ]);
        }

        // 3. Login berhasil -> reset hit counter rate limiter
        RateLimiter::clear($throttleKey);
        session()->regenerate();

        return redirect()->intended(route('dashboard'));
    }

    protected function getTimeRemainingString(int $seconds): string
    {
        $minutes = floor($seconds / 60);
        $remainingSeconds = $seconds % 60;

        if ($minutes > 0 && $remainingSeconds > 0) {
            return "{$minutes} menit {$remainingSeconds} detik";
        } elseif ($minutes > 0) {
            return "{$minutes} menit";
        } else {
            return "{$remainingSeconds} detik";
        }
    }

    public function render()
    {
        return view('livewire.auth.login')
            ->layout('components.layouts.guest', ['title' => 'Masuk - Dana BOS']);
    }
}
