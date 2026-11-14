<?php

declare(strict_types=1);

namespace App\Livewire;

use App\Enums\PaymentStatus;
use App\Enums\PaymentType;
use App\Models\Campaign;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Rule;
use Livewire\Component;
use Livewire\WithFileUploads;

class OrderConfigurator extends Component
{
    use WithFileUploads;

    public Campaign $campaign;

    // Jersey Customization Attributes
    #[Rule('required|in:XS,S,M,L,XL,XXL,3XL,4XL')]
    public string $size = 'L';

    #[Rule('nullable|string|max:12|regex:/^[A-Za-z0-9\s.]+$/')]
    public string $customName = '';

    #[Rule('nullable|numeric|digits_between:1,2')]
    public string $customNumber = '';

    #[Rule('required|in:short,long')]
    public string $sleeveType = 'short';

    public array $selectedAddonIds = [];

    #[Rule('required|integer|min:1|max:50')]
    public int $quantity = 1;

    // Customer Information
    #[Rule('required|string|min:3|max:100')]
    public string $customerName = '';

    #[Rule('required|regex:/^(\+62|62|0)8[1-9][0-9]{6,10}$/')]
    public string $customerPhone = '';

    #[Rule('nullable|email|max:150')]
    public string $customerEmail = '';

    #[Rule('nullable|string|max:500')]
    public string $shippingAddress = '';

    #[Rule('nullable|string|max:300')]
    public string $notes = '';

    // Payment Scheme
    #[Rule('required|in:dp,full')]
    public string $paymentType = 'dp';

    #[Rule('required|image|mimes:jpg,jpeg,png,webp|max:5120')]
    public $paymentProof;

    public bool $isSubmitted = false;
    public ?string $generatedInvoice = null;

    public function mount(Campaign $campaign): void
    {
        $this->campaign = $campaign;
    }

    #[Computed]
    public function sizeSurcharge(): float
    {
        return match ($this->size) {
            'XXL' => 10000.00,
            '3XL' => 20000.00,
            '4XL' => 30000.00,
            default => 0.00,
        };
    }

    #[Computed]
    public function sleeveSurcharge(): float
    {
        return $this->sleeveType === 'long' ? 15000.00 : 0.00;
    }

    #[Computed]
    public function selectedAddonsList(): array
    {
        return $this->campaign->addons()
            ->whereIn('id', $this->selectedAddonIds)
            ->where('is_active', true)
            ->get(['id', 'name', 'price'])
            ->toArray();
    }

    #[Computed]
    public function addonsTotalPerItem(): float
    {
        return (float) array_sum(array_column($this->selectedAddonsList, 'price'));
    }

    #[Computed]
    public function unitPrice(): float
    {
        return (float) $this->campaign->base_price
            + $this->sizeSurcharge
            + $this->sleeveSurcharge
            + $this->addonsTotalPerItem;
    }

    #[Computed]
    public function totalAmount(): float
    {
        return $this->unitPrice * $this->quantity;
    }

    #[Computed]
    public function downPaymentAmount(): float
    {
        $percentage = $this->campaign->dp_percentage ?: 50;
        return round(($this->totalAmount * $percentage) / 100, 2);
    }

    #[Computed]
    public function dueTodayAmount(): float
    {
        return $this->paymentType === 'full'
            ? $this->totalAmount
            : $this->downPaymentAmount;
    }

    #[Computed]
    public function remainingBalance(): float
    {
        return max(0.00, $this->totalAmount - $this->dueTodayAmount);
    }

    public function submitOrder(): void
    {
        $this->validate();

        // 1. Simpan bukti transfer ke S3 Supabase Bucket
        $diskName = config('filesystems.default', 's3');
        $proofFolder = 'proofs/dp/' . date('Y/m');
        $proofPath = $this->paymentProof->store($proofFolder, $diskName);

        // 2. Buat Invoice Code Unik (e.g. PO-FTI-8821)
        $campaignCode = strtoupper(substr(preg_replace('/[^A-Za-z0-9]/', '', $this->campaign->title), 0, 4)) ?: 'JRSY';
        $invoiceCode = sprintf('PO-%s-%04d', $campaignCode, random_int(1000, 9999));

        DB::transaction(function () use ($invoiceCode, $proofPath) {
            $order = Order::create([
                'campaign_id' => $this->campaign->id,
                'invoice_code' => $invoiceCode,
                'customer_name' => $this->customerName,
                'customer_phone' => $this->customerPhone,
                'customer_email' => $this->customerEmail ?: null,
                'shipping_address' => $this->shippingAddress ?: null,
                'notes' => $this->notes ?: null,
                'subtotal' => $this->campaign->base_price * $this->quantity,
                'addons_total' => ($this->sizeSurcharge + $this->sleeveSurcharge + $this->addonsTotalPerItem) * $this->quantity,
                'total_amount' => $this->totalAmount,
                'payment_type' => $this->paymentType === 'full' ? PaymentType::Full : PaymentType::Dp,
                'down_payment_amount' => $this->downPaymentAmount,
                'amount_paid' => 0.00, // Menunggu verifikasi admin panitia
                'payment_status' => PaymentStatus::WaitingVerification,
                'dp_proof_path' => $proofPath,
            ]);

            OrderItem::create([
                'order_id' => $order->id,
                'size' => $this->size,
                'custom_name' => $this->customName ? strtoupper($this->customName) : null,
                'custom_number' => $this->customNumber ?: null,
                'sleeve_type' => $this->sleeveType,
                'selected_addons' => $this->selectedAddonsList,
                'unit_price' => $this->campaign->base_price,
                'size_surcharge' => $this->sizeSurcharge,
                'sleeve_surcharge' => $this->sleeveSurcharge,
                'addons_price' => $this->addonsTotalPerItem,
                'quantity' => $this->quantity,
                'total_price' => $this->totalAmount,
            ]);
        });

        $this->generatedInvoice = $invoiceCode;
        $this->isSubmitted = true;
    }

    public function render()
    {
        return view('livewire.order-configurator');
    }
}
