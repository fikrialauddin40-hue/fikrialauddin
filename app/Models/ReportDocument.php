<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReportDocument extends Model
{
    protected $fillable = [
        'daily_report_id',
        'file_path',
        'keterangan',
    ];

    public function dailyReport()
    {
        return $this->belongsTo(DailyReport::class);
    }
}
