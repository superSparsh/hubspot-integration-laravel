<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('trigger_variables', function (Blueprint $table) {
            $table->id();
            $table->foreignId('trigger_id')->constrained()->onDelete('cascade');
            $table->string('var_key');
            $table->text('var_path');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('trigger_variables');
    }
};
