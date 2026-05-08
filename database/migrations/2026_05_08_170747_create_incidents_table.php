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
        Schema::create('incidents', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->text('description');
            $table->enum('status', ['open', 'resolved']);
            $table->timestamp('opened_at');
            $table->timestamp('resolved_at')->nullable();
            $table->unsignedBigInteger('service_id')->index('fk_incidents_services1_idx');
            $table->unsignedBigInteger('ping_id')->nullable()->index('fk_incidents_service_logs1_idx');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('incidents');
    }
};
