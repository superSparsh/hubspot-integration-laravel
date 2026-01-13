<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('webhook_payloads', function (Blueprint $table) {
            $table->id();
            $table->string('platform_id')->index();
            $table->string('event')->index();
            $table->json('payload');
            $table->timestamps();

            $table->unique(['platform_id', 'event']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('webhook_payloads');
    }
};
