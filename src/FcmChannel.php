<?php

namespace Williamcruzme\Fcm;

use Closure;
use Illuminate\Notifications\Notification;
use Kreait\Firebase\Exception\MessagingException;
use Williamcruzme\Fcm\Exceptions\CouldNotSendNotification;

class FcmChannel
{
    const MAX_TOKEN_PER_REQUEST = 500;

    /**
     * @var string|null
     */
    protected static $fcmProject = null;

    /**
     * @var array|\Closure
     */
    protected static $globalPayload = [];

    /**
     * Set project ID.
     *
     * @param  string  $project
     * @return void
     */
    public static function setProject($project)
    {
        static::$fcmProject = $project;
    }

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
        $token = $notifiable->routeNotificationFor('fcm', $notification);
        if (empty($token)) {
            return;
        }

        // Get the message from the notification class
        $message = $notification->toFcm($notifiable);
        if (! $message instanceof FcmMessage) {
            throw CouldNotSendNotification::invalidMessage();
        }

        // Apply global payload
        if ($globalPayload = static::$globalPayload) {
            $message = $globalPayload($message, $notification, $notifiable);
        }

        try {
            // Get device token
            $token = is_array($token) && count($token) === 1 ? $token[0] : $token;

            if (is_array($token)) {
                // Use multicast when there are multiple recipients
                $partialTokens = array_chunk($token, self::MAX_TOKEN_PER_REQUEST, false);
                foreach ($partialTokens as $tokens) {
                    $this->sendToFcmMulticast($message, $tokens);
                }
            } else {
                $this->sendToFcm($message, $token);
            }
        } catch (MessagingException $exception) {
            throw CouldNotSendNotification::serviceRespondedWithAnError($exception);
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
     * @param  \Williamcruzme\Fcm\FcmMessage  $message
     * @param  string  $token
     * @return array
     * @throws \Kreait\Firebase\Exception\MessagingException
     * @throws \Kreait\Firebase\Exception\FirebaseException
     */
    protected function sendToFcm(FcmMessage $message, string $token)
    {
        $message->token($token);

        return $this->messaging()->send($message);
    }

    /**
     * @param  \Williamcruzme\Fcm\FcmMessage  $message
     * @param  array|string  $token
     * @return array
     * @return \Kreait\Firebase\Messaging\MulticastSendReport
     * @throws \Kreait\Firebase\Exception\MessagingException
     * @throws \Kreait\Firebase\Exception\FirebaseException
     */
    protected function sendToFcmMulticast(FcmMessage $message, array $tokens)
    {
        return $this->messaging()->sendMulticast($message, $tokens);
    }

    /**
     * Set global payload.
     *
     * @param  array|\Closure  $payload
     * @return void
     */
    public static function setPayload($payload)
    {
        static::$globalPayload = $payload;
    }
}
