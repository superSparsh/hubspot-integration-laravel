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
        Schema::table('hubspot_connections', function (Blueprint $table) {
            $table->text('wapapp_token')->nullable()->after('scopes');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('hubspot_connections', function (Blueprint $table) {
            $table->dropColumn('wapapp_token');
        });
    }
};
