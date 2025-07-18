<?php

namespace App\Models;

use App\Constants\Status;
use App\Traits\FileExport;
use App\Traits\GlobalStatus;
use Illuminate\Database\Eloquent\Model;

class Group extends Model
{
    use GlobalStatus, FileExport;

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function plan()
    {
        return $this->belongsTo(Plan::class);
    }

    public function contact()
    {
        return $this->belongsToMany(Contact::class, 'group_contacts', 'group_id');
    }

    public function scopeActive($query)
    {
        return $query->where('status', Status::ENABLE);
    }

    public function scopeList($query)
    {
        return $query->where('status', [Status::ENABLE, Status::DISABLE]);
    }

    public function scopeBelongsToUser($query)
    {
        return $query->where('user_id', auth()->id());
    }
}
