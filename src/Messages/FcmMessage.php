<?php

namespace williamcruzme\FCM\Messages;

class FcmMessage
{
    /**
     * The devices token to send the message from.
     *
     * @var array|string
     */
    public $to;

    /**
     * The topic of the FCM message.
     *
     * @var array
     */
    public $topic;

    /**
     * The data of the FCM message.
     *
     * @var array
     */
    public $data;

    /**
     * The notification body of the FCM message.
     *
     * @var array
     */
    public $notification;

    /**
     * The condition for receive the FCM message.
     *
     * @var array
     */
    public $condition;

    /**
     * The priority of the FCM message.
     *
     * @var string
     */
    public $priority = 'normal';

    /**
     * The custom payload of the FCM message.
     *
     * @var array
     */
    public $payload = [];

    /**
     * Set the devices token to send the message from.
     *
     * @param  array|string  $to
     * @return $this
     */
    public function to($to)
    {
        if (is_array($to) && count($to) === 1) {
            $this->to = $to[0];
        } else {
            $this->to = $to;
        }

        return $this;
    }

    /**
     * Set the topic of the FCM message.
     *
     * @param  string  $topic
     * @return $this
     */
    public function topic(string $topic)
    {
        $this->topic = $topic;

        return $this;
    }

    /**
     * Set the data of the FCM message.
     *
     * @param  array  $data
     * @return $this
     */
    public function data(array $data)
    {
        $this->data = $data;

        return $this;
    }

    /**
     * Set the notification of the FCM message.
     *
     * @param  array  $notification
     * @return $this
     */
    public function notification(array $notification)
    {
        $this->notification = $notification;

        return $this;
    }

    /**
     * Set the condition for receive the FCM message.
     *
     * @param  string  $condition
     * @return $this
     */
    public function condition(string $condition)
    {
        $this->condition = $condition;

        return $this;
    }

    /**
     * Set the priority of the FCM message.
     *
     * @param  string  $priority
     * @return $this
     */
    public function priority(string $priority)
    {
        $this->priority = $priority;

        return $this;
    }

    /**
     * Set the custom payload of the FCM message.
     *
     * @param  array  $payload
     * @return $this
     */
    public function payload(array $payload)
    {
        $this->payload = $payload;

        return $this;
    }
}
