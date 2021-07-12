<?php

namespace williamcruzme\FCM;

use Kreait\Firebase\Messaging\Message;

class FcmMessage implements Message
{
    /**
     * @var string|null
     */
    protected $name;

    /**
     * @var array|null
     */
    protected $data;

    /**
     * @var array|null
     */
    protected $notification;

    /**
     * @var array|null
     */
    protected $android;

    /**
     * @var array|null
     */
    protected $webpush;

    /**
     * @var array|null
     */
    protected $apns;

    /**
     * @var array|null
     */
    protected $fcmOptions;

    /**
     * @var string|null
     */
    protected $condition;

    /**
     * @var string|null
     */
    protected $token;

    /**
     * Set the name of the FCM message.
     *
     * @param  array  $name
     * @return $this
     */
    public function name(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Set the data of the FCM message.
     *
     * @param  array  $data
     * @return $this
     */
    public function data(array $data): self
    {
        if ($this->data) {
            $this->data += $data;
        } else {
            $this->data = $data;
        }

        return $this;
    }

    /**
     * Set the notification of the FCM message.
     *
     * @param  array  $notification
     * @return $this
     */
    public function notification(array $notification): self
    {
        if ($this->notification) {
            $this->notification += $notification;
        } else {
            $this->notification = $notification;
        }

        return $this;
    }

    /**
     * Set the android payload of the FCM message.
     *
     * @param  array  $android
     * @return $this
     */
    public function android(array $android): self
    {
        $this->android = $android;

        return $this;
    }

    /**
     * Set the Web Push payload of the FCM message.
     *
     * @param  array  $webpush
     * @return $this
     */
    public function webpush(array $webpush): self
    {
        $this->webpush = $webpush;

        return $this;
    }

    /**
     * Set the APNS payload of the FCM message.
     *
     * @param  array  $apns
     * @return $this
     */
    public function apns(array $apns): self
    {
        $this->apns = $apns;

        return $this;
    }

    /**
     * Set the FCM options of the FCM message.
     *
     * @param  array  $fcmOptions
     * @return $this
     */
    public function fcmOptions(array $fcmOptions): self
    {
        $this->fcmOptions = $fcmOptions;

        return $this;
    }

    /**
     * Set the condition for receive the FCM message.
     *
     * @param  string  $condition
     * @return $this
     */
    public function condition(string $condition): self
    {
        $this->condition = $condition;

        return $this;
    }

    /**
     * Set the token of the FCM message.
     *
     * @param  string  $token
     * @return $this
     */
    public function token(string $token): self
    {
        $this->token = $token;

        return $this;
    }

    /**
     * Convert payload to array.
     *
     * @return array
     */
    public function toArray()
    {
        return [
            'name' => $this->name,
            'data' => $this->data,
            'notification' => $this->notification,
            'android' => $this->android,
            'webpush' => $this->webpush,
            'apns' => $this->apns,
            'fcm_options' => $this->fcmOptions,
            'condition' => $this->condition,
            'token' => $this->token,
        ];
    }

    /**
     * Convert payload to array.
     *
     * @return array
     */
    public function jsonSerialize()
    {
        return $this->toArray();
    }
}
