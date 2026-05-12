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
        Schema::create('services', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->text('description')->nullable();
            $table->enum('type', ['web', 'database']);
            $table->tinyInteger('is_active');
            $table->string('testMethod', 45)->nullable();
            $table->string('httpMethod', 45)->nullable();
            $table->string('port', 10)->nullable();
            $table->string('tiempo_espera', 10)->nullable();
            $table->enum('entorno', ['Desarrollo', 'Produccion'])->nullable();
            $table->unsignedBigInteger('server_id')
                ->index('fk_service_webs_servers1_idx')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign(['server_id'], 'fk_service_webs_servers1')->references(['id'])->on('servers')->onUpdate('no action')->onDelete('no action');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('service_webs', function (Blueprint $table) {
            $table->dropForeign('fk_service_webs_servers1');
        });
        Schema::dropIfExists('services');
    }
};
