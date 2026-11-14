<?php

declare(strict_types=1);

namespace App\Livewire;

use App\Enums\PaymentStatus;
use App\Enums\ProductionStage;
use App\Models\Order;
use Livewire\Attributes\Rule;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithFileUploads;

class TrackOrder extends Component
{
    use WithFileUploads;

    #[Url(as: 'invoice')]
    public string $searchQuery = '';

    public ?Order $order = null;

    #[Rule('required|image|mimes:jpg,jpeg,png,webp|max:5120')]
    public $finalPaymentProof;

    public bool $repaymentSubmitted = false;

    public function mount(): void
    {
        if ($this->searchQuery) {
            $this->search();
        }
    }

    public function search(): void
    {
        $query = trim($this->searchQuery);

        if (empty($query)) {
            $this->order = null;
            return;
        }

        $this->order = Order::with(['campaign', 'items'])
            ->where('invoice_code', $query)
            ->orWhere('customer_phone', $query)
            ->latest()
            ->first();

        if (! $this->order) {
            $this->addError('searchQuery', 'Pesanan tidak ditemukan. Pastikan Kode Invoice atau Nomor WhatsApp sudah sesuai.');
        } else {
            $this->resetErrorBag();
        }
    }

    public function submitFinalPayment(): void
    {
        $this->validateOnly('finalPaymentProof');

        if (! $this->order || ! $this->order->canUploadRepayment()) {
            return;
        }

        $diskName = config('filesystems.default', 's3');
        $folder = 'proofs/final/' . date('Y/m');
        $path = $this->finalPaymentProof->store($folder, $diskName);

        $this->order->update([
            'final_proof_path' => $path,
            'payment_status' => PaymentStatus::WaitingVerification,
        ]);

        $this->repaymentSubmitted = true;
        $this->order->refresh();
    }

    public function render()
    {
        return view('livewire.track-order');
    }
}
