<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('referentiels', function (Blueprint $table) {
            $table->id();
            $table->string('codeReferentiel')->unique();
            $table->string('libelleReferentiel')->unique();
            $table->enum('StatutReferentiel', ['Actif', 'Inactif', 'Archiver']);
            $table->string('photo')->nullable();
            $table->json('description'); // Stocker les compétences et modules sous forme JSON

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('referentiels');
    }
};
