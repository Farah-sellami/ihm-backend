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
        Schema::table('postes', function (Blueprint $table) {
            // Modifier la colonne 'photos' en type 'json'
            $table->json('photos')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('postes', function (Blueprint $table) {
            // Revenir au type d'origine de 'photos' (par exemple 'string')
            $table->string('photos', 255)->change();
        });
    }
};
