<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('daily_reports', function (Blueprint $table) {
            $table->dropColumn(['kegiatan', 'status', 'catatan']);
            $table->renameColumn('tanggal', 'report_date');
            $table->renameColumn('deskripsi', 'activity');
            $table->string('mood', 50)->nullable()->after('report_date');
            $table->time('wake_up_time')->nullable()->after('mood');
            $table->time('sleep_time')->nullable()->after('wake_up_time');
            $table->text('description')->nullable()->after('activity');
            $table->text('gratitude')->nullable()->after('description');
            $table->text('private_note')->nullable()->after('gratitude');
        });
    }

    public function down(): void
    {
        Schema::table('daily_reports', function (Blueprint $table) {
            $table->dropColumn(['mood', 'wake_up_time', 'sleep_time', 'description', 'gratitude', 'private_note']);
            $table->renameColumn('report_date', 'tanggal');
            $table->renameColumn('activity', 'deskripsi');
            $table->string('kegiatan');
            $table->enum('status', ['selesai', 'proses', 'tertunda'])->default('proses');
            $table->text('catatan')->nullable();
        });
    }
};
