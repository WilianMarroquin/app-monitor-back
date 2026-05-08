<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('area_notification_contact', function (Blueprint $table) {
            $table->foreignId('area_id')->constrained()->cascadeOnDelete();
            $table->foreignId('notification_contact_id')->constrained()->cascadeOnDelete();

            $table->primary(['area_id', 'notification_contact_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('area_notification_contact');
    }
};
