<?php

declare(strict_types=1);

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;

enum ProductionStage: string implements HasLabel, HasColor, HasIcon
{
    case OpenPo = 'open_po';
    case Printing = 'printing';
    case Sewing = 'sewing';
    case QualityControl = 'quality_control';
    case ReadyForPickup = 'ready_for_pickup';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::OpenPo => 'Kloter PO Dibuka',
            self::Printing => 'Naik Cetak & Sablon',
            self::Sewing => 'Proses Jahit Konveksi',
            self::QualityControl => 'Quality Control (QC)',
            self::ReadyForPickup => 'Siap Diambil / Dikirim',
        };
    }

    public function getColor(): string|array|null
    {
        return match ($this) {
            self::OpenPo => 'gray',
            self::Printing => 'info',
            self::Sewing => 'warning',
            self::QualityControl => 'primary',
            self::ReadyForPickup => 'success',
        };
    }

    public function getIcon(): ?string
    {
        return match ($this) {
            self::OpenPo => 'heroicon-o-shopping-bag',
            self::Printing => 'heroicon-o-printer',
            self::Sewing => 'heroicon-o-scissors',
            self::QualityControl => 'heroicon-o-magnifying-glass-circle',
            self::ReadyForPickup => 'heroicon-o-truck',
        };
    }

    public function stepIndex(): int
    {
        return match ($this) {
            self::OpenPo => 1,
            self::Printing => 2,
            self::Sewing => 3,
            self::QualityControl => 4,
            self::ReadyForPickup => 5,
        };
    }

    public function percentage(): int
    {
        return match ($this) {
            self::OpenPo => 20,
            self::Printing => 40,
            self::Sewing => 65,
            self::QualityControl => 85,
            self::ReadyForPickup => 100,
        };
    }

    public function description(): string
    {
        return match ($this) {
            self::OpenPo => 'Pendaftaran pesanan jersey sedang dibuka dan pembayaran DP sedang dihimpun.',
            self::Printing => 'Kain telah dipotong, pola sablon nama & nomor punggung sedang diproses cetak sublimasi/DTF.',
            self::Sewing => 'Bahan jersey sedang dijahit oleh vendor konveksi rekanan.',
            self::QualityControl => 'Pemeriksaan jahitan, keakuratan nomor/nama punggung, dan pembersihan benang sisa.',
            self::ReadyForPickup => 'Jersey telah dipacking rapi dan siap diambil di sekretariat atau dikirim via kurir.',
        };
    }
}
