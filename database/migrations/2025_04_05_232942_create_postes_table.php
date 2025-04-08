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
        Schema::create('postes', function (Blueprint $table) {
            $table->id();
            $table->string('titre', 100);
            $table->string('photos', 255);
            $table->string('description', 100);
            $table->float('prixIniale');
            $table->string('duree',50);
            $table->boolean('estApprouvé');
            $table->unsignedBigInteger('scategorieID');
            $table->foreign('scategorieID')
                ->references('id')
                ->on('scategories')
                ->onDelete('restrict');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('postes');
    }
};
