<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DailyReport extends Model
{
    protected $fillable = [
        'user_id',
        'report_date',
        'mood',
        'wake_up_time',
        'sleep_time',
        'activity',
        'description',
        'gratitude',
        'private_note',
    ];

    protected function casts(): array
    {
        return [
            'report_date' => 'date',
            'wake_up_time' => 'datetime:H:i',
            'sleep_time' => 'datetime:H:i',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function documents()
    {
        return $this->hasMany(ReportDocument::class);
    }

    public function scopeToday($query)
    {
        return $query->whereDate('report_date', today());
    }

    public function scopeByUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeForCouple($query, $userId, $partnerId)
    {
        return $query->whereIn('user_id', [$userId, $partnerId]);
    }
}
