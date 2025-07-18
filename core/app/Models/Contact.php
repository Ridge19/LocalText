<?php

namespace App\Models;

use App\Constants\Status;
use App\Traits\FileExport;
use App\Traits\GlobalStatus;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;

class Contact extends Model
{
    use GlobalStatus, FileExport;

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function groupContact()
    {
        return $this->belongsToMany(Group::class, 'group_contacts', 'contact_id');
    }

    public function campaignContact()
    {
        return $this->belongsToMany(Campaign::class, 'campaign_contacts', 'contact_id');
    }

    public function scopeActive($query)
    {
        return $query->where('status', Status::ENABLE);
    }

    public function scopeBanned($query)
    {
        return $query->where('status', Status::DISABLE);
    }

    public function scopeBelongsToUser($query)
    {
        return $query->where('user_id', auth()->id());
    }

    public function fullname(): Attribute
    {
        return new Attribute(
            get: fn() => $this->firstname . ' ' . $this->lastname,
        );
    }
}
