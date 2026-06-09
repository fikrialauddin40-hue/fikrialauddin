<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('kelas')->nullable();
            $table->string('jurusan')->nullable();
            $table->string('tempat_psg')->nullable();
            $table->string('no_hp')->nullable();
            $table->string('foto')->nullable();
            $table->string('nis')->nullable();
            $table->string('pembimbing')->nullable();
            $table->string('sekolah')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['kelas', 'jurusan', 'tempat_psg', 'no_hp', 'foto', 'nis', 'pembimbing', 'sekolah']);
        });
    }
};
