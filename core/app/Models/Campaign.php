<?php

namespace App\Models;

use App\Constants\Status;
use App\Traits\GlobalStatus;
use Illuminate\Database\Eloquent\Model;

class Campaign extends Model
{
    use GlobalStatus;
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getSim()
    {
        return $this->device_slot_number . '***' . $this->device_slot_name;
    }

    public function template()
    {
        return $this->belongsTo(Template::class);
    }

    public function allSms()
    {
        return $this->hasMany(Sms::class, 'campaign_id');
    }

    public function device()
    {
        return $this->belongsTo(Device::class);
    }

    public function scopeBelongsToUser($query)
    {
        return $query->where('user_id', auth()->id());
    }

    public function isCompleted()
    {
        return (!$this->allSms()->where('status', '!=', Status::SMS_DELIVERED)->exists());
    }

    public function campaignBadge()
    {
        if ($this->isCompleted() || $this->status == Status::CAMPAIGN_FINISHED) {
            return '<span class="badge badge--success">' . trans('Finished') . '</span>';
        }

        if ($this->status == Status::CAMPAIGN_RUNNING) {
            return '<span class="badge badge--primary">' . trans('Running') . '</span>';
        }

        if ($this->status == Status::CAMPAIGN_PENDING) {
            return '<span class="badge badge--warning">' . trans('Pending') . '</span>';
        }

        return '<span class="badge badge--dark">' . trans('Initiated') . '</span>';
    }

}
