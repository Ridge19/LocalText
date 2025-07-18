<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ApiKey extends Model
{
    public function user(){
        return $this->belongsTo(User::class);
    }

    public function scopeBelongsToUser($query)
    {
        return $query->where('user_id', auth()->id);
    }
}
