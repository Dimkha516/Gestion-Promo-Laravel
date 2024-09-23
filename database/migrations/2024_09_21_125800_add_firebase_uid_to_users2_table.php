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
        Schema::table('users2', function (Blueprint $table) {
            $table->string('firebase_uid')->nullable()->after('id'); // Ou après la colonne que vous préférez
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users2', function (Blueprint $table) {
            $table->dropColumn('firebase_uid');

        });
    }
};
