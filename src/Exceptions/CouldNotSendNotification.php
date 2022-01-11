<?php

namespace Williamcruzme\Fcm\Exceptions;

use Exception;
use Williamcruzme\Fcm\FcmMessage;

class CouldNotSendNotification extends Exception
{
    public static function invalidMessage()
    {
        return new static('The toFcm() method only accepts instances of '.FcmMessage::class);
    }
}
