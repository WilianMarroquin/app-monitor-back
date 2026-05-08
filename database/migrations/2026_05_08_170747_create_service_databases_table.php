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
        Schema::create('service_databases', function (Blueprint $table) {
            $table->unsignedBigInteger('service_id')->index('fk_service_databases_services1_idx');
            $table->string('db_type', 65);
            $table->string('host_ip', 350);
            $table->string('port', 10);
            $table->string('username', 150);
            $table->string('password');
            $table->timestamps();
            $table->softDeletes();

            $table->primary(['service_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('service_databases');
    }
};
