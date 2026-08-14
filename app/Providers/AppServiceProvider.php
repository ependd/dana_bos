<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        \Filament\Support\Facades\FilamentView::registerRenderHook(
            'panels::body.end',
            fn (): string => '
                <script>
                    document.addEventListener("invalid", function(e) { e.preventDefault(); }, true);

                    document.addEventListener("livewire:init", () => {
                        Livewire.onPageExpired((response, message) => {
                            const modalId = "custom-page-expired-modal";
                            if (document.getElementById(modalId)) return;

                            const isDark = document.documentElement.classList.contains("dark");
                            const bgColor = isDark ? "#18181b" : "#ffffff";
                            const borderColor = isDark ? "#27272a" : "#e4e4e7";
                            const textColor = isDark ? "#f4f4f5" : "#18181b";
                            const titleColor = isDark ? "#ffffff" : "#09090b";
                            const descColor = isDark ? "#a1a1aa" : "#71717a";
                            const overlayBg = isDark ? "rgba(9, 9, 11, 0.7)" : "rgba(244, 244, 245, 0.7)";

                            const modalHtml = `
                                <div id="${modalId}" style="
                                    position: fixed;
                                    inset: 0;
                                    z-index: 99999;
                                    display: flex;
                                    align-items: center;
                                    justify-content: center;
                                    background: ${overlayBg};
                                    backdrop-filter: blur(8px);
                                    -webkit-backdrop-filter: blur(8px);
                                    opacity: 0;
                                    transition: opacity 0.3s ease;
                                    padding: 1rem;
                                ">
                                    <div style="
                                        background: ${bgColor};
                                        border: 1px solid ${borderColor};
                                        border-radius: 1rem;
                                        max-width: 420px;
                                        width: 100%;
                                        padding: 2rem;
                                        box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
                                        transform: scale(0.95);
                                        transition: transform 0.3s cubic-bezier(0.16, 1, 0.3, 1);
                                        text-align: center;
                                        font-family: system-ui, -apple-system, sans-serif;
                                        color: ${textColor};
                                    ">
                                        <div style="
                                            width: 3.5rem;
                                            height: 3.5rem;
                                            background: rgba(59, 130, 246, 0.1);
                                            color: #3b82f6;
                                            border-radius: 9999px;
                                            display: flex;
                                            align-items: center;
                                            justify-content: center;
                                            margin: 0 auto 1.25rem;
                                        ">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" style="width: 1.75rem; height: 1.75rem;">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                                            </svg>
                                        </div>

                                        <h3 style="
                                            font-size: 1.25rem;
                                            font-weight: 600;
                                            margin: 0 0 0.5rem;
                                            color: ${titleColor};
                                        ">Sesi Halaman Berakhir</h3>
                                        <p style="
                                            font-size: 0.9rem;
                                            line-height: 1.5;
                                            color: ${descColor};
                                            margin: 0 0 1.5rem;
                                        ">Sesi halaman Anda telah berakhir karena tidak ada aktivitas atau koneksi terputus. Silakan muat ulang halaman untuk melanjutkan.</p>

                                        <button id="btn-reload-page" style="
                                            display: inline-flex;
                                            align-items: center;
                                            justify-content: center;
                                            background: #3b82f6;
                                            color: #ffffff;
                                            border: none;
                                            font-weight: 500;
                                            font-size: 0.9rem;
                                            padding: 0.75rem 1.5rem;
                                            border-radius: 0.5rem;
                                            width: 100%;
                                            cursor: pointer;
                                            transition: background 0.2s ease, transform 0.1s ease;
                                            box-shadow: 0 4px 12px rgba(59, 130, 246, 0.3);
                                        ">
                                            Muat Ulang Halaman
                                        </button>
                                    </div>
                                </div>
                            `;

                            document.body.insertAdjacentHTML("beforeend", modalHtml);

                            const modalEl = document.getElementById(modalId);
                            const modalBox = modalEl.firstElementChild;
                            const btnReload = document.getElementById("btn-reload-page");

                            btnReload.addEventListener("mouseenter", () => btnReload.style.background = "#2563eb");
                            btnReload.addEventListener("mouseleave", () => btnReload.style.background = "#3b82f6");
                            btnReload.addEventListener("mousedown", () => btnReload.style.transform = "scale(0.98)");
                            btnReload.addEventListener("mouseup", () => btnReload.style.transform = "scale(1)");
                            btnReload.addEventListener("click", () => {
                                btnReload.disabled = true;
                                btnReload.innerText = "Memuat ulang...";
                                window.location.reload();
                            });

                            setTimeout(() => {
                                modalEl.style.opacity = "1";
                                modalBox.style.transform = "scale(1)";
                            }, 10);
                        });
                    });
                </script>
            ',
        );
    }
}
