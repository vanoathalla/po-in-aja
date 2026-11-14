<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\PaymentStatus;
use App\Enums\PaymentType;
use App\Enums\ProductionStage;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'campaign_id',
        'invoice_code',
        'customer_name',
        'customer_phone',
        'customer_email',
        'shipping_address',
        'notes',
        'subtotal',
        'addons_total',
        'total_amount',
        'payment_type',
        'down_payment_amount',
        'amount_paid',
        'payment_status',
        'dp_proof_path',
        'dp_verified_at',
        'final_proof_path',
        'final_verified_at',
        'rejection_reason',
    ];

    protected $casts = [
        'subtotal' => 'decimal:2',
        'addons_total' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'down_payment_amount' => 'decimal:2',
        'amount_paid' => 'decimal:2',
        'payment_type' => PaymentType::class,
        'payment_status' => PaymentStatus::class,
        'dp_verified_at' => 'datetime',
        'final_verified_at' => 'datetime',
    ];

    public function campaign(): BelongsTo
    {
        return $this->belongsTo(Campaign::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    /**
     * Accessor Sisa Tagihan (Remaining Balance)
     */
    protected function remainingBalance(): Attribute
    {
        return Attribute::make(
            get: fn () => max(0.00, round((float) $this->total_amount - (float) $this->amount_paid, 2))
        );
    }

    /**
     * S3 Public/Temporary URL untuk Bukti Transfer DP
     */
    protected function dpProofUrl(): Attribute
    {
        return Attribute::make(
            get: function () {
                if (! $this->dp_proof_path) {
                    return null;
                }
                $disk = Storage::disk(config('filesystems.default', 's3'));
                return method_exists($disk, 'temporaryUrl')
                    ? $disk->temporaryUrl($this->dp_proof_path, now()->addMinutes(60))
                    : $disk->url($this->dp_proof_path);
            }
        );
    }

    /**
     * S3 Public/Temporary URL untuk Bukti Transfer Pelunasan
     */
    protected function finalProofUrl(): Attribute
    {
        return Attribute::make(
            get: function () {
                if (! $this->final_proof_path) {
                    return null;
                }
                $disk = Storage::disk(config('filesystems.default', 's3'));
                return method_exists($disk, 'temporaryUrl')
                    ? $disk->temporaryUrl($this->final_proof_path, now()->addMinutes(60))
                    : $disk->url($this->final_proof_path);
            }
        );
    }

    /**
     * Syarat pembeli boleh upload bukti pelunasan:
     * 1. Status saat ini DP Paid
     * 2. Ada sisa tagihan (remaining_balance > 0)
     * 3. Tahap produksi sudah masuk Quality Control atau Ready For Pickup
     */
    public function canUploadRepayment(): bool
    {
        if ($this->payment_status !== PaymentStatus::DpPaid) {
            return false;
        }

        if ($this->remaining_balance <= 0) {
            return false;
        }

        $allowedStages = [
            ProductionStage::QualityControl,
            ProductionStage::ReadyForPickup,
        ];

        return in_array($this->campaign->stage, $allowedStages, true);
    }

    /**
     * Action: Verifikasi Pembayaran DP oleh Admin
     */
    public function approveDownPayment(?float $verifiedAmount = null): void
    {
        $amount = $verifiedAmount ?? (float) $this->down_payment_amount;

        $this->update([
            'amount_paid' => $amount,
            'payment_status' => PaymentStatus::DpPaid,
            'dp_verified_at' => now(),
            'rejection_reason' => null,
        ]);
    }

    /**
     * Action: Verifikasi Pelunasan 100% oleh Admin
     */
    public function approveFinalPayment(): void
    {
        $this->update([
            'amount_paid' => $this->total_amount,
            'payment_status' => PaymentStatus::PaidOff,
            'final_verified_at' => now(),
            'rejection_reason' => null,
        ]);
    }

    /**
     * Action: Tolak Pembayaran dengan Alasan
     */
    public function rejectPayment(string $reason): void
    {
        $this->update([
            'payment_status' => PaymentStatus::Unpaid,
            'rejection_reason' => $reason,
        ]);
    }
}
