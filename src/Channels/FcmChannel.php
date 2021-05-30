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
     * @var \Closure|array
     */
    protected $globalPayload;

    /**
     * Create a new FCM channel instance.
     *
     * @param  \GuzzleHttp\Client  $http
     * @param  string  $apiKey
     * @param  \Closure|array  $globalPayload
     * @return void
     */
    public function __construct(HttpClient $http, string $apiKey, $globalPayload = null)
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
        $globalPayload = $this->globalPayload ?? [];
        if ($globalPayload instanceof Closure) {
            $globalPayload = $globalPayload($notification, $notifiable);
        }

        $payload = array_merge_recursive([
            'priority' => $message->priority,
            'data' => $message->data,
            'notification' => $message->notification,
            'condition' => $message->condition,
        ], $message->payload ?? [], $globalPayload);

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
}
