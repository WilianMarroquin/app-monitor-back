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
        Schema::create('incident_has_notificacion', function (Blueprint $table) {
            $table->foreignId('incident_id')->constrained('incidents');
            $table->foreignId('notification_contact_id')->constrained('notification_contacts');
            $table->enum('status', ['open', 'resolved', 'follow-up']);
            $table->string('number', 10)->nullable();

            $table->primary(['incident_id', 'notification_contact_id', 'status']);

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('incident_has_notificacion');
    }
};
