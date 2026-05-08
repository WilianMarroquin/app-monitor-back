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
        Schema::table('incidents', function (Blueprint $table) {
            $table->foreign(['ping_id'], 'fk_incidents_service_logs1')->references(['id'])->on('service_logs')->onUpdate('no action')->onDelete('no action');
            $table->foreign(['service_id'], 'fk_incidents_services1')->references(['id'])->on('services')->onUpdate('no action')->onDelete('no action');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('incidents', function (Blueprint $table) {
            $table->dropForeign('fk_incidents_service_logs1');
            $table->dropForeign('fk_incidents_services1');
        });
    }
};
