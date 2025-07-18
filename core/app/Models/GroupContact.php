<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GroupContact extends Model
{
    public function contact()
    {
        return $this->belongsTo(Contact::class, 'contact_id');
    }

    public function group()
    {
        return $this->belongsTo(Group::class, 'group_id');
    }

    public function scopeBelongsToUser($query)
    {
        return $query->where('user_id', auth()->id());
    }
}
