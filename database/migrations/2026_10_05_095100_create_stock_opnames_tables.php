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
        Schema::create('stock_opnames', function (Blueprint $table) {
            $table->id();
            $table->string('opname_number', 50)->unique();
            $table->date('opname_date');
            $table->string('status', 20)->default('COMPLETED'); // DRAFT, COMPLETED
            $table->unsignedInteger('total_items')->default(0);
            $table->unsignedInteger('total_matched')->default(0);
            $table->unsignedInteger('total_mismatched')->default(0);
            $table->text('notes')->nullable();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('conducted_by', 100)->nullable(); // Nama petugas/tim pemeriksa fisik
            $table->timestamps();

            $table->index(['opname_date', 'opname_number']);
        });

        Schema::create('stock_opname_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('stock_opname_id')->constrained('stock_opnames')->cascadeOnDelete();
            $table->foreignId('item_id')->constrained('items')->cascadeOnDelete();
            $table->integer('system_stock'); // Stok sistem tercatat
            $table->integer('physical_stock'); // Stok fisik hasil hitungan nyata
            $table->integer('difference'); // physical_stock - system_stock
            $table->string('status', 20)->default('MATCH'); // MATCH, PLUS, MINUS
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['stock_opname_id', 'item_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stock_opname_details');
        Schema::dropIfExists('stock_opnames');
    }
};
