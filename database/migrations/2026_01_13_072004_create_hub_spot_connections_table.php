<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('hubspot_connections', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('wapapp_account_id')->index();
            $table->string('hubspot_portal_id')->unique();
            $table->text('access_token'); // Encrypted
            $table->text('refresh_token'); // Encrypted
            $table->timestamp('expires_at');
            $table->text('scopes')->nullable();
            $table->enum('status', ['active', 'inactive', 'expired', 'revoked'])->default('active');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('hubspot_connections');
    }
};
