<?php

use App\Enums\PaymentStatus;
use App\Enums\PaymentType;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('campaign_id')->constrained()->cascadeOnDelete();
            $table->string('invoice_code')->unique(); // e.g. PO-FTI-8821
            $table->string('customer_name');
            $table->string('customer_phone'); // WhatsApp format 628xxx
            $table->string('customer_email')->nullable();
            $table->text('shipping_address')->nullable();
            $table->text('notes')->nullable();

            // Pricing breakdowns
            $table->decimal('subtotal', 12, 2)->default(0.00);
            $table->decimal('addons_total', 12, 2)->default(0.00);
            $table->decimal('total_amount', 12, 2)->default(0.00);

            // Payment Split handling
            $table->string('payment_type')->default(PaymentType::Dp->value); // dp or full
            $table->decimal('down_payment_amount', 12, 2)->default(0.00);
            $table->decimal('amount_paid', 12, 2)->default(0.00);
            $table->string('payment_status')->default(PaymentStatus::WaitingVerification->value);

            // S3 Proof Uploads
            $table->string('dp_proof_path')->nullable();
            $table->timestamp('dp_verified_at')->nullable();
            $table->string('final_proof_path')->nullable();
            $table->timestamp('final_verified_at')->nullable();

            $table->string('rejection_reason')->nullable();
            $table->timestamps();

            $table->index(['campaign_id', 'payment_status']);
            $table->index('customer_phone');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
