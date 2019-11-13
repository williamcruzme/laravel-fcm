
<h1 align="center" style="text-align:center">Laravel FCM</h1>

<p align="center">
  <a href="https://laravel.com/"><img src="https://badgen.net/badge/Laravel/6.x/red" alt="Laravel"></a>
</p>

<br>

laravel-fcm is a powerful [Laravel](https://laravel.com/) package to send [Push Notifications](https://firebase.google.com/docs/cloud-messaging) to one or many devices of the user. Being channel-based you only need to specify the `channel` in your Laravel [Notification](https://laravel.com/docs/master/notifications).

- [Installation](#-installation)
- [Getting Started](#-getting-started)
- [Usage](#-usage)
- [Routes](#-routes)
- [Customizing](#-customizing)

## 💿 Installation

```bash
composer require williamcruzme/laravel-fcm
```

#### Configure the enviroment

Get the key of Server Key and paste in your `.env` file:
<br>
*(gear-next-to-project-name) > Project Settings > Cloud Messaging*

```bash
FCM_KEY=
```

## 🏁 Getting Started

### 1. Adding traits

In your user model add the `HasDevices` trait. This trait supports custom guards:

```php
<?php

namespace App;

use williamcruzme\FCM\Traits\HasDevices;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use HasDevices;
}
```

### 2. Running migrations

```bash
php artisan migrate
```

Sometimes you may need to customize the migrations. Using the `vendor:publish` command you can export the migrations:

```bash
php artisan vendor:publish --tag=migrations
```

### 3. Creating notifications

```bash
php artisan make:notification InvoicePaid
```

### 4. Adding delivery channels

Every notification class has a `via` method that determines on which channels the notification will be delivered. Add `fcm` as delivery channel:

```php
/**
 * Get the notification's delivery channels.
 *
 * @param  mixed  $notifiable
 * @return array
 */
public function via($notifiable)
{
    return ['fcm'];
}

/**
 * Get the Firebase Message representation of the notification.
 *
 * @param  mixed  $notifiable
 * @return \williamcruzme\FCM\Messages\FcmMessage
 */
public function toFcm($notifiable)
{
    // ...
}
```

### 5. Adding routes
Using the `Device` facade to import the routes for manage the devices:

```php
Device::routes();
```

## 🚀 Usage

### Basic notification

`FcmMessage` automatically gets all devices of `$notifiable`. The `notification` method support the [Firebase payload](https://firebase.google.com/docs/cloud-messaging/http-server-ref#notification-payload-support):

```php
/**
 * Get the Firebase Message representation of the notification.
 *
 * @param  mixed  $notifiable
 * @return \williamcruzme\FCM\Messages\FcmMessage
 */
public function toFcm($notifiable)
{
    return (new FcmMessage)
                ->notification([
                    'title' => 'Happy Code!',
                    'body' => 'This is a test',
                ]);
}
```

### Specifying devices

Using the `to` method you can specific the device's to send the notification:

```php
/**
 * Get the Firebase Message representation of the notification.
 *
 * @param  mixed  $notifiable
 * @return \williamcruzme\FCM\Messages\FcmMessage
 */
public function toFcm($notifiable)
{
    return (new FcmMessage)
                ->to('fxssWy2Lgtk:APA91bFXy79AmofgTnBm5CfBpyeEFJsSHq0Xcdk...')
                ->notification([
                    'title' => 'Happy Code!',
                    'body' => 'This is a test',
                ]);
}
```

### Specifying topics

Using the `topic` method you can specific the topic to send the notification. This method ignores the devices of `$notifiable`:

```php
/**
 * Get the Firebase Message representation of the notification.
 *
 * @param  mixed  $notifiable
 * @return \williamcruzme\FCM\Messages\FcmMessage
 */
public function toFcm($notifiable)
{
    return (new FcmMessage)
                ->topic('news')
                ->notification([
                    'title' => 'Happy Code!',
                    'body' => 'This is a test',
                ]);
}
```

### Sending data

Using the `data` method you can specify the custom key-value pairs of the notification payload:

```php
/**
 * Get the Firebase Message representation of the notification.
 *
 * @param  mixed  $notifiable
 * @return \williamcruzme\FCM\Messages\FcmMessage
 */
public function toFcm($notifiable)
{
    return (new FcmMessage)
                ->notification([
                    'title' => 'Happy Code!',
                    'body' => 'This is a test',
                ])
                ->data([
                    'type' => 'banner',
                    'link' => 'https://github.com/',
                ]);
}
```

### Adding conditions

Using the `condition` method you can specify a boolean expression to send the notification:

```php
/**
 * Get the Firebase Message representation of the notification.
 *
 * @param  mixed  $notifiable
 * @return \williamcruzme\FCM\Messages\FcmMessage
 */
public function toFcm($notifiable)
{
    return (new FcmMessage)
                ->condition("'stock-GOOG' in topics || 'industry-tech' in topics")
                ->notification([
                    'title' => 'Happy Code!',
                    'body' => 'This is a test',
                ]);
}
```

### Setting priority

Using the `priority` method you can specify a priority of the notification. Default is `normal`:

```php
/**
 * Get the Firebase Message representation of the notification.
 *
 * @param  mixed  $notifiable
 * @return \williamcruzme\FCM\Messages\FcmMessage
 */
public function toFcm($notifiable)
{
    return (new FcmMessage)
                ->priority('high')
                ->notification([
                    'title' => 'Happy Code!',
                    'body' => 'This is a test',
                ]);
}
```

## 🌐 Routes

### Add device

| Method |    URI     |
| ------ | ---------- |
| POST   | `/devices` |

#### Body Params

```json
{
    "token": "fxssWy2Lgtk:APA91bFXy79AmofgTnBm5CfBpyeEFJsSHq0Xcdk...",
}
```

### Remove device

| Method |           URI            |
| ------ | ------------------------ |
| DELETE | `/devices/{deviceToken}` |

## 🎨 Customizing

First of all, create your own `DeviceController` controller and add the `ManageDevices` trait.

Second, modify the namespace of the `Device` facade routes:

```php
Device::routes('App\Http\Controllers');
```

### Custom request validations

The `rules` `validationErrorMessages` methods in the `DeviceController` allows you override the default request validations:

```php
use williamcruzme\FCM\Traits\ManageDevices;

class DeviceController extends Controller {

    use ManageDevices;
    
    /**
     * Get the device management validation rules.
     *
     * @return array
     */
    protected function rules()
    {
        return [
            'token' => ['required', 'string', 'unique:devices'],
        ];
    }

    /**
     * Get the device management validation error messages.
     *
     * @return array
     */
    protected function validationErrorMessages()
    {
        return [];
    }
}
```

### Custom guards

The `guard` method in the `DeviceController` allows you override the default guard:

```php
use williamcruzme\FCM\Traits\ManageDevices;

class DeviceController extends Controller {

    use ManageDevices;
    
    /**
     * Get the guard to be used during device management.
     *
     * @return \Illuminate\Contracts\Auth\StatefulGuard
     */
    protected function guard()
    {
        return auth('admin')->guard();
    }
}
```

## 🚸 Contributing

You are welcome to contribute to this project, but before you do, please make sure you read the [contribution guide](CONTRIBUTING.md).

## 🔒 License

MIT
