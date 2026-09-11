<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void { Schema::table('business_profiles', function (Blueprint $table) { $table->boolean('accept_mpesa')->default(true); $table->boolean('accept_tigopesa')->default(true); $table->boolean('accept_airtelmoney')->default(true); $table->boolean('accept_halopesa')->default(true); }); }
    public function down(): void { Schema::table('business_profiles', function (Blueprint $table) { $table->dropColumn(['accept_mpesa', 'accept_tigopesa', 'accept_airtelmoney', 'accept_halopesa']); }); }
};
