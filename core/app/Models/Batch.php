<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Batch extends Model
{

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function sms()
    {
        return $this->hasMany(Sms::class, 'batch_id');
    }

    public function scopeBelongsToUser($query)
    {
        return $query->where('user_id', auth()->id());
    }
}
