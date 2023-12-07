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
        Schema::create('companies', function (Blueprint $table) {
            $table->charset = 'utf8mb4'; // Set the character set to utf8mb4
            $table->collation = 'utf8mb4_unicode_ci'; // Set the collation to utf8mb4_unicode_ci

            $table->string('basic_cnpj', 8)->unique()->primary();
            $table->string('corporate_name');
            $table->string('legal_nature', 4);
            $table->string('responsible_qualification', 2);
            $table->decimal('capital_social', 15);
            $table->string('company_size', 2);
            $table->string('federative_entity_responsible')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('companies');
    }
};
