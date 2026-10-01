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
        Schema::table('items', function (Blueprint $table) {
            $table->string('small_unit', 50)->nullable()->after('unit'); // Satuan eceran/terkecil (misal 'Pcs', 'Lembar')
            $table->unsignedInteger('conversion_rate')->default(1)->after('small_unit'); // Rasio: 1 kemasan = X satuan kecil
        });

        Schema::table('stock_out_details', function (Blueprint $table) {
            $table->unsignedInteger('conversion_factor')->default(1)->after('unit');
            $table->unsignedInteger('base_quantity')->default(1)->after('conversion_factor');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('stock_out_details', function (Blueprint $table) {
            $table->dropColumn(['conversion_factor', 'base_quantity']);
        });

        Schema::table('items', function (Blueprint $table) {
            $table->dropColumn(['small_unit', 'conversion_rate']);
        });
    }
};
