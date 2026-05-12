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
        Schema::create('service_webs', function (Blueprint $table) {
            $table->unsignedBigInteger('service_id')->index('fk_service_webs_services1_idx');
            $table->string('url', 350);

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
        Schema::dropIfExists('service_webs');
    }
};
