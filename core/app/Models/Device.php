<?php

namespace App\Models;

use App\Constants\Status;
use App\Traits\FileExport;
use App\Traits\GlobalStatus;
use Illuminate\Database\Eloquent\Model;

class Device extends Model
{
    use GlobalStatus, FileExport;

    protected $casts = [
        'sim' => 'array'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function sms()
    {
        return $this->hasMany(Sms::class);
    }

    public function scopeConnected($query)
    {
        return $query->where('status', Status::YES);
    }

    public function scopeDisconnected($query)
    {
        return $query->where('status', Status::NO);
    }

    public function scopeBelongsToUser($query)
    {
        return $query->where('user_id', auth()->id());
    }
}
