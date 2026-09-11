<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void { Schema::table('wholesale_orders', function (Blueprint $table) { $table->string('payment_method', 30)->nullable()->after('total_amount'); $table->string('transaction_reference', 100)->nullable()->after('payment_status'); }); }
    public function down(): void { Schema::table('wholesale_orders', function (Blueprint $table) { $table->dropColumn(['payment_method', 'transaction_reference']); }); }
};
