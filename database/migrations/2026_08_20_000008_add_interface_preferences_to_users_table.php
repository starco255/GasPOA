<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Add optional display preferences without changing or removing existing data.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'interface_language')) {
                $table->string('interface_language', 5)->nullable();
            }

            if (!Schema::hasColumn('users', 'interface_theme')) {
                $table->string('interface_theme', 10)->nullable();
            }
        });
    }

    /**
     * These columns are isolated from the existing user data.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $columns = array_values(array_filter([
                Schema::hasColumn('users', 'interface_language') ? 'interface_language' : null,
                Schema::hasColumn('users', 'interface_theme') ? 'interface_theme' : null,
            ]));

            if ($columns !== []) {
                $table->dropColumn($columns);
            }
        });
    }
};
