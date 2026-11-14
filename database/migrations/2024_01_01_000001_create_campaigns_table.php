<?php

use App\Enums\ProductionStage;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('campaigns', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->string('banner_path')->nullable();
            $table->decimal('base_price', 12, 2)->default(0.00);
            $table->unsignedInteger('min_quota')->default(24);
            $table->unsignedTinyInteger('dp_percentage')->default(50);
            $table->string('stage')->default(ProductionStage::OpenPo->value);
            $table->date('start_date');
            $table->date('end_date');
            $table->string('primary_color', 7)->default('#111827'); // Monochrome dark default
            $table->string('accent_color', 7)->default('#F59E0B');  // Gold/amber default
            $table->string('qris_image_path')->nullable();
            $table->string('bank_name')->nullable()->default('BCA');
            $table->string('bank_account_number')->nullable();
            $table->string('bank_account_holder')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['is_active', 'stage']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('campaigns');
    }
};
