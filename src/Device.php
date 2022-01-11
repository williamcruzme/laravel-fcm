<?php

namespace Williamcruzme\Fcm;

use Illuminate\Database\Eloquent\Model;

class Device extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'token',
        'user_type',
        'user_id',
    ];

    /**
     * Get the user that owns the device.
     */
    public function user()
    {
        return $this->morphTo();
    }
}
