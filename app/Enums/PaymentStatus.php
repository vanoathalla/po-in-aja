<?php

declare(strict_types=1);

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;

enum PaymentStatus: string implements HasLabel, HasColor, HasIcon
{
    case Unpaid = 'unpaid';
    case WaitingVerification = 'waiting_verification';
    case DpPaid = 'dp_paid';
    case PaidOff = 'paid_off';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::Unpaid => 'Belum Bayar',
            self::WaitingVerification => 'Menunggu Verifikasi',
            self::DpPaid => 'DP Terverifikasi (50%)',
            self::PaidOff => 'Lunas (100%)',
        };
    }

    public function getColor(): string|array|null
    {
        return match ($this) {
            self::Unpaid => 'danger',
            self::WaitingVerification => 'warning',
            self::DpPaid => 'info',
            self::PaidOff => 'success',
        };
    }

    public function getIcon(): ?string
    {
        return match ($this) {
            self::Unpaid => 'heroicon-o-x-circle',
            self::WaitingVerification => 'heroicon-o-clock',
            self::DpPaid => 'heroicon-o-check',
            self::PaidOff => 'heroicon-o-check-badge',
        };
    }

    public function isVerified(): bool
    {
        return in_array($this, [self::DpPaid, self::PaidOff], true);
    }
}
