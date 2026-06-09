<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['kelas', 'jurusan', 'tempat_psg', 'nis', 'pembimbing', 'sekolah']);
            $table->string('partner_code', 20)->nullable()->unique()->after('no_hp');
            $table->foreignId('partner_id')->nullable()->constrained('users')->nullOnDelete()->after('partner_code');
            $table->enum('gender', ['male', 'female'])->nullable()->after('partner_id');
            $table->date('birth_date')->nullable()->after('gender');
            $table->date('anniversary_date')->nullable()->after('birth_date');
            $table->text('bio')->nullable()->after('anniversary_date');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['partner_id']);
            $table->dropColumn(['partner_code', 'partner_id', 'gender', 'birth_date', 'anniversary_date', 'bio']);
            $table->string('kelas')->nullable();
            $table->string('jurusan')->nullable();
            $table->string('tempat_psg')->nullable();
            $table->string('nis')->nullable();
            $table->string('pembimbing')->nullable();
            $table->string('sekolah')->nullable();
        });
    }
};
