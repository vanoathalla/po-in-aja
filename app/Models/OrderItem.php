<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'size',
        'custom_name',
        'custom_number',
        'sleeve_type',
        'selected_addons',
        'unit_price',
        'size_surcharge',
        'sleeve_surcharge',
        'addons_price',
        'quantity',
        'total_price',
    ];

    protected $casts = [
        'selected_addons' => 'array',
        'unit_price' => 'decimal:2',
        'size_surcharge' => 'decimal:2',
        'sleeve_surcharge' => 'decimal:2',
        'addons_price' => 'decimal:2',
        'quantity' => 'integer',
        'total_price' => 'decimal:2',
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * Sanitasi & standarisasi nama punggung ke huruf kapital
     */
    protected function customName(): Attribute
    {
        return Attribute::make(
            set: fn (?string $value) => $value ? strtoupper(substr(trim($value), 0, 12)) : null
        );
    }

    /**
     * Ringkasan Add-ons yang dipilih dalam bentuk string rapi
     */
    public function getAddonsSummaryAttribute(): string
    {
        if (empty($this->selected_addons) || ! is_array($this->selected_addons)) {
            return '-';
        }

        $items = [];
        foreach ($this->selected_addons as $addon) {
            $name = $addon['name'] ?? 'Addon';
            $price = isset($addon['price']) ? ' (+Rp' . number_format((float) $addon['price'], 0, ',', '.') . ')' : '';
            $items[] = "{$name}{$price}";
        }

        return implode(', ', $items);
    }
}
