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
        Schema::create('people', function (Blueprint $table) {
            $table->id();
            $table->string('passport_id');
            $table->string('gender');
            $table->string('firstname');
            $table->string('lastname');
            $table->string('type');
            $table->date('date_of_birth')->nullable();
            $table->date('passport_issue_date')->nullable();
            $table->date('passport_expiry_date')->nullable();
            $table->date('visa_expiry_date')->nullable();
            $table->date('arrival_date')->nullable();
            $table->string('mobile_number')->nullable();
            $table->date('police_arrival_reported_date')->nullable();
            $table->date('police_departure_reported_date')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('people');
    }
};
