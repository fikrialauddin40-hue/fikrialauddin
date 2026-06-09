<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'no_hp',
        'foto',
        'partner_code',
        'partner_id',
        'gender',
        'birth_date',
        'anniversary_date',
        'bio',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'birth_date' => 'date',
            'anniversary_date' => 'date',
        ];
    }

    public function dailyReports()
    {
        return $this->hasMany(DailyReport::class);
    }

    public function partner()
    {
        return $this->belongsTo(User::class, 'partner_id');
    }

    public function hasPartner(): bool
    {
        return !is_null($this->partner_id);
    }

    public function isPartnerWith(?User $user): bool
    {
        return $user && $this->partner_id === $user->id;
    }

    public function getPartnerReports()
    {
        if (!$this->hasPartner()) return collect();
        return DailyReport::where('user_id', $this->partner_id)
            ->orderBy('report_date', 'desc');
    }

    public function scopeAvailableForPartner($query)
    {
        return $query->whereNull('partner_id')
            ->whereNotNull('partner_code')
            ->where('id', '!=', $this->id ?? 0);
    }
}
