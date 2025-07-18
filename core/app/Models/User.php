<?php

namespace App\Models;

use App\Constants\Status;
use App\Traits\UserNotify;
use Carbon\Carbon;
use Exception;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Facades\Schema;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, UserNotify;

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
        'ver_code',
        'balance'
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'ver_code_send_at' => 'datetime'
    ];

    public function activePlan()
    {
        if (Carbon::parse($this->plan_expires_at)->lt(now())) {
            return null;
        }

        return Plan::where('id', $this->plan_id)->first();
    }

    public function purchasePlans()
    {
        return $this->hasMany(PurchasePlan::class);
    }

    public function contacts()
    {
        return $this->hasMany(Contact::class);
    }

    public function activeApiKey()
    {
        return $this->hasOne(ApiKey::class, 'user_id')->where('status', Status::ENABLE);
    }

    public function apiKey()
    {
        return $this->hasMany(ApiKey::class, 'user_id');
    }

    public function loginLogs()
    {
        return $this->hasMany(UserLogin::class);
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class)->orderBy('id', 'desc');
    }

    public function deposits()
    {
        return $this->hasMany(Deposit::class)->where('status', '!=', Status::PAYMENT_INITIATE);
    }

    public function tickets()
    {
        return $this->hasMany(SupportTicket::class);
    }

    public function fullname(): Attribute
    {
        return new Attribute(
            get: fn() => $this->firstname . ' ' . $this->lastname,
        );
    }

    public function mobileNumber(): Attribute
    {
        return new Attribute(
            get: fn() => $this->dial_code . $this->mobile,
        );
    }

    // SCOPES
    public function scopeActive($query)
    {
        return $query->where('status', Status::USER_ACTIVE)->where('ev', Status::VERIFIED)->where('sv', Status::VERIFIED);
    }

    public function scopeBanned($query)
    {
        return $query->where('status', Status::USER_BAN);
    }

    public function scopeEmailUnverified($query)
    {
        return $query->where('ev', Status::UNVERIFIED);
    }

    public function scopeMobileUnverified($query)
    {
        return $query->where('sv', Status::UNVERIFIED);
    }

    public function scopeEmailVerified($query)
    {
        return $query->where('ev', Status::VERIFIED);
    }

    public function scopeMobileVerified($query)
    {
        return $query->where('sv', Status::VERIFIED);
    }

    public function scopeWithBalance($query)
    {
        return $query->where('balance', '>', 0);
    }

    public function deviceTokens()
    {
        return $this->hasMany(DeviceToken::class);
    }

    public function hasLimit($column, $quantity = 0, $hasInfinite = true)
    {
        if (!Schema::hasColumn('users', $column)) {
            throw new Exception("{$column} column not found");
        }

        $columnValue = $this->$column;

        if ($hasInfinite && $columnValue == Status::UNLIMITED) {
            return true;
        }

        return $columnValue > 0 && $columnValue >= $quantity;
    }   


    public function subtractLimitCounter($column, $quantity = 1)
    {

        if (!Schema::hasColumn('users', $column)) {
            throw new Exception($column . ' column not found');
        }

        if ($this->$column != Status::UNLIMITED) {
            $this->$column -= $quantity;
            $this->save();
        }
    }
}
