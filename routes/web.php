<?php

use Illuminate\Support\Facades\Route;
use App\Livewire\Auth\Login;
use App\Livewire\Auth\EditProfile;
use App\Livewire\Dashboard\DashboardManager;
use App\Livewire\PeriodeBos\PeriodeBosManager;
use App\Livewire\KatalogBarang\KatalogBarangManager;
use App\Livewire\User\UserManager;
use App\Livewire\Kebutuhan\KebutuhanManager;
use App\Livewire\Rkas\RkasManager;
use App\Livewire\Rkas\KonfirmasiBarangDatangManager;
use App\Livewire\Bku\BkuManager;
use App\Livewire\Bku\CreateBkuItem;
use App\Livewire\Klasifikasi\KlasifikasiManager;
use App\Livewire\Klasifikasi\ImportDataTrainingManager;
use App\Livewire\Monitoring\MonitoringManager;
use App\Http\Controllers\MonitoringPdfController;
use App\Livewire\Kategori\KategoriManager;
use Illuminate\Support\Facades\Auth;

use App\Livewire\Rkas\VerifyRkas;

// Public Verification Route (Scanned from QR Code)
Route::get('/verify-rkas/{kode}', VerifyRkas::class)->name('rkas.verify');

// Guest Routes
Route::middleware('guest')->group(function () {
    Route::get('/login', Login::class)->name('login');
});

// Authenticated Routes
Route::middleware('auth')->group(function () {
    // Logout
    Route::post('/logout', function () {
        Auth::logout();
        request()->session()->invalidate();
        request()->session()->regenerateToken();
        return redirect()->route('login');
    })->name('logout');

    // Profile Edit
    Route::get('/profile', EditProfile::class)->name('profile.edit');

    // Routes accessible by ALL authenticated users (including Guru)
    Route::get('/', DashboardManager::class)->name('dashboard');
    Route::get('/kebutuhan', KebutuhanManager::class)->name('kebutuhan.index');

    // Routes restricted from Guru (Admin & Kepala Sekolah only)
    Route::group(['middleware' => function ($request, $next) {
        if ($request->user()?->isGuru()) {
            abort(403, 'Akses Ditolak. Halaman ini bukan hak akses Anda.');
        }
        return $next($request);
    }], function () {
        Route::get('/rkas', RkasManager::class)->name('rkas.index');
        Route::get('/konfirmasi-barang', KonfirmasiBarangDatangManager::class)->name('konfirmasi-barang.index');
        Route::get('/bku', BkuManager::class)->name('bku.index');
        Route::get('/bku/create', CreateBkuItem::class)->name('bku.create');
        Route::get('/periode-bos', PeriodeBosManager::class)->name('periode-bos.index');
        Route::get('/katalog-barang', KatalogBarangManager::class)->name('katalog-barang.index');
        Route::get('/kategori', KategoriManager::class)->name('kategori.index');
        Route::get('/users', UserManager::class)->name('users.index');

        // AI / ML & Monitoring Routes
        Route::get('/klasifikasi', KlasifikasiManager::class)->name('klasifikasi.index');
        Route::get('/import-training', ImportDataTrainingManager::class)->name('import-training.index');
        Route::get('/monitoring', MonitoringManager::class)->name('monitoring.index');
        Route::get('/monitoring/download-pdf/{periode}', [MonitoringPdfController::class, 'download'])->name('monitoring.download-pdf');
    });
});
