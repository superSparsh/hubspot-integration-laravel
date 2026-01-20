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
        Schema::table('triggers', function (Blueprint $table) {
            // Add shop_domain column (compatible with original PHP code)
            $table->string('shop_domain')->nullable()->after('account_id')->index();
            
            // Add integration_type column
            $table->string('integration_type')->nullable()->default('hubspot')->after('api_token');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('triggers', function (Blueprint $table) {
            $table->dropColumn(['shop_domain', 'integration_type']);
        });
    }
};
