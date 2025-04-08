<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('CIN')->unique();
            $table->string('nom');
            $table->string('prenom'); // Renamed from prénom to prenom
            $table->date('dateNaissance');
            $table->string('ville');
            $table->string('photoProfil')->nullable();
            $table->string('motDePasse');
            $table->tinyInteger('role')->default(1); // 0 = admin, 1 = normal user
            $table->enum('type', ['A', 'V'])->default('A'); // Par défaut, tous les utilisateurs sont acheteurs
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('users');
    }
};
