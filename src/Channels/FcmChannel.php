<?php

namespace williamcruzme\FCM\Channels;

use Closure;
use GuzzleHttp\Client as HttpClient;
use Illuminate\Notifications\Notification;
use williamcruzme\FCM\Messages\FcmMessage;

class FcmChannel
{
    /**
     * The API URL for FCM.
     *
     * @var string
     */
    const API_URI = 'https://fcm.googleapis.com/fcm/send';

    /**
     * The HTTP client instance.
     *
     * @var \GuzzleHttp\Client
     */
    protected $http;

    /**
     * The FCM API key.
     *
     * @var string
     */
    protected $apikey;

    /**
     * The global payload.
     *
     * @var array|\Closure
     */
    protected static $globalPayload = [];

    /**
     * Create a new FCM channel instance.
     *
     * @param  \GuzzleHttp\Client  $http
     * @param  string  $apiKey
     * @return void
     */
    public function __construct(HttpClient $http, string $apiKey)
    {
        $this->http = $http;
        $this->apiKey = $apiKey;
    }

    /**
     * Send the given notification.
     *
     * @param  mixed  $notifiable
     * @param  \Illuminate\Notifications\Notification  $notification
     * @return void
     */
    public function send($notifiable, Notification $notification)
    {
        $message = $notification->toFcm($notifiable);

        if (! $message->topic) {
            $message->to($notifiable->devices->pluck('token')->all());
        }

        if (! $this->apiKey || (! $message->topic && ! $message->to)) {
            return;
        }

        return $this->http->post(self::API_URI, [
            'headers' => [
                'Authorization' => "key={$this->apiKey}",
                'Content-Type' => 'application/json',
            ],
            'json' => $this->buildJsonPayload($message, $notifiable, $notification),
        ]);
    }

    /**
     * Send the given notification.
     *
     * @param  \williamcruzme\FCM\Messages\FcmMessage  $message
     * @param  mixed  $notifiable
     * @param  \Illuminate\Notifications\Notification  $notification
     * @return void
     */
    protected function buildJsonPayload(FcmMessage $message, $notifiable, $notification)
    {
        $globalPayload = static::$globalPayload;
        if ($globalPayload instanceof Closure) {
            $globalPayload = $globalPayload($notification, $notifiable);
        }

        $payload = array_merge_recursive([
            'priority' => $message->priority,
            'data' => $message->data,
            'notification' => $message->notification,
            'condition' => $message->condition,
        ], $message->payload ?? [], $globalPayload ?? []);

        $payload = array_filter($payload);

        if ($message->topic) {
            $payload['to'] = "/topics/{$message->topic}";
        } else {
            if (is_array($message->to)) {
                $payload['registration_ids'] = $message->to;
            } else {
                $payload['to'] = $message->to;
            }
        }

        return $payload;
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
