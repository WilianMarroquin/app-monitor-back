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
        Schema::table('service_webs', function (Blueprint $table) {
            $table->foreign(['server_id'], 'fk_service_webs_servers1')->references(['id'])->on('servers')->onUpdate('no action')->onDelete('no action');
            $table->foreign(['service_id'], 'fk_service_webs_services1')->references(['id'])->on('services')->onUpdate('no action')->onDelete('no action');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('service_webs', function (Blueprint $table) {
            $table->dropForeign('fk_service_webs_servers1');
            $table->dropForeign('fk_service_webs_services1');
        });
    }
};
