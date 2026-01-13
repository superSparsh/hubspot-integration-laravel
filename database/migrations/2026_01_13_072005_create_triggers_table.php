<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('triggers', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('account_id')->index();
            $table->string('event')->index();
            $table->string('trigger_name');
            $table->string('template_uid');
            $table->string('template_name');
            $table->text('to_field');
            $table->string('api_token');
            $table->timestamps();

            $table->index(['account_id', 'event']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('triggers');
    }
};
