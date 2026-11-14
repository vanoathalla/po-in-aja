<div style="font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; color: #0f172a; line-height: 1.4; font-size: 11px;">

    {{-- HEADER SHEET --}}
    <div style="border-bottom: 2px solid #2563eb; padding-bottom: 12px; margin-bottom: 16px;">
        <table style="width: 100%; border-collapse: collapse;">
            <tr>
                <td style="vertical-align: top;">
                    <div style="font-size: 18px; font-weight: 900; color: #1e40af; text-transform: uppercase; letter-spacing: 0.5px;">
                        LEMBAR KERJA PRODUKSI KONVEKSI & SABLON
                    </div>
                    <div style="font-size: 13px; font-weight: 700; color: #334155; margin-top: 2px;">
                        CAMPAIGN: {{ strtoupper($campaign->title) }}
                    </div>
                    <div style="font-size: 10px; color: #64748b; margin-top: 3px;">
                        Dicetak pada: {{ now()->format('d M Y, H:i') }} WIB &bull; Sistem PO-in Aja (HMIF Edition)
                    </div>
                </td>
                <td style="text-align: right; vertical-align: top; width: 220px;">
                    <div style="background-color: #eff6ff; border: 1px solid #bfdbfe; border-radius: 8px; padding: 8px 12px; text-align: right;">
                        <div style="font-size: 9px; text-transform: uppercase; font-weight: 700; color: #3b82f6;">TOTAL ITEM JERSEY</div>
                        <div style="font-size: 22px; font-weight: 900; color: #1e3a8a;">{{ $grandTotal }} PCS</div>
                        <div style="font-size: 9px; color: #0284c7; font-weight: 600;">Status PO: {{ $campaign->stage->getLabel() }}</div>
                    </div>
                </td>
            </tr>
        </table>
    </div>

    {{-- BAGIAN 1: REKAP MATRIKS UKURAN --}}
    <div style="margin-bottom: 22px;">
        <div style="font-size: 11px; font-weight: 800; text-transform: uppercase; margin-bottom: 6px; color: #1e40af;">
            1. REKAPITULASI MATRIKS UKURAN & POLA LENGAN
        </div>
        <table style="width: 100%; border-collapse: collapse; text-align: center;">
            <thead>
                <tr style="background-color: #2563eb; color: #ffffff;">
                    <th style="padding: 6px 10px; border: 1px solid #2563eb; text-align: left; font-size: 10px;">UKURAN (SIZE)</th>
                    <th style="padding: 6px 10px; border: 1px solid #2563eb; font-size: 10px;">LENGAN PENDEK (SHORT)</th>
                    <th style="padding: 6px 10px; border: 1px solid #2563eb; font-size: 10px;">LENGAN PANJANG (LONG)</th>
                    <th style="padding: 6px 10px; border: 1px solid #2563eb; font-size: 10px; background-color: #1d4ed8;">SUBTOTAL SIZE</th>
                </tr>
            </thead>
            <tbody>
                @foreach($sizeMatrix as $size => $counts)
                    <tr style="background-color: {{ $loop->even ? '#f8fafc' : '#ffffff' }};">
                        <td style="padding: 6px 10px; border: 1px solid #e2e8f0; text-align: left; font-weight: 800; color: #1e293b;">
                            {{ $size }}
                        </td>
                        <td style="padding: 6px 10px; border: 1px solid #e2e8f0; font-family: monospace; font-size: 12px;">
                            {{ $counts['short'] > 0 ? $counts['short'] : '-' }}
                        </td>
                        <td style="padding: 6px 10px; border: 1px solid #e2e8f0; font-family: monospace; font-size: 12px;">
                            {{ $counts['long'] > 0 ? $counts['long'] : '-' }}
                        </td>
                        <td style="padding: 6px 10px; border: 1px solid #e2e8f0; font-weight: 800; font-family: monospace; font-size: 12px; background-color: #f1f5f9;">
                            {{ $counts['total'] }}
                        </td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr style="background-color: #e2e8f0; font-weight: 900;">
                    <td style="padding: 8px 10px; border: 1px solid #cbd5e1; text-align: left;">TOTAL KESELURUHAN</td>
                    <td style="padding: 8px 10px; border: 1px solid #cbd5e1; font-family: monospace; font-size: 13px;">{{ $totalShort }}</td>
                    <td style="padding: 8px 10px; border: 1px solid #cbd5e1; font-family: monospace; font-size: 13px;">{{ $totalLong }}</td>
                    <td style="padding: 8px 10px; border: 1px solid #cbd5e1; font-family: monospace; font-size: 14px; background-color: #1e40af; color: #ffffff;">
                        {{ $grandTotal }} PCS
                    </td>
                </tr>
            </tfoot>
        </table>
    </div>

    {{-- BAGIAN 2: DAFTAR DETAIL SABLON PUNGGUNG --}}
    <div>
        <div style="font-size: 11px; font-weight: 800; text-transform: uppercase; margin-bottom: 6px; color: #1e40af;">
            2. DAFTAR SABLON NAMA & NOMOR PUNGGUNG (DETAIL CUTTING/PRINT)
        </div>
        <table style="width: 100%; border-collapse: collapse;">
            <thead>
                <tr style="background-color: #1e40af; color: #ffffff; text-align: left;">
                    <th style="padding: 6px 8px; border: 1px solid #1e40af; width: 28px; text-align: center;">NO</th>
                    <th style="padding: 6px 8px; border: 1px solid #1e40af; width: 85px;">INVOICE</th>
                    <th style="padding: 6px 8px; border: 1px solid #1e40af; width: 45px; text-align: center;">SIZE</th>
                    <th style="padding: 6px 8px; border: 1px solid #1e40af; width: 65px; text-align: center;">LENGAN</th>
                    <th style="padding: 6px 8px; border: 1px solid #1e40af;">NAMA PUNGGUNG</th>
                    <th style="padding: 6px 8px; border: 1px solid #1e40af; width: 55px; text-align: center;">NOMOR</th>
                    <th style="padding: 6px 8px; border: 1px solid #1e40af;">ADD-ONS / SPESIFIKASI</th>
                </tr>
            </thead>
            <tbody>
                @forelse($items as $index => $item)
                    <tr style="background-color: {{ $loop->even ? '#f8fafc' : '#ffffff' }};">
                        <td style="padding: 5px 8px; border: 1px solid #e2e8f0; text-align: center; font-weight: bold; color: #64748b;">
                            {{ $index + 1 }}
                        </td>
                        <td style="padding: 5px 8px; border: 1px solid #e2e8f0; font-family: monospace; font-size: 10px; font-weight: bold; color: #2563eb;">
                            {{ $item->order->invoice_code }}
                        </td>
                        <td style="padding: 5px 8px; border: 1px solid #e2e8f0; text-align: center; font-weight: 800;">
                            {{ $item->size }}
                        </td>
                        <td style="padding: 5px 8px; border: 1px solid #e2e8f0; text-align: center; font-size: 10px;">
                            {{ $item->sleeve_type === 'long' ? 'PANJANG' : 'PENDEK' }}
                        </td>
                        <td style="padding: 5px 8px; border: 1px solid #e2e8f0; font-weight: 900; text-transform: uppercase; font-size: 11px; color: #0f172a;">
                            {{ $item->custom_name ?: '(POLOS / TANPA NAMA)' }}
                        </td>
                        <td style="padding: 5px 8px; border: 1px solid #e2e8f0; text-align: center; font-weight: 900; font-size: 13px; font-family: monospace; color: #1e40af;">
                            {{ $item->custom_number ?: '-' }}
                        </td>
                        <td style="padding: 5px 8px; border: 1px solid #e2e8f0; font-size: 10px; color: #475569;">
                            {{ $item->addons_summary }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" style="padding: 16px; text-align: center; color: #94a3b8;">
                            Belum ada pesanan jersey yang terverifikasi untuk campaign ini.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- FOOTER SIGNATURE --}}
    <div style="margin-top: 30px; page-break-inside: avoid;">
        <table style="width: 100%;">
            <tr>
                <td style="width: 50%; text-align: center; vertical-align: top;">
                    <div style="font-size: 10px; color: #64748b;">Diserahkan oleh (Panitia HMIF):</div>
                    <div style="height: 45px;"></div>
                    <div style="font-weight: bold; border-top: 1px dashed #cbd5e1; display: inline-block; padding-top: 4px; width: 160px;">
                        ( PIC Panitia IF )
                    </div>
                </td>
                <td style="width: 50%; text-align: center; vertical-align: top;">
                    <div style="font-size: 10px; color: #64748b;">Diterima oleh (Vendor Konveksi):</div>
                    <div style="height: 45px;"></div>
                    <div style="font-weight: bold; border-top: 1px dashed #cbd5e1; display: inline-block; padding-top: 4px; width: 160px;">
                        ( PIC Konveksi / Jahit )
                    </div>
                </td>
            </tr>
        </table>
    </div>
</div>
