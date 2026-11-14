<?php

declare(strict_types=1);

namespace App\Exports;

use App\Enums\PaymentStatus;
use App\Models\Campaign;
use App\Models\OrderItem;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class VendorProductionSheetExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithStyles, WithTitle
{
    use Exportable;

    protected Campaign $campaign;
    private int $rowNumber = 0;

    public function __construct(Campaign $campaign)
    {
        $this->campaign = $campaign;
    }

    public function collection()
    {
        return OrderItem::with('order')
            ->whereHas('order', function ($query) {
                $query->where('campaign_id', $this->campaign->id)
                    ->whereIn('payment_status', [PaymentStatus::DpPaid, PaymentStatus::PaidOff]);
            })
            ->orderBy('size')
            ->orderBy('custom_number')
            ->get();
    }

    public function headings(): array
    {
        return [
            'NO',
            'KODE INVOICE',
            'UKURAN (SIZE)',
            'TIPE LENGAN',
            'NAMA PUNGGUNG',
            'NOMOR PUNGGUNG',
            'ADD-ONS / SPESIFIKASI TAMBAHAN',
            'NAMA PEMESAN',
            'WHATSAPP',
            'STATUS PEMBAYARAN',
        ];
    }

    /**
     * @param OrderItem $row
     */
    public function map($row): array
    {
        $this->rowNumber++;

        return [
            $this->rowNumber,
            $row->order->invoice_code,
            $row->size,
            $row->sleeve_type === 'long' ? 'Lengan Panjang' : 'Lengan Pendek',
            $row->custom_name ?: '(Polos)',
            $row->custom_number ?: '-',
            $row->addons_summary,
            $row->order->customer_name,
            $row->order->customer_phone,
            $row->order->payment_status->getLabel(),
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => [
                'font' => ['bold' => true, 'color' => ['argb' => 'FFFFFF']],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['argb' => '111827'],
                ],
            ],
        ];
    }

    public function title(): string
    {
        return 'Rekap Konveksi';
    }
}
