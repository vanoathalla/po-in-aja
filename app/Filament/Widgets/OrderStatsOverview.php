<?php

declare(strict_types=1);

namespace App\Filament\Widgets;

use App\Enums\PaymentStatus;
use App\Models\Campaign;
use App\Models\Order;
use App\Models\OrderItem;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class OrderStatsOverview extends BaseWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        // 1. Total Dana Masuk (Uang kas panitia yang sudah diverifikasi)
        $totalDanaMasuk = (float) Order::sum('amount_paid');

        // 2. Total Piutang (Total sisa tagihan dari order yang belum lunas)
        $totalPiutang = (float) Order::where('payment_status', '!=', PaymentStatus::PaidOff)
            ->where('payment_status', '!=', PaymentStatus::Unpaid)
            ->selectRaw('SUM(total_amount - amount_paid) as total_piutang')
            ->value('total_piutang') ?? 0.00;

        // 3. Kuota Terkumpul vs Target Konveksi (Untuk campaign aktif)
        $activeCampaign = Campaign::where('is_active', true)->first();
        $totalPcs = 0;
        $targetPcs = 0;
        $progressPct = 0;

        if ($activeCampaign) {
            $totalPcs = $activeCampaign->total_jerseys_ordered;
            $targetPcs = $activeCampaign->min_quota;
            $progressPct = $activeCampaign->quota_progress_percentage;
        } else {
            $totalPcs = (int) OrderItem::whereHas('order', fn ($q) => $q->where('payment_status', '!=', PaymentStatus::Unpaid))->sum('quantity');
            $targetPcs = (int) Campaign::sum('min_quota');
            $progressPct = $targetPcs > 0 ? (int) min(100, round(($totalPcs / $targetPcs) * 100)) : 100;
        }

        return [
            Stat::make('Total Dana Masuk (Kas)', 'Rp ' . number_format($totalDanaMasuk, 0, ',', '.'))
                ->description('Total uang DP & Pelunasan yang telah diverifikasi')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color('success'),

            Stat::make('Total Piutang (Sisa Tagihan)', 'Rp ' . number_format($totalPiutang, 0, ',', '.'))
                ->description('Menunggu pelunasan saat tahap QC / Diambil')
                ->descriptionIcon('heroicon-m-clock')
                ->color('warning'),

            Stat::make('Total Jersey Dipesan', "{$totalPcs} / {$targetPcs} pcs")
                ->description("{$progressPct}% dari kuota minimum vendor konveksi")
                ->descriptionIcon('heroicon-m-sparkles')
                ->color($progressPct >= 100 ? 'success' : 'primary'),
        ];
    }
}
