<?php

declare(strict_types=1);

namespace App\Services;

use App\Enums\PaymentStatus;
use App\Exports\VendorProductionSheetExport;
use App\Models\Campaign;
use App\Models\OrderItem;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Response;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class VendorProductionSheetExporter
{
    protected array $standardSizes = ['XS', 'S', 'M', 'L', 'XL', 'XXL', '3XL', '4XL'];

    public function generateSizeMatrix(Campaign $campaign): array
    {
        $items = OrderItem::with('order')
            ->whereHas('order', function ($query) use ($campaign) {
                $query->where('campaign_id', $campaign->id)
                    ->whereIn('payment_status', [PaymentStatus::DpPaid, PaymentStatus::PaidOff]);
            })
            ->get();

        $matrix = [];
        $totalShort = 0;
        $totalLong = 0;

        foreach ($this->standardSizes as $size) {
            $shortCount = $items->where('size', $size)->where('sleeve_type', 'short')->sum('quantity');
            $longCount = $items->where('size', $size)->where('sleeve_type', 'long')->sum('quantity');

            $matrix[$size] = [
                'short' => $shortCount,
                'long' => $longCount,
                'total' => $shortCount + $longCount,
            ];

            $totalShort += $shortCount;
            $totalLong += $longCount;
        }

        return [
            'matrix' => $matrix,
            'totalShort' => $totalShort,
            'totalLong' => $totalLong,
            'grandTotal' => $totalShort + $totalLong,
            'items' => $items,
        ];
    }

    public function downloadPdf(Campaign $campaign): Response
    {
        $data = $this->generateSizeMatrix($campaign);

        $pdf = Pdf::loadView('exports.vendor-sheet-pdf', [
            'campaign' => $campaign,
            'sizeMatrix' => $data['matrix'],
            'totalShort' => $data['totalShort'],
            'totalLong' => $data['totalLong'],
            'grandTotal' => $data['grandTotal'],
            'items' => $data['items'],
        ])->setPaper('a4', 'portrait');

        $filename = sprintf('LEMBAR-KONVEKSI-%s-%s.pdf', Str::slug($campaign->title), date('Ymd-Hi'));

        return $pdf->download($filename);
    }

    public function downloadExcel(Campaign $campaign): BinaryFileResponse
    {
        $filename = sprintf('REKAP-KONVEKSI-%s-%s.xlsx', Str::slug($campaign->title), date('Ymd-Hi'));

        return Excel::download(new VendorProductionSheetExport($campaign), $filename);
    }
}
