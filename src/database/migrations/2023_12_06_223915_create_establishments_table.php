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
        Schema::create('establishments', function (Blueprint $table) {
            $table->string('basic_cnpj', 8);
            $table->string('cnpj_order', 4);
            $table->string('cnpj_dv', 2);
            $table->char('main_branch_office', 1);
            $table->string('trade_name')->nullable();
            $table->integer('registration_status');
            $table->date('registration_status_date')->nullable();
            $table->string('registration_reason')->nullable();
            $table->string('foreign_city_name')->nullable();
            $table->string('country')->nullable();
            $table->date('activity_start_date')->nullable();
            $table->string('main_cnae', 7);
            $table->text('secondary_cnae')->nullable();
            $table->string('street_type')->nullable();
            $table->string('address')->nullable();
            $table->string('address_number')->nullable();
            $table->string('additional_address_info')->nullable();
            $table->string('neighborhood')->nullable();
            $table->string('zip_code', 8)->nullable();
            $table->string('state', 2);
            $table->string('city_code')->nullable();
            $table->string('phone_area_code_1', 2)->nullable();
            $table->string('phone_number_1')->nullable();
            $table->string('phone_area_code_2', 2)->nullable();
            $table->string('phone_number_2')->nullable();
            $table->string('fax_area_code', 2)->nullable();
            $table->string('fax_number')->nullable();
            $table->string('email')->nullable();
            $table->string('special_situation')->nullable();
            $table->date('special_situation_date')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('establishments');
    }
};
