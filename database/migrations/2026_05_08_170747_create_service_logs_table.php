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
        Schema::create('service_logs', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->enum('status', ['up', 'down']);
            $table->float('response_time')->nullable();
            $table->timestamp('checked_at');
            $table->unsignedBigInteger('service_id')->index('fk_service_logs_services1_idx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('service_logs');
    }
};
