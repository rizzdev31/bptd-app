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
        Schema::create('stock_ins', function (Blueprint $table) {
            $table->id();
            $table->string('transaction_number', 50)->unique();
            $table->date('date');
            $table->string('source', 50)->default('Procurement'); // Procurement, Direct, Hibah, Adjustment, Lainnya
            $table->foreignId('procurement_id')->nullable()->constrained('procurements')->nullOnDelete();
            $table->foreignId('supplier_id')->nullable()->constrained('suppliers')->nullOnDelete();
            $table->string('supplier_name', 150)->nullable();
            $table->string('reference_number', 100)->nullable(); // Nomor Faktur / Surat Jalan / Bukti Terima
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete(); // Petugas Penerima
            $table->text('notes')->nullable();
            $table->unsignedInteger('total_items')->default(0);
            $table->unsignedInteger('total_quantity')->default(0); // Total pieces
            $table->timestamps();

            $table->index(['date', 'source']);
            $table->index('procurement_id');
        });

        Schema::create('stock_in_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('stock_in_id')->constrained('stock_ins')->cascadeOnDelete();
            $table->foreignId('item_id')->constrained('items')->cascadeOnDelete();
            $table->string('item_name', 200);
            $table->string('item_code', 50);
            $table->unsignedInteger('quantity');
            $table->string('unit', 50);
            $table->unsignedInteger('conversion_factor')->default(1);
            $table->unsignedInteger('base_quantity');
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['stock_in_id', 'item_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stock_in_details');
        Schema::dropIfExists('stock_ins');
    }
};
