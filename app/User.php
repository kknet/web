<?php

namespace App;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
        'confirmation_key',
        'ip',
    ];

    protected $casts = [
        'email_verified' => 'bool',
        'captcha_verified' => 'bool',
    ];

    protected $dates = [
        'wallet_updated_at',
    ];

    /**
     * Limits results to a given email
     *
     * @param Builder $query
     * @param $email
     * @return Builder
     */
    public function scopeWithEmail(Builder $query, $email)
    {
        return $query->where('email', '=', $email);
    }

    /**
     * Referrals relationship
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function referrals()
    {
        return $this->hasMany(User::class, 'affiliate_id');
    }

    /**
     * Affiliate relationship
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function affiliate()
    {
        return $this->belongsTo(User::class, 'affiliate_id');
    }

    /**
     * Email confirmations relationship
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function emailConfirmations()
    {
        return $this->hasMany(EmailConfirmation::class);
    }

    /**
     * Returns true if current user participated in airdrop
     * @return bool
     */
    public function isAirdropParticipant()
    {
        return $this->airdrop > 0;
    }

    /**
     * Always save email in lowercase
     * @param $value
     */
    public function setEmailAttribute($value)
    {
        $this->attributes['email'] = strtolower($value);
    }

    /**
     * Identity mutator
     * @param $value
     */
    public function setIdentificationAttribute($value)
    {
        $this->attributes['identification'] = is_null($value) ? null : json_encode($value);
    }

    /**
     * Identity accessor
     * @param $value
     * @return mixed
     */
    public function getIdentificationAttribute($value)
    {
        return is_null($value) ? $value : json_decode($value);
    }
}
