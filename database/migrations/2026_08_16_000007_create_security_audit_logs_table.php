<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * This table is additive: it does not alter or remove any existing records.
     */
    public function up(): void
    {
        if (Schema::hasTable('security_audit_logs')) {
            return;
        }

        Schema::create('security_audit_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('actor_user_id')->nullable();
            $table->unsignedBigInteger('subject_user_id')->nullable();
            $table->string('event_type', 80);
            $table->string('severity', 20)->default('info');
            $table->string('description', 500);
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->json('context')->nullable();
            $table->timestamp('occurred_at')->useCurrent();
            $table->timestamps();

            $table->index(['event_type', 'occurred_at']);
            $table->index(['subject_user_id', 'occurred_at']);
            $table->index(['actor_user_id', 'occurred_at']);
            $table->index(['severity', 'occurred_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('security_audit_logs');
    }
};
