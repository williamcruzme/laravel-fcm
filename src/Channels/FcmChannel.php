<?php

namespace williamcruzme\FCM\Channels;

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
     * @var array
     */
    protected $globalPayload;

    /**
     * Create a new FCM channel instance.
     *
     * @param  \GuzzleHttp\Client  $http
     * @param  string  $apiKey
     * @param  array  $globalPayload
     * @return void
     */
    public function __construct(HttpClient $http, string $apiKey, array $globalPayload = [])
    {
        $this->http = $http;
        $this->apiKey = $apiKey;
        $this->globalPayload = $globalPayload;
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
            'json' => $this->buildJsonPayload($message),
        ]);
    }

    protected function buildJsonPayload(FcmMessage $message)
    {
        $payload = array_filter([
            'priority' => $message->priority,
            'data' => $message->data,
            'notification' => $message->notification,
            'condition' => $message->condition,
        ] + ($message->payload ?? []) + ($this->globalPayload ?? []));

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
}
