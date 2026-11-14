<div class="min-h-screen bg-[#F8FAFC] text-slate-800 font-sans antialiased pb-24">

    {{-- iOS Navbar --}}
    <header class="border-b border-slate-200/80 bg-white/80 ios-blur sticky top-0 z-30">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 py-3.5 flex items-center justify-between">
            <a href="/" class="flex items-center space-x-2.5 text-slate-900 hover:text-blue-600 transition">
                <div class="w-7 h-7 rounded-lg bg-blue-600 text-white flex items-center justify-center font-black text-xs shadow-sm">
                    IF
                </div>
                <span class="font-extrabold tracking-tight text-sm">PO-IN AJA // TRACKER</span>
            </a>
            <a href="/" class="text-xs font-semibold text-slate-500 hover:text-blue-600 transition flex items-center space-x-1">
                <span>&larr; Form Pemesanan</span>
            </a>
        </div>
    </header>

    <main class="max-w-3xl mx-auto px-4 sm:px-6 py-10 sm:py-14">
        
        {{-- Search Input Card (iOS Clean White) --}}
        <div class="bg-white border border-slate-200/80 rounded-[32px] p-6 sm:p-8 shadow-ios mb-8">
            <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">
                Lacak Status Pesanan Jersey
            </h1>
            <p class="text-slate-500 text-sm mt-1 mb-6">
                Masukkan Kode Invoice (contoh: <code class="text-blue-600 font-mono font-bold">PO-IF-2024</code>) atau Nomor WhatsApp terdaftar.
            </p>

            <form wire:submit.prevent="search" class="flex flex-col sm:flex-row gap-3">
                <input type="text" 
                       wire:model="searchQuery" 
                       placeholder="Kode Invoice / No. WA..."
                       class="flex-1 bg-slate-50 border border-slate-200 rounded-2xl px-5 py-3.5 text-slate-900 text-base focus:bg-white focus:outline-none focus:border-blue-600 focus:ring-4 focus:ring-blue-100 font-mono transition">
                <button type="submit" 
                        class="px-7 py-3.5 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-2xl text-sm transition shadow-md shadow-blue-500/20 shrink-0 active:scale-95">
                    Lacak Pesanan
                </button>
            </form>
            @error('searchQuery') 
                <span class="text-rose-500 text-xs mt-3 block font-semibold">{{ $message }}</span> 
            @enderror
        </div>

        @if($order)
            {{-- TRACKING RESULTS --}}
            <div class="space-y-6">
                
                {{-- 1. ORDER SUMMARY CARD --}}
                <div class="bg-white border border-slate-200/80 rounded-[32px] p-6 sm:p-8 shadow-ios flex flex-col md:flex-row md:items-center justify-between gap-6">
                    <div>
                        <div class="flex items-center space-x-3 mb-2">
                            <span class="font-mono text-2xl font-black text-blue-600">{{ $order->invoice_code }}</span>
                            <span class="px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider bg-blue-50 text-blue-700 border border-blue-100">
                                {{ $order->payment_status->getLabel() }}
                            </span>
                        </div>
                        <div class="text-sm font-semibold text-slate-700">
                            Pemesan: <strong class="text-slate-900">{{ $order->customer_name }}</strong> ({{ $order->customer_phone }})
                        </div>
                        <div class="text-xs text-slate-400 mt-0.5">
                            Campaign: {{ $order->campaign->title }} &bull; Tanggal: {{ $order->created_at->format('d M Y, H:i') }} WIB
                        </div>
                    </div>

                    <div class="bg-slate-50 p-4 rounded-2xl border border-slate-200/80 text-left md:text-right min-w-[190px]">
                        <div class="text-[11px] uppercase font-bold text-slate-400">Total Tagihan:</div>
                        <div class="text-xl font-mono font-black text-slate-900">Rp{{ number_format($order->total_amount, 0, ',', '.') }}</div>
                        <div class="text-xs text-emerald-600 font-semibold mt-0.5">Terbayar: Rp{{ number_format($order->amount_paid, 0, ',', '.') }}</div>
                        @if($order->remaining_balance > 0)
                            <div class="text-xs text-blue-600 font-bold mt-0.5">Sisa: Rp{{ number_format($order->remaining_balance, 0, ',', '.') }}</div>
                        @endif
                    </div>
                </div>

                {{-- 2. VISUAL PRODUCTION STAGE STEPPER (iOS Clean Blue) --}}
                <div class="bg-white border border-slate-200/80 rounded-[32px] p-6 sm:p-8 shadow-ios">
                    <h2 class="text-xs font-black uppercase tracking-wider text-slate-400 mb-6 flex items-center justify-between">
                        <span>Tahapan Produksi Jersey</span>
                        <span class="text-blue-600 font-bold">{{ $order->campaign->stage->getLabel() }}</span>
                    </h2>

                    @php
                        $stages = [
                            \App\Enums\ProductionStage::OpenPo,
                            \App\Enums\ProductionStage::Printing,
                            \App\Enums\ProductionStage::Sewing,
                            \App\Enums\ProductionStage::QualityControl,
                            \App\Enums\ProductionStage::ReadyForPickup,
                        ];
                        $currentIdx = $order->campaign->stage->stepIndex();
                    @endphp

                    <div class="relative">
                        {{-- Connecting Progress Bar --}}
                        <div class="hidden sm:block absolute top-1/2 left-6 right-6 -translate-y-1/2 h-1 bg-slate-100 z-0"></div>
                        <div class="hidden sm:block absolute top-1/2 left-6 -translate-y-1/2 h-1 bg-blue-600 transition-all duration-700 z-0" 
                             style="width: {{ (($currentIdx - 1) / 4) * 100 }}%"></div>

                        <div class="grid grid-cols-1 sm:grid-cols-5 gap-3 relative z-10">
                            @foreach($stages as $stage)
                                @php
                                    $stepIdx = $stage->stepIndex();
                                    $isPassed = $stepIdx <= $currentIdx;
                                    $isCurrent = $stepIdx === $currentIdx;
                                @endphp
                                <div class="flex sm:flex-col items-center sm:text-center gap-3 sm:gap-2 p-3 sm:p-0 rounded-2xl sm:rounded-none bg-slate-50 sm:bg-transparent border border-slate-200/60 sm:border-none">
                                    <div class="w-10 h-10 rounded-full flex items-center justify-center font-bold text-xs shrink-0 transition {{ $isCurrent ? 'bg-blue-600 text-white ring-4 ring-blue-100 shadow-md' : ($isPassed ? 'bg-blue-500 text-white' : 'bg-slate-200 text-slate-400') }}">
                                        @if($isPassed && ! $isCurrent)
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                        @else
                                            {{ $stepIdx }}
                                        @endif
                                    </div>
                                    <div>
                                        <div class="text-xs font-bold {{ $isCurrent ? 'text-blue-600' : ($isPassed ? 'text-slate-900' : 'text-slate-400') }}">
                                            {{ $stage->getLabel() }}
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    {{-- Stage Description Box --}}
                    <div class="mt-6 p-4 bg-slate-50 rounded-2xl border border-slate-200/70 text-xs text-slate-600 flex items-start space-x-2.5">
                        <span class="w-2 h-2 rounded-full bg-blue-600 shrink-0 mt-1"></span>
                        <div>
                            <strong class="text-slate-900">Keterangan Tahap Saat Ini:</strong> {{ $order->campaign->stage->description() }}
                        </div>
                    </div>
                </div>

                {{-- 3. JERSEY SPECIFICATIONS --}}
                <div class="bg-white border border-slate-200/80 rounded-[32px] p-6 sm:p-8 shadow-ios">
                    <h2 class="text-xs font-black uppercase tracking-wider text-slate-400 mb-4">Rincian Item Jersey Dipesan</h2>
                    
                    <div class="space-y-3">
                        @foreach($order->items as $item)
                            <div class="p-4 bg-slate-50 rounded-2xl border border-slate-200/70 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                                <div class="flex items-center space-x-4">
                                    <div class="w-14 h-14 bg-white border border-slate-200 rounded-2xl flex flex-col items-center justify-center font-mono font-black text-blue-600 shadow-sm">
                                        <span class="text-[10px] uppercase text-slate-400">NO</span>
                                        <span class="text-base leading-none">{{ $item->custom_number ?? '-' }}</span>
                                    </div>
                                    <div>
                                        <div class="text-base font-black text-slate-900 uppercase">{{ $item->custom_name ?? 'POLOS (TANPA NAMA)' }}</div>
                                        <div class="text-xs text-slate-500 mt-0.5">
                                            Ukuran: <strong class="text-slate-800">{{ $item->size }}</strong> &bull; Lengan: <strong class="text-slate-800">{{ $item->sleeve_type === 'long' ? 'Panjang' : 'Pendek' }}</strong>
                                        </div>
                                        @if($item->addons_summary !== '-')
                                            <div class="text-xs text-blue-600 mt-1 font-semibold">Add-ons: {{ $item->addons_summary }}</div>
                                        @endif
                                    </div>
                                </div>
                                <div class="text-right">
                                    <div class="text-sm font-mono font-bold text-slate-900">Rp{{ number_format($item->total_price, 0, ',', '.') }}</div>
                                    <div class="text-[11px] text-slate-400">Qty: {{ $item->quantity }} pcs</div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                {{-- 4. CONDITIONAL UPLOAD PELUNASAN FORM (iOS Style) --}}
                @if($order->canUploadRepayment())
                    <div class="bg-blue-50/70 border-2 border-blue-200 rounded-[32px] p-6 sm:p-8 shadow-ios">
                        <div class="flex items-center space-x-2.5 mb-2">
                            <span class="w-2.5 h-2.5 rounded-full bg-blue-600 animate-ping"></span>
                            <span class="text-xs font-black uppercase tracking-wider text-blue-700">Waktunya Pelunasan</span>
                        </div>
                        <h2 class="text-xl font-extrabold text-slate-900">Jersey Telah Masuk QC / Siap Diambil!</h2>
                        <p class="text-slate-600 text-sm mt-1 max-w-xl">
                            Jersey pesananmu hampir selesai dijahit. Silakan transfer sisa pelunasan sebesar <strong class="text-blue-600 font-mono text-base font-bold">Rp{{ number_format($order->remaining_balance, 0, ',', '.') }}</strong> agar pesanan bisa segera diserahkan.
                        </p>

                        @if($repaymentSubmitted)
                            <div class="mt-5 p-4 bg-emerald-50 border border-emerald-200 rounded-2xl text-emerald-700 text-sm font-bold flex items-center space-x-3">
                                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                <span>Bukti pelunasan terkirim! Admin panitia sedang memverifikasi transferanmu.</span>
                            </div>
                        @else
                            <form wire:submit.prevent="submitFinalPayment" class="mt-5 space-y-4">
                                <div class="border-2 border-dashed border-blue-300 rounded-2xl p-5 bg-white text-center relative cursor-pointer">
                                    <input type="file" 
                                           wire:model="finalPaymentProof" 
                                           accept="image/*"
                                           class="absolute inset-0 w-full h-full opacity-0 cursor-pointer">

                                    @if ($finalPaymentProof)
                                        <div class="flex flex-col items-center">
                                            <img src="{{ $finalPaymentProof->temporaryUrl() }}" class="w-20 h-20 rounded-xl object-cover border border-slate-200 shadow-sm mb-2">
                                            <span class="text-xs text-blue-600 font-bold">Bukti Pelunasan Terpilih &check;</span>
                                        </div>
                                    @else
                                        <div class="flex flex-col items-center">
                                            <svg class="w-7 h-7 text-blue-600 mb-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                                            <span class="text-xs font-bold text-slate-800">Unggah Foto Bukti Pelunasan</span>
                                            <span class="text-[10px] text-slate-400">Format JPG/PNG/WebP</span>
                                        </div>
                                    @endif
                                </div>
                                @error('finalPaymentProof') <span class="text-rose-500 text-xs block font-medium">{{ $message }}</span> @enderror

                                <button type="submit" 
                                        wire:loading.attr="disabled"
                                        class="w-full sm:w-auto px-7 py-3.5 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-2xl text-sm transition shadow-md shadow-blue-500/20 active:scale-95">
                                    <span wire:loading.remove wire:target="submitFinalPayment">Kirim Bukti Pelunasan</span>
                                    <span wire:loading wire:target="submitFinalPayment">Mengunggah...</span>
                                </button>
                            </form>
                        @endif
                    </div>
                @endif

            </div>
        @endif

    </main>
</div>
