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
        Schema::table('users', function (Blueprint $table) {
            $table->string('username', 50)->unique()->after('id');
            $table->string('nip', 30)->nullable()->unique()->after('username');
            $table->foreignId('role_id')->nullable()->after('password')->constrained('roles')->nullOnDelete();
            $table->foreignId('work_unit_id')->nullable()->after('role_id')->constrained('work_units')->nullOnDelete();
            $table->string('phone', 30)->nullable()->after('work_unit_id');
            $table->enum('status', ['active', 'inactive'])->default('active')->after('phone');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['role_id']);
            $table->dropForeign(['work_unit_id']);
            $table->dropColumn(['username', 'nip', 'role_id', 'work_unit_id', 'phone', 'status']);
        });
    }
};
