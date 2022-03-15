<?php

namespace Williamcruzme\Fcm;

use Closure;
use Illuminate\Notifications\Notification;
use Williamcruzme\Fcm\Exceptions\CouldNotSendNotification;

class FcmChannel
{
    /**
     * @var int
     */
    const MAX_TOKEN_PER_REQUEST = 500;

    /**
     * @var \Closure|null
     */
    protected static $beforeSendingCallback;

    /**
     * Send the given notification.
     *
     * @param mixed $notifiable
     * @param \Illuminate\Notifications\Notification $notification
     *
     * @return void
     * @throws \Williamcruzme\Fcm\Exceptions\CouldNotSendNotification
     * @throws \Kreait\Firebase\Exception\FirebaseException
     */
    public function send($notifiable, Notification $notification)
    {
        // Get device token
        $token = $notifiable->routeNotificationFor('fcm', $notification);
        if (empty($token)) {
            return;
        }

        $token = is_array($token) && count($token) === 1 ? $token[0] : $token;

        // Get the message from the notification
        $message = $notification->toFcm($notifiable);
        if (! $message instanceof FcmMessage) {
            throw CouldNotSendNotification::invalidMessage();
        }

        // Apply before sending callback
        if ($callback = static::$beforeSendingCallback) {
            $message = $callback($message, $notification, $notifiable);
        }

        try {
            // Send notification
            if (is_array($token)) {
                $partialTokens = array_chunk($token, self::MAX_TOKEN_PER_REQUEST, false);
                foreach ($partialTokens as $tokens) {
                    $report = $this->messaging()->sendMulticast($message, $tokens);
                    $unknownTokens = $report->unknownTokens();
                    if (! empty($unknownTokens)) {
                        $notifiable->devices()->whereIn('token', $unknownTokens)->get()->each->delete();
                    }
                }
            } else {
                $message->token($token);
                $this->messaging()->send($message);
            }
        } catch (\Exception $exception) {
            $notifiable->devices()->get()->each->delete();
        }
    }

    /**
     * @return \Kreait\Firebase\Messaging
     */
    protected function messaging()
    {
        return app('firebase.messaging');
    }

    /**
     * Set the callback to be run before sending message.
     *
     * @param  \Closure  $callback
     * @return void
     */
    public static function beforeSending(Closure $callback)
    {
        static::$beforeSendingCallback = $callback;
    }
}
