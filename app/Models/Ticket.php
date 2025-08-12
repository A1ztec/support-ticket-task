<?php

namespace App\Models;

use App\Enums\Ticket\TicketStatus;
use Illuminate\Database\Eloquent\Model;

class Ticket extends Model
{
    protected $fillable = [
        'subject',
        'message',
        'user_id',
        'status',
        'attachment',
    ];


    protected function casts(): array
    {
        return [
            'status' => TicketStatus::class,
        ];
    }


    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function messages()
    {
        return $this->hasMany(Message::class);
    }


    public function scopeInProgress($query)
    {
        return $query->where('status', TicketStatus::IN_PROGRESS);
    }

    public function scopeClosed($query)
    {
        return $query->where('status', TicketStatus::CLOSED);
    }

    public function scopeOpen($query)
    {
        return $query->where('status', TicketStatus::OPEN);
    }
}
