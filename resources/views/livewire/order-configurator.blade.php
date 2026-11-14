<div x-data="{
        name: @entangle('customName').live,
        number: @entangle('customNumber').live,
        sleeve: @entangle('sleeveType').live,
        copied: false,
        copyInvoice(code) {
            navigator.clipboard.writeText(code);
            this.copied = true;
            setTimeout(() => this.copied = false, 2500);
        }
    }" 
    class="min-h-screen bg-[#F8FAFC] text-slate-800 font-sans antialiased pb-24">

    {{-- iOS Glassmorphism Header --}}
    <header class="border-b border-slate-200/80 bg-white/80 ios-blur sticky top-0 z-30 transition-all">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-3.5 flex items-center justify-between">
            <div class="flex items-center space-x-3">
                <div class="w-8 h-8 rounded-xl bg-blue-600 text-white flex items-center justify-center font-black text-xs shadow-md shadow-blue-500/20">
                    IF
                </div>
                <div>
                    <span class="text-xs uppercase tracking-wider font-extrabold text-blue-600 block leading-tight">PO-IN AJA</span>
                    <span class="text-[11px] font-semibold text-slate-400 block leading-tight">Teknik Informatika Edition</span>
                </div>
            </div>
            <div class="flex items-center space-x-2">
                <a href="/admin" target="_blank" class="text-xs font-bold text-slate-500 hover:text-blue-600 transition flex items-center space-x-1.5 px-3 py-1.5 rounded-full hover:bg-slate-100">
                    <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                    <span>Admin Panel</span>
                </a>
                <a href="/track" class="text-xs font-semibold text-slate-600 hover:text-blue-600 transition flex items-center space-x-1.5 bg-slate-100 hover:bg-slate-200/70 px-3.5 py-1.5 rounded-full border border-slate-200/60 shadow-ios-pill">
                    <svg class="w-3.5 h-3.5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    <span>Cek Status PO</span>
                </a>
            </div>
        </div>
    </header>

    @if($isSubmitted)
        {{-- SUCCESS MODAL / SCREEN (iOS Style) --}}
        <main class="max-w-xl mx-auto px-4 py-16 sm:py-24 text-center">
            <div class="bg-white border border-slate-200/80 rounded-[32px] p-8 sm:p-12 shadow-ios-lg relative overflow-hidden">
                <div class="w-20 h-20 bg-blue-50 border border-blue-100 rounded-full flex items-center justify-center mx-auto mb-6 text-blue-600 shadow-sm">
                    <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                </div>

                <span class="text-[11px] font-black uppercase tracking-wider text-blue-600 bg-blue-50 px-3.5 py-1 rounded-full border border-blue-100">Slot Jersey Berhasil Dipesan</span>
                <h1 class="text-2xl sm:text-3xl font-black text-slate-900 mt-3">Terima Kasih, {{ $customerName }}!</h1>
                <p class="text-slate-500 text-sm mt-2 leading-relaxed">Bukti transfer pesanan jersey Teknik Informatika kamu sudah tersimpan. Panitia akan segera memverifikasi pembayaranmu.</p>

                {{-- Invoice Code Box --}}
                <div class="mt-8 bg-slate-50 border border-slate-200/80 rounded-2xl p-5 flex flex-col sm:flex-row items-center justify-between gap-4">
                    <div class="text-left">
                        <div class="text-[11px] uppercase tracking-wider text-slate-400 font-bold">Kode Invoice Publik</div>
                        <div class="text-2xl font-mono font-black text-blue-600 tracking-wider">{{ $generatedInvoice }}</div>
                    </div>
                    <button type="button" 
                            @click="copyInvoice('{{ $generatedInvoice }}')"
                            class="w-full sm:w-auto px-5 py-2.5 bg-white hover:bg-slate-100 text-slate-700 rounded-xl font-bold text-xs flex items-center justify-center space-x-2 border border-slate-200 shadow-sm transition active:scale-95">
                        <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                        <span x-text="copied ? 'Tersalin!' : 'Salin Invoice'"></span>
                    </button>
                </div>

                <div class="mt-8 flex flex-col sm:flex-row gap-3">
                    <a href="/track?invoice={{ $generatedInvoice }}" class="w-full py-3.5 px-6 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-2xl text-sm transition shadow-lg shadow-blue-500/20 flex items-center justify-center space-x-2 active:scale-95">
                        <span>Pantau Progres Produksi</span>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                    </a>
                    <button type="button" wire:click="$set('isSubmitted', false)" class="w-full py-3.5 px-6 bg-slate-100 hover:bg-slate-200/70 text-slate-600 font-bold rounded-2xl text-sm transition">
                        Pesan Item Tambahan
                    </button>
                </div>
            </div>
        </main>
    @else
        <main class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-8 sm:py-10">
            
            {{-- Campaign Header Card (iOS Clean White) --}}
            <div class="mb-8 bg-white border border-slate-200/80 rounded-[32px] p-6 sm:p-8 shadow-ios flex flex-col md:flex-row md:items-center justify-between gap-6">
                <div>
                    <div class="inline-flex items-center space-x-2 bg-blue-50 border border-blue-100/80 px-3 py-1 rounded-full text-xs font-bold text-blue-600 mb-3">
                        <span class="w-2 h-2 rounded-full bg-blue-600 animate-pulse"></span>
                        <span>Pre-Order Angkatan & Komunitas</span>
                    </div>
                    <h1 class="text-2xl sm:text-3xl lg:text-4xl font-extrabold tracking-tight text-slate-900">
                        {{ $campaign->title }}
                    </h1>
                    <p class="text-slate-500 text-sm max-w-2xl mt-1.5 leading-relaxed">
                        {{ $campaign->description }}
                    </p>
                </div>

                {{-- iOS Quota Progress Badge --}}
                <div class="bg-slate-50 border border-slate-200/70 rounded-2xl p-4 flex items-center space-x-4 min-w-[220px] shrink-0">
                    <div class="w-12 h-12 rounded-xl bg-blue-600 text-white font-black text-lg flex items-center justify-center shadow-md shadow-blue-500/25">
                        {{ $campaign->quota_progress_percentage }}%
                    </div>
                    <div>
                        <div class="text-[11px] uppercase tracking-wider text-slate-400 font-bold">Target Konveksi</div>
                        <div class="text-sm font-extrabold text-slate-800">
                            <span class="text-blue-600">{{ $campaign->total_jerseys_ordered }}</span> / {{ $campaign->min_quota }} Jersey
                        </div>
                    </div>
                </div>
            </div>

            <form wire:submit.prevent="submitOrder" class="grid grid-cols-1 lg:grid-cols-12 gap-8">
                
                {{-- LEFT COLUMN: INTERACTIVE SVG BACK JERSEY PREVIEW (Sticky iOS Card) --}}
                <div class="lg:col-span-5 flex flex-col items-center">
                    <div class="sticky top-20 w-full bg-white border border-slate-200/80 rounded-[32px] p-6 sm:p-7 shadow-ios">
                        
                        {{-- Header Preview with iOS Segmented Switcher --}}
                        <div class="flex items-center justify-between mb-4">
                            <div>
                                <span class="text-xs font-black uppercase tracking-wider text-slate-400">Live Preview</span>
                                <h3 class="text-sm font-bold text-slate-800">Tampak Belakang</h3>
                            </div>
                            <span class="text-[11px] font-mono font-bold px-2.5 py-1 rounded-full bg-blue-50 text-blue-600 border border-blue-100" 
                                  x-text="sleeve === 'long' ? 'LENGAN PANJANG' : 'LENGAN PENDEK'"></span>
                        </div>

                        {{-- THE SVG INFORMATIKA JERSEY BACK VIEW --}}
                        <div class="relative w-full aspect-[4/5] flex items-center justify-center bg-gradient-to-b from-slate-50 via-slate-100/60 to-slate-200/40 rounded-2xl border border-slate-200/80 p-3 shadow-inner overflow-hidden">
                            <svg class="w-full h-full max-h-[370px] drop-shadow-[0_12px_24px_rgba(30,58,138,0.18)]" viewBox="0 0 500 600" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <defs>
                                    {{-- Jersey Body White Gradient --}}
                                    <linearGradient id="jerseyWhiteBody" x1="250" y1="50" x2="250" y2="580" gradientUnits="userSpaceOnUse">
                                        <stop offset="0%" stop-color="#FFFFFF"/>
                                        <stop offset="80%" stop-color="#F8FAFC"/>
                                        <stop offset="100%" stop-color="#F1F5F9"/>
                                    </linearGradient>

                                    {{-- Informatika Royal Blue Gradient for Sleeves & Shoulders --}}
                                    <linearGradient id="informatikaBlue" x1="100" y1="100" x2="400" y2="400" gradientUnits="userSpaceOnUse">
                                        <stop offset="0%" stop-color="#2563EB"/>
                                        <stop offset="100%" stop-color="#1D4ED8"/>
                                    </linearGradient>

                                    <linearGradient id="skyAccent" x1="0" y1="0" x2="500" y2="600" gradientUnits="userSpaceOnUse">
                                        <stop offset="0%" stop-color="#38BDF8"/>
                                        <stop offset="100%" stop-color="#0284C7"/>
                                    </linearGradient>
                                </defs>

                                {{-- Long Sleeve Extension (Toggled via Alpine) --}}
                                <g x-show="sleeve === 'long'" x-transition:enter="transition ease-out duration-300">
                                    {{-- Left Long Sleeve --}}
                                    <path d="M70 170 L25 350 L75 365 L115 190 Z" fill="url(#informatikaBlue)"/>
                                    <path d="M25 350 L75 365" stroke="#38BDF8" stroke-width="6"/>
                                    {{-- Right Long Sleeve --}}
                                    <path d="M430 170 L475 350 L425 365 L385 190 Z" fill="url(#informatikaBlue)"/>
                                    <path d="M475 350 L425 365" stroke="#38BDF8" stroke-width="6"/>
                                </g>

                                {{-- Short Sleeves (Royal Blue) --}}
                                <g>
                                    {{-- Left Short Sleeve --}}
                                    <path d="M140 100 L70 170 L115 220 L160 170 Z" fill="url(#informatikaBlue)"/>
                                    <path d="M70 170 L115 220" stroke="#38BDF8" stroke-width="4"/>
                                    {{-- Right Short Sleeve --}}
                                    <path d="M360 100 L430 170 L385 220 L340 170 Z" fill="url(#informatikaBlue)"/>
                                    <path d="M430 170 L385 220" stroke="#38BDF8" stroke-width="4"/>
                                </g>

                                {{-- Jersey Main Body Silhouette (Clean White) --}}
                                <path d="M160 100 
                                         C190 120, 310 120, 340 100 
                                         L380 170 
                                         L360 480 
                                         C360 520, 140 520, 140 480 
                                         L120 170 Z" 
                                      fill="url(#jerseyWhiteBody)" 
                                      stroke="#E2E8F0" 
                                      stroke-width="2.5"/>

                                {{-- Teknik Informatika Side Panels (Royal Blue with subtle cyber stripes) --}}
                                <path d="M120 170 L140 480 C155 480, 160 470, 155 170 Z" fill="#2563EB" opacity="0.12"/>
                                <path d="M380 170 L360 480 C345 480, 340 470, 345 170 Z" fill="#2563EB" opacity="0.12"/>
                                <line x1="130" y1="200" x2="148" y2="460" stroke="#2563EB" stroke-width="2" stroke-dasharray="4 4" opacity="0.4"/>
                                <line x1="370" y1="200" x2="352" y2="460" stroke="#2563EB" stroke-width="2" stroke-dasharray="4 4" opacity="0.4"/>

                                {{-- Raglan Shoulder Lines --}}
                                <path d="M160 100 L180 170" stroke="#CBD5E1" stroke-width="2" stroke-linecap="round"/>
                                <path d="M340 100 L320 170" stroke="#CBD5E1" stroke-width="2" stroke-linecap="round"/>

                                {{-- Collar Ribbing Back (Royal Blue & Cyan Tape) --}}
                                <path d="M195 105 C220 118, 280 118, 305 105" stroke="#1D4ED8" stroke-width="6" stroke-linecap="round" fill="none"/>
                                <path d="M210 112 C230 120, 270 120, 290 112" stroke="#38BDF8" stroke-width="2.5" stroke-linecap="round" fill="none"/>

                                {{-- "INFORMATIKA" / Sub-title on Upper Neck Arc --}}
                                <text x="250" y="150" 
                                      text-anchor="middle" 
                                      fill="#64748B" 
                                      font-family="system-ui, -apple-system, sans-serif" 
                                      font-size="11" 
                                      font-weight="800" 
                                      letter-spacing="4"
                                      class="select-none uppercase">INFORMATICS</text>

                                {{-- LIVE CUSTOM NAME (ALPINE.JS x-text) in Royal Blue --}}
                                <text x="250" y="210" 
                                      text-anchor="middle" 
                                      fill="#1E3A8A" 
                                      font-family="Impact, 'Bebas Neue', 'Arial Black', sans-serif" 
                                      font-size="34" 
                                      letter-spacing="4"
                                      class="select-none uppercase font-black"
                                      x-text="name ? name.toUpperCase() : 'NAMA ANDA'"></text>

                                {{-- LIVE CUSTOM NUMBER (ALPINE.JS x-text) in Electric/Royal Blue --}}
                                <text x="250" y="340" 
                                      text-anchor="middle" 
                                      dominant-baseline="central"
                                      fill="#2563EB" 
                                      font-family="Impact, 'Bebas Neue', 'Arial Black', sans-serif" 
                                      font-size="135" 
                                      class="select-none font-black drop-shadow-[0_6px_12px_rgba(37,99,235,0.22)]"
                                      x-text="number ? number : '00'"></text>

                                {{-- Lower Hem Tag - "HMIF AUTHENTIC" --}}
                                <rect x="300" y="450" width="48" height="22" rx="4" fill="#EFF6FF" stroke="#BFDBFE"/>
                                <text x="324" y="464" text-anchor="middle" fill="#1D4ED8" font-size="8" font-weight="bold" font-family="monospace">HMIF.ORIGINAL</text>
                            </svg>
                        </div>

                        {{-- iOS Style Sleeve Segmented Control --}}
                        <div class="mt-5 p-1.5 bg-slate-100 rounded-2xl flex border border-slate-200/60">
                            <button type="button" 
                                    wire:click="$set('sleeveType', 'short')"
                                    class="w-1/2 py-2.5 rounded-xl font-bold text-xs transition flex items-center justify-center space-x-1.5 {{ $sleeveType === 'short' ? 'bg-white text-blue-600 shadow-sm' : 'text-slate-500 hover:text-slate-800' }}">
                                <span>Lengan Pendek</span>
                                <span class="text-[10px] opacity-75">(Standar)</span>
                            </button>
                            <button type="button" 
                                    wire:click="$set('sleeveType', 'long')"
                                    class="w-1/2 py-2.5 rounded-xl font-bold text-xs transition flex items-center justify-center space-x-1.5 {{ $sleeveType === 'long' ? 'bg-white text-blue-600 shadow-sm' : 'text-slate-500 hover:text-slate-800' }}">
                                <span>Lengan Panjang</span>
                                <span class="text-[10px] text-blue-600 font-bold">+15k</span>
                            </button>
                        </div>
                    </div>
                </div>

                {{-- RIGHT COLUMN: CONFIGURATOR FORM --}}
                <div class="lg:col-span-7 space-y-6">
                    
                    {{-- 1. KUSTOMISASI SABLON PUNGGUNG --}}
                    <div class="bg-white border border-slate-200/80 rounded-[32px] p-6 sm:p-8 shadow-ios space-y-6">
                        <div class="flex items-center space-x-3 border-b border-slate-100 pb-4">
                            <span class="w-7 h-7 rounded-full bg-blue-600 text-white font-bold text-xs flex items-center justify-center shadow-sm">1</span>
                            <div>
                                <h2 class="text-base font-extrabold text-slate-900">Kustomisasi Sablon Punggung</h2>
                                <p class="text-xs text-slate-400">Preview di sebelah kiri otomatis berubah saat diketik</p>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            {{-- Nama Punggung --}}
                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1.5">
                                    Nama Punggung (Maks. 12 Karakter)
                                </label>
                                <input type="text" 
                                       wire:model.live.debounce.100ms="customName"
                                       maxlength="12"
                                       placeholder="Contoh: ALIFIAN / HACKER"
                                       class="w-full bg-slate-50 border border-slate-200 rounded-2xl px-4 py-3 text-slate-900 placeholder-slate-400 focus:bg-white focus:outline-none focus:border-blue-600 focus:ring-4 focus:ring-blue-100 font-mono uppercase tracking-wider text-sm transition">
                                @error('customName') <span class="text-rose-500 text-xs mt-1 block font-medium">{{ $message }}</span> @enderror
                            </div>

                            {{-- Nomor Punggung --}}
                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1.5">
                                    Nomor Punggung (Maks. 2 Digit)
                                </label>
                                <input type="text" 
                                       wire:model.live.debounce.100ms="customNumber"
                                       maxlength="2"
                                       placeholder="Contoh: 01 / 404"
                                       class="w-full bg-slate-50 border border-slate-200 rounded-2xl px-4 py-3 text-slate-900 placeholder-slate-400 focus:bg-white focus:outline-none focus:border-blue-600 focus:ring-4 focus:ring-blue-100 font-mono text-sm transition">
                                @error('customNumber') <span class="text-rose-500 text-xs mt-1 block font-medium">{{ $message }}</span> @enderror
                            </div>
                        </div>

                        {{-- Pilihan Ukuran Jersey (iOS Style Pills) --}}
                        <div>
                            <div class="flex items-center justify-between mb-2">
                                <label class="block text-xs font-bold text-slate-700">
                                    Pilih Ukuran Jersey
                                </label>
                                <span class="text-[11px] text-slate-400">XXL ke atas ada penyesuaian kain</span>
                            </div>
                            <div class="grid grid-cols-4 sm:grid-cols-8 gap-2">
                                @foreach(['XS', 'S', 'M', 'L', 'XL', 'XXL', '3XL', '4XL'] as $sz)
                                    <button type="button"
                                            wire:click="$set('size', '{{ $sz }}')"
                                            class="py-3 px-2 rounded-2xl border text-center font-bold text-xs transition {{ $size === $sz ? 'bg-blue-600 text-white border-blue-600 shadow-md shadow-blue-500/20' : 'bg-slate-50 border-slate-200 text-slate-700 hover:bg-slate-100' }}">
                                        {{ $sz }}
                                        @if(in_array($sz, ['XXL', '3XL', '4XL']))
                                            <span class="block text-[9px] font-mono leading-none mt-1 opacity-75">+Extra</span>
                                        @endif
                                    </button>
                                @endforeach
                            </div>
                        </div>

                        {{-- Add-ons Checklist --}}
                        @if($campaign->addons->where('is_active', true)->isNotEmpty())
                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-2">
                                    Opsi Add-on Tambahan (Opsional)
                                </label>
                                <div class="space-y-2">
                                    @foreach($campaign->addons->where('is_active', true) as $addon)
                                        <label class="flex items-center justify-between p-3.5 bg-slate-50 border border-slate-200/80 rounded-2xl cursor-pointer hover:bg-slate-100/70 transition">
                                            <div class="flex items-center space-x-3">
                                                <input type="checkbox" 
                                                       wire:model.live="selectedAddonIds" 
                                                       value="{{ $addon->id }}"
                                                       class="w-4 h-4 rounded border-slate-300 text-blue-600 focus:ring-blue-500">
                                                <span class="text-xs font-bold text-slate-800">{{ $addon->name }}</span>
                                            </div>
                                            <span class="text-xs font-mono font-bold text-blue-600">+Rp{{ number_format($addon->price, 0, ',', '.') }}</span>
                                        </label>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </div>

                    {{-- 2. DATA IDENTITAS PEMESAN --}}
                    <div class="bg-white border border-slate-200/80 rounded-[32px] p-6 sm:p-8 shadow-ios space-y-4">
                        <div class="flex items-center space-x-3 border-b border-slate-100 pb-4">
                            <span class="w-7 h-7 rounded-full bg-blue-600 text-white font-bold text-xs flex items-center justify-center shadow-sm">2</span>
                            <div>
                                <h2 class="text-base font-extrabold text-slate-900">Data Identitas Mahasiswa</h2>
                                <p class="text-xs text-slate-400">Untuk verifikasi dan pengiriman jersey</p>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1.5">Nama Lengkap *</label>
                                <input type="text" wire:model="customerName" placeholder="Contoh: Alifian Pratama" class="w-full bg-slate-50 border border-slate-200 rounded-2xl px-4 py-3 text-slate-900 text-sm focus:bg-white focus:outline-none focus:border-blue-600 focus:ring-4 focus:ring-blue-100 transition">
                                @error('customerName') <span class="text-rose-500 text-xs mt-1 block font-medium">{{ $message }}</span> @enderror
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1.5">Nomor WhatsApp Aktif *</label>
                                <input type="text" wire:model="customerPhone" placeholder="08xxxxxxxxxx" class="w-full bg-slate-50 border border-slate-200 rounded-2xl px-4 py-3 text-slate-900 text-sm focus:bg-white focus:outline-none focus:border-blue-600 focus:ring-4 focus:ring-blue-100 transition">
                                @error('customerPhone') <span class="text-rose-500 text-xs mt-1 block font-medium">{{ $message }}</span> @enderror
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1.5">Alamat Pengiriman / Ambil di Sekretariat</label>
                            <textarea wire:model="shippingAddress" rows="2" placeholder="Kosongkan jika ingin mengambil langsung di Sekre HMIF" class="w-full bg-slate-50 border border-slate-200 rounded-2xl px-4 py-3 text-slate-900 text-sm focus:bg-white focus:outline-none focus:border-blue-600 focus:ring-4 focus:ring-blue-100 transition"></textarea>
                        </div>
                    </div>

                    {{-- 3. SKEMA PEMBAYARAN (SPLIT DP VS LUNAS) --}}
                    <div class="bg-white border border-slate-200/80 rounded-[32px] p-6 sm:p-8 shadow-ios space-y-6">
                        <div class="flex items-center space-x-3 border-b border-slate-100 pb-4">
                            <span class="w-7 h-7 rounded-full bg-blue-600 text-white font-bold text-xs flex items-center justify-center shadow-sm">3</span>
                            <div>
                                <h2 class="text-base font-extrabold text-slate-900">Skema & Bukti Transfer</h2>
                                <p class="text-xs text-slate-400">Pilih opsi pembayaran DP atau Lunas langsung</p>
                            </div>
                        </div>

                        {{-- Calculation Breakdown Card --}}
                        <div class="bg-slate-50 border border-slate-200/80 rounded-2xl p-4 space-y-2.5">
                            <div class="flex justify-between text-xs text-slate-500">
                                <span>Harga Dasar Jersey:</span>
                                <span class="font-mono font-semibold text-slate-800">Rp{{ number_format($campaign->base_price, 0, ',', '.') }}</span>
                            </div>
                            @if($this->sizeSurcharge > 0)
                                <div class="flex justify-between text-xs text-slate-500">
                                    <span>Penyesuaian Ukuran ({{ $size }}):</span>
                                    <span class="font-mono font-semibold text-blue-600">+Rp{{ number_format($this->sizeSurcharge, 0, ',', '.') }}</span>
                                </div>
                            @endif
                            @if($this->sleeveSurcharge > 0)
                                <div class="flex justify-between text-xs text-slate-500">
                                    <span>Opsi Lengan Panjang:</span>
                                    <span class="font-mono font-semibold text-blue-600">+Rp{{ number_format($this->sleeveSurcharge, 0, ',', '.') }}</span>
                                </div>
                            @endif
                            @if($this->addonsTotalPerItem > 0)
                                <div class="flex justify-between text-xs text-slate-500">
                                    <span>Add-ons Terpilih:</span>
                                    <span class="font-mono font-semibold text-blue-600">+Rp{{ number_format($this->addonsTotalPerItem, 0, ',', '.') }}</span>
                                </div>
                            @endif
                            <div class="border-t border-slate-200/80 pt-3 flex justify-between items-center text-sm font-bold">
                                <span class="text-slate-900">Total Harga Jersey:</span>
                                <span class="text-xl font-mono font-extrabold text-blue-600">Rp{{ number_format($this->totalAmount, 0, ',', '.') }}</span>
                            </div>
                        </div>

                        {{-- iOS Style Split Choice Cards --}}
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                            {{-- Option DP 50% --}}
                            <label class="cursor-pointer border-2 rounded-2xl p-4 flex flex-col justify-between transition {{ $paymentType === 'dp' ? 'bg-blue-50/60 border-blue-600 text-slate-900 shadow-sm' : 'bg-slate-50/50 border-slate-200/80 text-slate-600 hover:border-slate-300' }}">
                                <div>
                                    <div class="flex items-center justify-between mb-1.5">
                                        <div class="flex items-center space-x-2">
                                            <input type="radio" wire:model.live="paymentType" value="dp" class="sr-only">
                                            <span class="text-xs font-black uppercase tracking-wide">Down Payment (DP 50%)</span>
                                        </div>
                                        <span class="text-[10px] font-bold px-2 py-0.5 rounded-full bg-blue-100 text-blue-700">DP Dulu</span>
                                    </div>
                                    <p class="text-[11px] text-slate-500">Bayar separuh sekarang, sisa tagihan saat jersey selesai QC.</p>
                                </div>
                                <div class="mt-4 pt-2.5 border-t border-slate-200/60">
                                    <div class="text-[10px] text-slate-400 font-bold uppercase">Bayar Sekarang:</div>
                                    <div class="text-lg font-black font-mono text-blue-600">Rp{{ number_format($this->downPaymentAmount, 0, ',', '.') }}</div>
                                </div>
                            </label>

                            {{-- Option Lunas 100% --}}
                            <label class="cursor-pointer border-2 rounded-2xl p-4 flex flex-col justify-between transition {{ $paymentType === 'full' ? 'bg-blue-50/60 border-blue-600 text-slate-900 shadow-sm' : 'bg-slate-50/50 border-slate-200/80 text-slate-600 hover:border-slate-300' }}">
                                <div>
                                    <div class="flex items-center justify-between mb-1.5">
                                        <div class="flex items-center space-x-2">
                                            <input type="radio" wire:model.live="paymentType" value="full" class="sr-only">
                                            <span class="text-xs font-black uppercase tracking-wide">Bayar Lunas (100%)</span>
                                        </div>
                                        <span class="text-[10px] font-bold px-2 py-0.5 rounded-full bg-emerald-100 text-emerald-700">Bebas Pikir</span>
                                    </div>
                                    <p class="text-[11px] text-slate-500">Lunasi penuh hari ini, tinggal tunggu jersey jadi tanpa tagihan.</p>
                                </div>
                                <div class="mt-4 pt-2.5 border-t border-slate-200/60">
                                    <div class="text-[10px] text-slate-400 font-bold uppercase">Bayar Sekarang:</div>
                                    <div class="text-lg font-black font-mono text-slate-900">Rp{{ number_format($this->totalAmount, 0, ',', '.') }}</div>
                                </div>
                            </label>
                        </div>

                        {{-- Rekening Transfer Panitia --}}
                        <div class="bg-slate-50 border border-slate-200/80 rounded-2xl p-4 flex items-center space-x-4">
                            <div class="w-12 h-12 rounded-2xl bg-blue-600 text-white flex items-center justify-center font-bold text-sm shadow-sm shrink-0">
                                BCA
                            </div>
                            <div>
                                <div class="text-[11px] uppercase tracking-wider text-slate-400 font-bold">Transfer Resmi Bendahara HMIF:</div>
                                <div class="text-base font-extrabold text-slate-900 font-mono tracking-wide">
                                    {{ $campaign->bank_account_number ?? '8219-0291-88' }}
                                </div>
                                <div class="text-xs text-slate-500">a.n. {{ $campaign->bank_account_holder ?? 'Bendahara Panitia Jersey' }}</div>
                            </div>
                        </div>

                        {{-- Upload Bukti Transfer --}}
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1.5">
                                Unggah Foto Bukti Transfer (JPG/PNG maks 5MB) *
                            </label>
                            
                            <div class="border-2 border-dashed border-slate-200 hover:border-blue-500 rounded-2xl p-5 text-center bg-slate-50/50 hover:bg-blue-50/30 transition relative cursor-pointer">
                                <input type="file" 
                                       wire:model="paymentProof" 
                                       accept="image/*"
                                       class="absolute inset-0 w-full h-full opacity-0 cursor-pointer">

                                @if ($paymentProof)
                                    <div class="flex flex-col items-center">
                                        <img src="{{ $paymentProof->temporaryUrl() }}" class="w-20 h-20 rounded-xl object-cover border border-slate-200 shadow-sm mb-2">
                                        <span class="text-xs font-bold text-blue-600">Bukti Berhasil Dipilih &check;</span>
                                        <span class="text-[10px] text-slate-400">Klik untuk mengganti foto</span>
                                    </div>
                                @else
                                    <div class="flex flex-col items-center justify-center py-2">
                                        <div class="w-10 h-10 rounded-full bg-blue-50 text-blue-600 flex items-center justify-center mb-2">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                        </div>
                                        <span class="text-xs font-bold text-slate-700">Pilih atau Drag Bukti Transfer</span>
                                        <span class="text-[11px] text-slate-400 mt-0.5">Format JPG/PNG/WebP</span>
                                    </div>
                                @endif
                            </div>
                            <div wire:loading wire:target="paymentProof" class="text-xs text-blue-600 mt-1.5 font-semibold">
                                Memproses file bukti...
                            </div>
                            @error('paymentProof') <span class="text-rose-500 text-xs mt-1 block font-medium">{{ $message }}</span> @enderror
                        </div>

                        {{-- iOS Blue CTA Button --}}
                        <button type="submit" 
                                wire:loading.attr="disabled"
                                class="w-full py-4 px-8 bg-blue-600 hover:bg-blue-700 disabled:opacity-50 text-white font-extrabold rounded-2xl text-sm uppercase tracking-wider transition shadow-lg shadow-blue-500/25 active:scale-[0.99] flex items-center justify-center space-x-2">
                            <span wire:loading.remove wire:target="submitOrder">Amankan Slot Jersey Informatika</span>
                            <span wire:loading wire:target="submitOrder" class="flex items-center space-x-2">
                                <svg class="animate-spin h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                                </svg>
                                <span>Menyimpan Pesanan...</span>
                            </span>
                        </button>
                    </div>

                </div>
            </form>
        </main>
    @endif
</div>
