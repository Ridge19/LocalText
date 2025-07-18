<?php

namespace App\Models;

use App\Constants\Status;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;

class Sms extends Model
{

    protected $guarded = ['id'];

    protected $casts = [
        'device_sim_slot' => 'array'
    ];

    protected $appends = ['formatted_message'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function device()
    {
        return $this->belongsTo(Device::class, 'device_id');
    }

    public function failReason()
    {
        return $this->belongsTo(SmsFailedErrorCode::class, 'error_code', 'code');
    }

    public function scopeSend($query)
    {
        $query->whereIn('status', [Status::SMS_INITIAL, Status::SMS_SCHEDULED])->where('et', Status::NO);
    }

    public function scopeInitiated($query)
    {
        $query->where('status', Status::SMS_INITIAL);
    }

    public function scopeDelivered($query)
    {
        $query->where('status', Status::SMS_DELIVERED);
    }

    public function scopeFailed($query)
    {
        $query->where('status', Status::SMS_FAILED)->where('et', Status::NO);
    }

    public function scopeScheduled($query)
    {
        $query->where('status', Status::SMS_SCHEDULED)->where('et', Status::NO);
    }

    public function scopeReSend($query)
    {
        $query->whereNotIn('status', [Status::SMS_FAILED, Status::SMS_DELIVERED, Status::SMS_PROCESSING])->where('et', Status::YES);
    }

    public function scopeBelongsToUser($query)
    {
        return $query->where('user_id', auth()->id());
    }

    public function contact()
    {
        return Contact::whereRaw("CONCAT(dial_code, mobile) = ?", [$this->mobile_number])->first();
    }


    public function formattedMessage(): Attribute
    {
        return new Attribute(function () {
            $contact = $this->contact();
            $values = [
                $contact->firstname ?? '',
                $contact->lastname ?? '',
                $contact->email ?? '',
                $contact->state ?? '',
                $contact->city ?? '',
                $contact->zip ?? '',
                $contact->country ?? ''
            ];

            $message = str_replace(showShortCodes(), $values, $this->message);

            return trim($message);
        });
    }
}
