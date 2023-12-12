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
        Schema::create('partners', function (Blueprint $table) {
            $table->string('basic_cnpj', 8)->unique()->primary();
            $table->integer('partner_identifier');
            $table->string('partner_name');
            $table->string('cnpj_cpf_partner', 14);
            $table->string('partner_qualification', 2);
            $table->date('partnership_start_date')->nullable();
            $table->string('country')->nullable();
            $table->string('legal_representative', 11);
            $table->string('representative_name')->nullable();
            $table->string('representative_qualification', 2)->nullable();
            $table->string('age_group')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('partners');
    }
};
