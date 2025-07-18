<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\SupportMessage;

class SupportTicket extends Model
{
    use HasFactory;

    /**
     * Disable guarded mass assignment protection for this model.
     *
     * @var array<int, string>
     */
    protected $guarded = [];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'last_reply' => 'datetime',
    ];

    /**
     * Get all messages for this ticket.
     */
    public function messages()
    {
        return $this->hasMany(SupportMessage::class, 'support_ticket_id', 'id');
    }
}
