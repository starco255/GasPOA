<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Keeps the legacy retail_orders.payment_method enum intact. New provider
     * values live here so historical cash/mobile_money/card values remain valid.
     */
    public function up(): void
    {
        Schema::table('retail_orders', function (Blueprint $table) {
            $table->string('payment_method_provider', 30)->nullable()->after('payment_method');
        });
    }
    public function down(): void { Schema::table('retail_orders', fn (Blueprint $table) => $table->dropColumn('payment_method_provider')); }
};
