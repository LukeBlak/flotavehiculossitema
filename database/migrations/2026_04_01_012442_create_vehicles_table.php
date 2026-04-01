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
        Schema::create('vehicles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vehicle_type_id')->constrained()->cascadeOnUpdate()->restrictOnDelete();
            $table->string('plate_number')->unique();
            $table->string('brand');
            $table->string('model');
            $table->year('year');
            $table->unsignedBigInteger('current_odometer')->nullable();
            $table->unsignedBigInteger('next_maintenance_km')->nullable();
            $table->string('status')->default('active');
            $table->foreignId('assigned_driver_id')->nullable()->constrained('users')->nullOnDelete()->cascadeOnUpdate();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vehicles');
    }
};
