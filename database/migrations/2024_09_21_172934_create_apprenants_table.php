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
        Schema::create('apprenants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users2'); // Référence à la table des utilisateurs
            $table->string('nom_tuteur');
            $table->string('contact_tuteur');
            $table->string('photocopie_cni')->nullable();
            $table->string('diplome')->nullable();
            $table->string('visite_medicale')->nullable();
            $table->string('extrait_naissance')->nullable();
            $table->string('casier_judiciaire')->nullable();
            $table->foreignId('referentiel_id')->constrained('referentiels');
            $table->decimal('moyenne', 5, 2)->nullable();
            $table->string('appreciation')->nullable();
            $table->json('notes')->nullable(); // Stocker les notes sous forme JSON

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('apprenants');
    }
};
