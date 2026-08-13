<div class="space-y-6">
    
    <!-- Model Selection & Actions -->
    <x-card>
        <div class="flex flex-col md:flex-row items-center justify-between gap-4">
            
            <div class="w-full md:w-auto flex flex-col md:flex-row items-center gap-4">
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-gray-700 dark:text-zinc-300 mb-1">Periode BOS</label>
                    <select wire:model.live="selected_periode" class="px-4 py-2.5 rounded-xl border border-gray-300 dark:border-zinc-700 bg-white dark:bg-zinc-800 text-gray-900 dark:text-white text-sm focus:ring-2 focus:ring-blue-500 transition-all">
                        <option value="">-- Pilih Periode --</option>
                        @foreach($periodes as $id => $tahun)
                        <option value="{{ $id }}">{{ $tahun }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="w-full md:w-auto flex items-center gap-3 justify-end pt-2 md:pt-0">
                <button wire:click="runClassification" wire:loading.attr="disabled" class="px-5 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-500 text-white font-semibold text-sm shadow-md transition-all flex items-center gap-2 disabled:opacity-50 cursor-pointer">
                    <svg wire:loading wire:target="runClassification" class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    <svg wire:loading.remove wire:target="runClassification" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <span wire:loading.remove wire:target="runClassification">Klasifikasi</span>
                    <span wire:loading wire:target="runClassification">Memproses...</span>
                </button>

                <button wire:click="runEvaluation" wire:loading.attr="disabled" class="px-4 py-2.5 rounded-xl bg-purple-600 hover:bg-purple-500 text-white font-semibold text-sm shadow-md transition-all flex items-center gap-2 disabled:opacity-50 cursor-pointer">
                    <svg wire:loading wire:target="runEvaluation" class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    <svg wire:loading.remove wire:target="runEvaluation" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                    <span wire:loading.remove wire:target="runEvaluation">Evaluasi K-Fold</span>
                    <span wire:loading wire:target="runEvaluation">Mengevaluasi...</span>
                </button>
            </div>

        </div>

        <!-- Animated Processing Banner for Classification -->
        <div wire:loading.flex wire:target="runClassification" class="mt-4 p-4 rounded-xl bg-blue-50 dark:bg-blue-950/50 border border-blue-200 dark:border-blue-800 text-blue-900 dark:text-blue-200 text-sm items-center gap-3 shadow-sm">
            <div class="relative flex h-4 w-4">
                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-blue-400 opacity-75"></span>
                <span class="relative inline-flex rounded-full h-4 w-4 bg-blue-600"></span>
            </div>
            <div class="font-medium animate-pulse">
                Proses klasifikasi sedang dijalankan. Mohon tunggu...
            </div>
        </div>

        <!-- Animated Processing Banner for Evaluation -->
        <div wire:loading.flex wire:target="runEvaluation" class="mt-4 p-4 rounded-xl bg-purple-50 dark:bg-purple-950/50 border border-purple-200 dark:border-purple-800 text-purple-900 dark:text-purple-200 text-sm items-center gap-3 shadow-sm">
            <div class="relative flex h-4 w-4">
                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-purple-400 opacity-75"></span>
                <span class="relative inline-flex rounded-full h-4 w-4 bg-purple-600"></span>
            </div>
            <div class="font-medium animate-pulse">
                Proses evaluasi K-Fold sedang dijalankan. Mohon tunggu...
            </div>
        </div>

        @if($evaluationResult)
        @php
            $isEval = ($activeMode ?? 'classification') === 'evaluation';
            $themeCardBg = $isEval ? 'bg-purple-50 dark:bg-purple-950/40 border-purple-200 dark:border-purple-800 text-purple-900 dark:text-purple-300' : 'bg-blue-50 dark:bg-blue-950/40 border-blue-200 dark:border-blue-800 text-blue-900 dark:text-blue-300';
            $themeIcon = $isEval ? 'text-purple-600 dark:text-purple-400' : 'text-blue-600 dark:text-blue-400';
            $themeTitle = $isEval ? 'text-purple-950 dark:text-purple-200' : 'text-blue-950 dark:text-blue-200';
            $optBorder = $isEval ? 'border-purple-200/80 dark:border-purple-800/60' : 'border-blue-200/80 dark:border-blue-800/60';
            $optTitle = $isEval ? 'text-purple-900 dark:text-purple-200' : 'text-blue-900 dark:text-blue-200';
            $optDot = $isEval ? 'bg-purple-600' : 'bg-blue-600';
            $optBadge = $isEval ? 'text-purple-700 dark:text-purple-300 bg-purple-100 dark:bg-purple-900/60' : 'text-blue-700 dark:text-blue-300 bg-blue-100 dark:bg-blue-900/60';
            $optBoxBg = $isEval ? 'bg-purple-50/80 dark:bg-purple-950/50' : 'bg-blue-50/80 dark:bg-blue-950/50';
            $optLabel = $isEval ? 'text-purple-700 dark:text-purple-300' : 'text-blue-700 dark:text-blue-300';
            $optVal = $isEval ? 'text-purple-950 dark:text-purple-100' : 'text-blue-950 dark:text-blue-100';
        @endphp
        <div class="mt-6">
            <div class="p-4 rounded-xl {{ $themeCardBg }} text-xs">
                <div class="mb-3">
                    <p class="font-bold text-sm {{ $themeTitle }} flex items-center gap-2">
                        <svg class="w-4 h-4 {{ $themeIcon }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                        @if($isEval)
                            Rata-Rata Matriks Evaluasi (5-Fold Cross Validation):
                        @else
                            Rata-Rata:
                        @endif
                    </p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- Baseline Model Average Matrix -->
                    <div class="p-3.5 rounded-lg bg-white/80 dark:bg-zinc-800/80 border border-slate-200 dark:border-zinc-700 shadow-xs">
                        <div class="flex items-center justify-between mb-2 pb-1.5 border-b border-slate-100 dark:border-zinc-700/50">
                            <span class="font-semibold text-xs text-slate-700 dark:text-slate-300 flex items-center gap-1.5">
                                <span class="w-2 h-2 rounded-full bg-slate-400"></span>
                                Rata-Rata Model Baseline
                            </span>
                            <span class="text-[10px] font-medium text-slate-500 dark:text-slate-400 bg-slate-100 dark:bg-zinc-700 px-2 py-0.5 rounded-md">Naive Bayes</span>
                        </div>
                        <div class="grid grid-cols-2 sm:grid-cols-4 gap-2">
                            <div class="p-2 rounded-md bg-slate-50/70 dark:bg-zinc-900/60 text-center">
                                <span class="block text-[10px] font-semibold text-slate-600 dark:text-slate-400 uppercase tracking-wider mb-0.5">Akurasi</span>
                                <span class="text-sm font-bold text-slate-900 dark:text-slate-100">{{ $evaluationResult['baseline']['average']['accuracy'] ?? 0 }}%</span>
                            </div>
                            <div class="p-2 rounded-md bg-slate-50/70 dark:bg-zinc-900/60 text-center">
                                <span class="block text-[10px] font-semibold text-slate-600 dark:text-slate-400 uppercase tracking-wider mb-0.5">Presisi</span>
                                <span class="text-sm font-bold text-slate-900 dark:text-slate-100">{{ $evaluationResult['baseline']['average']['precision'] ?? 0 }}%</span>
                            </div>
                            <div class="p-2 rounded-md bg-slate-50/70 dark:bg-zinc-900/60 text-center">
                                <span class="block text-[10px] font-semibold text-slate-600 dark:text-slate-400 uppercase tracking-wider mb-0.5">Sensitivitas</span>
                                <span class="text-sm font-bold text-slate-900 dark:text-slate-100">{{ $evaluationResult['baseline']['average']['recall'] ?? 0 }}%</span>
                            </div>
                            <div class="p-2 rounded-md bg-slate-50/70 dark:bg-zinc-900/60 text-center">
                                <span class="block text-[10px] font-semibold text-slate-600 dark:text-slate-400 uppercase tracking-wider mb-0.5">F1-Score</span>
                                <span class="text-sm font-bold text-slate-900 dark:text-slate-100">{{ $evaluationResult['baseline']['average']['f1_score'] ?? 0 }}%</span>
                            </div>
                        </div>
                    </div>

                    <!-- Optimasi Model (WOA) Average Matrix -->
                    <div class="p-3.5 rounded-lg bg-white/90 dark:bg-zinc-800/90 border {{ $optBorder }} shadow-xs">
                        <div class="flex items-center justify-between mb-2 pb-1.5 border-b border-slate-100 dark:border-zinc-700/50">
                            <span class="font-semibold text-xs {{ $optTitle }} flex items-center gap-1.5">
                                <span class="w-2 h-2 rounded-full {{ $optDot }} animate-pulse"></span>
                                Rata-Rata Model Optimasi
                            </span>
                            <span class="text-[10px] font-semibold {{ $optBadge }} px-2 py-0.5 rounded-md">NB + WOA</span>
                        </div>
                        <div class="grid grid-cols-2 sm:grid-cols-4 gap-2">
                            <div class="p-2 rounded-md {{ $optBoxBg }} text-center">
                                <span class="block text-[10px] font-semibold {{ $optLabel }} uppercase tracking-wider mb-0.5">Akurasi</span>
                                <span class="text-sm font-bold {{ $optVal }}">{{ $evaluationResult['woa']['average']['accuracy'] ?? 0 }}%</span>
                            </div>
                            <div class="p-2 rounded-md {{ $optBoxBg }} text-center">
                                <span class="block text-[10px] font-semibold {{ $optLabel }} uppercase tracking-wider mb-0.5">Presisi</span>
                                <span class="text-sm font-bold {{ $optVal }}">{{ $evaluationResult['woa']['average']['precision'] ?? 0 }}%</span>
                            </div>
                            <div class="p-2 rounded-md {{ $optBoxBg }} text-center">
                                <span class="block text-[10px] font-semibold {{ $optLabel }} uppercase tracking-wider mb-0.5">Sensitivitas</span>
                                <span class="text-sm font-bold {{ $optVal }}">{{ $evaluationResult['woa']['average']['recall'] ?? 0 }}%</span>
                            </div>
                            <div class="p-2 rounded-md {{ $optBoxBg }} text-center">
                                <span class="block text-[10px] font-semibold {{ $optLabel }} uppercase tracking-wider mb-0.5">F1-Score</span>
                                <span class="text-sm font-bold {{ $optVal }}">{{ $evaluationResult['woa']['average']['f1_score'] ?? 0 }}%</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endif
    </x-card>

</div>
