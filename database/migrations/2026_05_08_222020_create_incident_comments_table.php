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
        Schema::create('incident_comments', function (Blueprint $table) {
            $table->id();

            // Usamos text() porque los comentarios suelen ser largos y superar los 255 caracteres de un string()
            $table->text('description');

            // Llave foránea conectada a la tabla incidents
            $table->foreignId('incident_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('user_id')
                ->constrained()
                ->cascadeOnDelete();

            // Crea automáticamente created_at y updated_at
            $table->timestamps();

            // Crea automáticamente la columna deleted_at para el Soft Deletion
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('incident_comments');
    }
};
