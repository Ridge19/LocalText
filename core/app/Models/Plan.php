<?php

namespace App\Models;

use App\Constants\Status;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use App\Traits\GlobalStatus;

class Plan extends Model
{
    use GlobalStatus;

    public function contacts()
    {
        return $this->hasMany(Contact::class);
    }

    public function recurringtypeName(): Attribute
    {
        return Attribute::make(
            get: fn() => match ($this->recurring_type) {
                Status::MONTHLY_PLAN => 'Monthly',
                Status::YEARLY_PLAN => 'Yearly',
                default => 'others',
            },
        );
    }

    public function scopeActive($query)
    {
        return $query->where('status', Status::ENABLE);
    }
}
