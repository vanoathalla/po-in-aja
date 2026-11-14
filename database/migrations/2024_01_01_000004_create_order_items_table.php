<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            $table->string('size', 10); // XS, S, M, L, XL, XXL, 3XL, 4XL
            $table->string('custom_name', 12)->nullable(); // Punggung Jersey
            $table->string('custom_number', 2)->nullable(); // No Punggung
            $table->string('sleeve_type', 10)->default('short'); // short, long
            $table->json('selected_addons')->nullable(); // array of addon objects/ids

            // Price breakdowns per jersey
            $table->decimal('unit_price', 12, 2)->default(0.00);
            $table->decimal('size_surcharge', 12, 2)->default(0.00);
            $table->decimal('sleeve_surcharge', 12, 2)->default(0.00);
            $table->decimal('addons_price', 12, 2)->default(0.00);
            $table->unsignedInteger('quantity')->default(1);
            $table->decimal('total_price', 12, 2)->default(0.00);

            $table->timestamps();

            $table->index(['order_id', 'size']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_items');
    }
};
