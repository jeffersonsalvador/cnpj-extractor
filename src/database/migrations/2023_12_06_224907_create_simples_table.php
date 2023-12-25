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
        Schema::create('simples', function (Blueprint $table) {
            $table->string('basic_cnpj', 8);
            $table->enum('simple_option', ['S', 'N', '']);
            $table->string('simple_option_date')->nullable();
            $table->string('simple_exclusion_date')->nullable();
            $table->enum('mei_option', ['S', 'N', '']);
            $table->string('mei_option_date')->nullable();
            $table->string('mei_exclusion_date')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('simples');
    }
};
