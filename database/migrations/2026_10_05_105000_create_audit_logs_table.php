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
        Schema::create('audit_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('user_name')->nullable();
            $table->string('user_nip')->nullable();
            $table->string('action', 50); // CREATE, UPDATE, DELETE, STATUS_CHANGE, STOCK_OUT, STOCK_IN, ADJUSTMENT, OPNAME, RECEIVE, CANCEL, LOGIN, LOGOUT
            $table->string('module', 50); // Master ATK, Permintaan ATK, Kendali Stok, Pengadaan, Penerimaan Barang, Pegawai, Role & Hak Akses, Autentikasi
            $table->string('record_type', 100)->nullable();
            $table->string('record_id', 100)->nullable();
            $table->text('description');
            $table->json('old_values')->nullable();
            $table->json('new_values')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->timestamps();

            // Indexes untuk query filter yang sangat cepat
            $table->index('user_id');
            $table->index('action');
            $table->index('module');
            $table->index(['record_type', 'record_id']);
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('audit_logs');
    }
};
