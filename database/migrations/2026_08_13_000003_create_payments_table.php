<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void { Schema::create('payments', function (Blueprint $table) { $table->id(); $table->enum('order_type', ['retail','wholesale']); $table->unsignedBigInteger('order_id'); $table->enum('payment_method', ['mpesa','tigopesa','airtelmoney','halopesa','bank']); $table->string('provider_reference', 100)->unique(); $table->enum('status', ['pending','success','failed','reversed'])->default('pending'); $table->decimal('amount', 10, 2); $table->json('raw_callback_payload')->nullable(); $table->timestamps(); $table->index(['order_type', 'order_id']); }); }
    public function down(): void { Schema::dropIfExists('payments'); }
};
