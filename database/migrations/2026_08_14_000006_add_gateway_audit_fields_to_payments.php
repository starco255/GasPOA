<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->string('provider', 30)->default('manual')->after('payment_method');
            $table->string('provider_payment_reference', 100)->nullable()->after('provider_reference');
            $table->text('checkout_url')->nullable()->after('provider_payment_reference');
            $table->timestamp('paid_at')->nullable()->after('status');
            $table->timestamp('failed_at')->nullable()->after('paid_at');
            $table->string('failure_reason', 255)->nullable()->after('failed_at');
        });
    }

    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->dropColumn(['provider', 'provider_payment_reference', 'checkout_url', 'paid_at', 'failed_at', 'failure_reason']);
        });
    }
};
