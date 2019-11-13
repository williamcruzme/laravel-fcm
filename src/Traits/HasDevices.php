<?php

namespace williamcruzme\FCM\Traits;

use williamcruzme\FCM\Device;

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
}
