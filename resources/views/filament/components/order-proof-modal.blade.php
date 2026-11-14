<div class="space-y-6">
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        {{-- Bukti DP --}}
        <div class="border border-neutral-700/80 rounded-2xl p-4 bg-neutral-900">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-black uppercase tracking-wider text-amber-400">Bukti Transfer DP</span>
                @if($record->dp_verified_at)
                    <span class="text-[11px] text-emerald-400 font-semibold">&check; Diverifikasi: {{ $record->dp_verified_at->format('d/m/y H:i') }}</span>
                @endif
            </div>

            @if($record->dp_proof_path)
                <div class="rounded-xl overflow-hidden border border-neutral-800 bg-black flex items-center justify-center max-h-80">
                    <img src="{{ $record->dp_proof_url }}" alt="Bukti DP" class="max-h-80 object-contain w-full hover:scale-105 transition duration-300">
                </div>
                <div class="mt-3 text-center">
                    <a href="{{ $record->dp_proof_url }}" target="_blank" class="inline-flex items-center space-x-1.5 text-xs font-bold text-amber-400 hover:text-amber-300">
                        <span>Buka Gambar Asli Resolusi Penuh &nearr;</span>
                    </a>
                </div>
            @else
                <div class="h-40 flex items-center justify-center text-xs text-neutral-500 bg-neutral-950 rounded-xl">
                    Belum ada bukti DP yang diunggah
                </div>
            @endif
        </div>

        {{-- Bukti Pelunasan --}}
        <div class="border border-neutral-700/80 rounded-2xl p-4 bg-neutral-900">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-black uppercase tracking-wider text-emerald-400">Bukti Transfer Pelunasan (Sisa)</span>
                @if($record->final_verified_at)
                    <span class="text-[11px] text-emerald-400 font-semibold">&check; Diverifikasi: {{ $record->final_verified_at->format('d/m/y H:i') }}</span>
                @endif
            </div>

            @if($record->final_proof_path)
                <div class="rounded-xl overflow-hidden border border-neutral-800 bg-black flex items-center justify-center max-h-80">
                    <img src="{{ $record->final_proof_url }}" alt="Bukti Pelunasan" class="max-h-80 object-contain w-full hover:scale-105 transition duration-300">
                </div>
                <div class="mt-3 text-center">
                    <a href="{{ $record->final_proof_url }}" target="_blank" class="inline-flex items-center space-x-1.5 text-xs font-bold text-emerald-400 hover:text-emerald-300">
                        <span>Buka Gambar Asli Resolusi Penuh &nearr;</span>
                    </a>
                </div>
            @else
                <div class="h-40 flex items-center justify-center text-xs text-neutral-500 bg-neutral-950 rounded-xl">
                    Belum ada bukti pelunasan yang diunggah
                </div>
            @endif
        </div>
    </div>

    {{-- Detail Keuangan Cepat --}}
    <div class="bg-neutral-950 p-4 rounded-xl border border-neutral-800 text-xs text-neutral-300 flex justify-between items-center">
        <div>
            Total: <strong class="text-white">Rp{{ number_format($record->total_amount, 0, ',', '.') }}</strong> &bull;
            Sudah Masuk: <strong class="text-emerald-400">Rp{{ number_format($record->amount_paid, 0, ',', '.') }}</strong>
        </div>
        <div>
            Sisa Tagihan: <strong class="text-amber-400 font-mono text-sm">Rp{{ number_format($record->remaining_balance, 0, ',', '.') }}</strong>
        </div>
    </div>
</div>
