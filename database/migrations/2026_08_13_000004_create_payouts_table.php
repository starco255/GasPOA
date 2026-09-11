<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void { Schema::create('payouts', function (Blueprint $table) { $table->id(); $table->unsignedBigInteger('payment_id'); $table->integer('business_profile_id'); $table->decimal('amount', 10, 2); $table->decimal('commission_amount', 10, 2); $table->enum('status', ['pending','sent','failed'])->default('pending'); $table->string('provider_reference', 100)->nullable(); $table->timestamps(); $table->unique('payment_id'); $table->index('business_profile_id'); }); }
    public function down(): void { Schema::dropIfExists('payouts'); }
};
