<?php

namespace App\Models;

use App\Enums\User\UserRole;
use App\Events\UserRegistered;
use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable implements MustVerifyEmail
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasApiTokens;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'avatar',
        'role',
        'email_verified_at',
        'verify_otp',
        'email_otp_expires_at'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'role' => UserRole::class,
        ];
    }


    public function tickets()
    {
        return $this->hasMany(Ticket::class);
    }


    public function messages()
    {
        return $this->hasMany(Message::class);
    }


    public function generateAndSendVerificationCode(): void
    {
        $this->update([
            'verify_otp' => rand(100000, 999999),
            'email_otp_expires_at' => now()->addMinutes(10),
        ]);

        event(new UserRegistered($this, $this->verify_otp));
    }


    public function isAdmin(): bool
    {
        return $this->role === UserRole::ADMIN;
    }
}
