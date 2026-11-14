<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\PaymentStatus;
use App\Enums\ProductionStage;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;

class Campaign extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'slug',
        'description',
        'banner_path',
        'base_price',
        'min_quota',
        'dp_percentage',
        'stage',
        'start_date',
        'end_date',
        'primary_color',
        'accent_color',
        'qris_image_path',
        'bank_name',
        'bank_account_number',
        'bank_account_holder',
        'is_active',
    ];

    protected $casts = [
        'base_price' => 'decimal:2',
        'min_quota' => 'integer',
        'dp_percentage' => 'integer',
        'stage' => ProductionStage::class,
        'start_date' => 'date',
        'end_date' => 'date',
        'is_active' => 'boolean',
    ];

    public function addons(): HasMany
    {
        return $this->hasMany(CampaignAddon::class);
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    protected function bannerUrl(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->banner_path
                ? Storage::disk(config('filesystems.default', 's3'))->url($this->banner_path)
                : null
        );
    }

    protected function qrisImageUrl(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->qris_image_path
                ? Storage::disk(config('filesystems.default', 's3'))->url($this->qris_image_path)
                : null
        );
    }

    /**
     * Menghitung total jersey yang sudah dipesan dalam kloter ini.
     */
    public function getTotalJerseysOrderedAttribute(): int
    {
        return (int) OrderItem::whereHas('order', function ($query) {
            $query->where('campaign_id', $this->id)
                ->where('payment_status', '!=', PaymentStatus::Unpaid);
        })->sum('quantity');
    }

    /**
     * Menghitung persentase progres kuota minimum konveksi.
     */
    public function getQuotaProgressPercentageAttribute(): int
    {
        if ($this->min_quota <= 0) {
            return 100;
        }

        return (int) min(100, round(($this->total_jerseys_ordered / $this->min_quota) * 100));
    }
}
