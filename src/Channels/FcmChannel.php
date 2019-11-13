<?php

namespace williamcruzme\FCM\Channels;

use GuzzleHttp\Client as HttpClient;
use williamcruzme\FCM\Messages\FcmMessage;
use Illuminate\Notifications\Notification;

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
        ]);

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
