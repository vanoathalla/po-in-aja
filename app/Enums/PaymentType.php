<?php

declare(strict_types=1);

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum PaymentType: string implements HasLabel, HasColor
{
    case Dp = 'dp';
    case Full = 'full';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::Dp => 'Down Payment (DP 50%)',
            self::Full => 'Pelunasan Penuh (100%)',
        };
    }

    public function getColor(): string|array|null
    {
        return match ($this) {
            self::Dp => 'warning',
            self::Full => 'success',
        };
    }
}
