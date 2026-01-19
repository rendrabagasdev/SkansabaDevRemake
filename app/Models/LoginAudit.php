<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LoginAudit extends Model
{
    protected $fillable = [
        'email',
        'user_id',
        'status',
        'ip_address',
        'user_agent',
        'failure_reason',
        'attempted_at'
    ];

    protected $casts = [
        'attempted_at' => 'datetime'
    ];

    /**
     * Get the user associated with the audit
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Check if login was successful
     */
    public function isSuccessful(): bool
    {
        return $this->status === 'success';
    }

    /**
     * Check if login failed
     */
    public function isFailed(): bool
    {
        return $this->status === 'failed';
    }

    /**
     * Scope: Successful logins
     */
    public function scopeSuccessful($query)
    {
        return $query->where('status', 'success');
    }

    /**
     * Scope: Failed logins
     */
    public function scopeFailed($query)
    {
        return $query->where('status', 'failed');
    }

    /**
     * Scope: Recent attempts for an email
     */
    public function scopeRecentAttempts($query, string $email, int $minutes = 15)
    {
        return $query->where('email', $email)
            ->where('attempted_at', '>=', now()->subMinutes($minutes));
    }

    /**
     * Count recent failed attempts for rate limiting
     */
    public static function countRecentFailedAttempts(string $email, int $minutes = 15): int
    {
        return static::failed()
            ->recentAttempts($email, $minutes)
            ->count();
    }
}
