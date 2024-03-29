<?php

namespace Williamcruzme\Fcm;

use Williamcruzme\Fcm\Device;

trait HasDevices
{
    /**
     * Get the devices of the model.
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany
     */
    public function devices()
    {
        return $this->morphMany(Device::class, 'user');
    }

    /**
     * Specifies the model's FCM token.
     *
     * @return array
     */
    public function routeNotificationForFcm()
    {
        return $this->devices()->pluck('token')->all();
    }
}
