<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('stock_adjustments', function (Blueprint $table) {
            $table->id();
            $table->string('adjustment_number', 50)->unique();
            $table->date('adjustment_date');
            $table->string('type', 20)->default('CORRECTION'); // INCREASE, DECREASE, CORRECTION
            $table->unsignedInteger('total_items')->default(1);
            $table->string('reason', 150); // Rusak/Cacat, Hilang/Selisih Fisik, Temuan Berlebih, Koreksi Audit, Kadaluarsa, dll
            $table->text('notes')->nullable();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['adjustment_date', 'adjustment_number']);
        });

        Schema::create('stock_adjustment_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('stock_adjustment_id')->constrained('stock_adjustments')->cascadeOnDelete();
            $table->foreignId('item_id')->constrained('items')->cascadeOnDelete();
            $table->integer('system_stock'); // Stok sistem sebelum penyesuaian (dalam satuan dasar / eceran)
            $table->integer('actual_stock'); // Stok fisik riil setelah penyesuaian
            $table->integer('difference'); // Selisih (+/-)
            $table->string('unit', 30)->default('Pcs'); // Satuan yang digunakan saat input
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['stock_adjustment_id', 'item_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stock_adjustment_details');
        Schema::dropIfExists('stock_adjustments');
    }
};
