<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('hubspot_webhook_logs', function (Blueprint $table) {
            $table->id();
            $table->string('hubspot_portal_id')->index();
            $table->string('event_type');
            $table->json('payload');
            $table->string('signature')->nullable();
            $table->boolean('verified')->default(false);
            $table->enum('status', ['pending', 'processed', 'failed'])->default('pending');
            $table->text('error')->nullable();
            $table->timestamps();

            $table->index(['hubspot_portal_id', 'event_type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('hubspot_webhook_logs');
    }
};
