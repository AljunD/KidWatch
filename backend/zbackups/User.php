<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasOne;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes;

    protected $fillable = [
        'email',
        'password',
        'role',
        'is_active',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'is_active' => 'boolean',
    ];

    /**
     * Get the teacher profile associated with the user.
     * Changed to hasOne because teachers table holds the user_id.
     */
    public function teacher(): HasOne
    {
        return $this->hasOne(Teacher::class, 'user_id');
    }

    /**
     * Get the guardian profile associated with the user.
     * Changed to hasOne because guardians table holds the user_id.
     */
    public function guardian(): HasOne
    {
        return $this->hasOne(Guardian::class, 'user_id');
    }
}
