
<h1 align="center" style="text-align:center">Laravel FCM</h1>

<p align="center">
  <a href="https://laravel.com/"><img src="https://badgen.net/badge/Laravel/>= 5.x/green" alt="Laravel"></a>
  <a href="https://github.com/williamcruzme/laravel-fcm"><img src="https://img.shields.io/github/license/williamcruzme/laravel-fcm" alt="GitHub"></a>
</p>

<br>

laravel-fcm is a powerful [Laravel](https://laravel.com/) package to send [Push Notifications](https://firebase.google.com/docs/cloud-messaging) to all devices of one or many users. Being channel-based you only need to specify the `channel` in your Laravel [Notification](https://laravel.com/docs/master/notifications).

## Features

- Easy integration
- Compatible with any version of Laravel
- Send notifications to all devices of one or many users at the same time
- Send millions of notifications in batch
- Fully customizable and adaptable
- Queue support

## 📄 Content

- [Installation](#-installation)
- [Create Notification](#-create-notification)
- [Routes](#-routes)
- [Customizing The Notification](#-customizing-the-notification)
- [Customizing The Controller](#-customizing-the-controller)

## 💿 Installation

```bash
composer require williamcruzme/laravel-fcm
```

### 1. Configure the enviroment

Get the Service Account and paste in your `.env` file:
<br>
*(gear-next-to-project-name) > Project Settings > Cloud Messaging*

```bash
FIREBASE_CREDENTIALS=/path/to/service-account.json
```

### 2. Adding traits

In your `App\Models\User` model add the `HasDevices` trait:

```php
<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Williamcruzme\Fcm\HasDevices;

class User extends Authenticatable
{
    use Notifiable, HasDevices;
}
```

> Remember, you are not limited to only including the trait on your `App\Models\User` model.

### 3. Running migrations

```bash
php artisan migrate
```

**(Optional)** Sometimes you may need to customize the migrations. Using the `vendor:publish` command you can export the migrations:

```bash
php artisan vendor:publish --tag=migrations
```

### 4. Adding routes
In your `routes/api.php` add the routes using the `Device` facade, this is for manage the devices:

```php
Route::middleware('auth')->group(function () {
    Device::routes();
});
```

## ⚡ Create notification

### 1. Creating notifications

```bash
php artisan make:notification InvoicePaid
```

### 2. Adding delivery channels

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
```

### 3. Formatting notifications

The `notification` method support the [Firebase payload](https://firebase.google.com/docs/cloud-messaging/http-server-ref#notification-payload-support):

```php
/**
 * Get the Firebase Message representation of the notification.
 *
 * @param  mixed  $notifiable
 * @return \Williamcruzme\Fcm\Messages\FcmMessage
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

### 4. Sending notifications

`FcmMessage` automatically gets all devices of the notifiable entities; you just need to send notifications. Notifications may be sent in two ways: using the `notify` method of the `Notifiable` trait or using the `Notification` facade. First, let's explore using the trait:

#### Using The Notifiable Trait

This trait is utilized by the default `App\User` model and contains one method that may be used to send notifications: `notify`. The `notify` method expects to receive a notification instance:

```php
use App\Notifications\InvoicePaid;

$user->notify(new InvoicePaid($invoice));
```

> Remember, you may use the `Illuminate\Notifications\Notifiable` trait on any of your models. You are not limited to only including it on your `App\User` model.

#### Using The Notification Facade

Alternatively, you may send notifications via the `Notification` facade. This is useful primarily when you need to send a notification to multiple notifiable entities such as a collection of users. To send notifications using the facade, pass all of the notifiable entities and the notification instance to the `send` method:

```php
use App\Notifications\InvoicePaid;
use Illuminate\Support\Facades\Notification;

Notification::send($users, new InvoicePaid($invoice));
```

## 🌐 Routes

These routes are generated automatically, once wherever you add `Device::routes();`

### Add device

| Method |    URI     |
| ------ | ---------- |
| POST   | `/devices` |

#### Body Params

```json
{
    "token": "fxssWy2Lgtk:APA91bFXy79AmofgTnBm5CfBpyeEFJsSHq0Xcdk..."
}
```

### Remove device

| Method |    URI     |
| ------ | ---------- |
| DELETE | `/devices` |

#### Body Params

```json
{
    "token": "fxssWy2Lgtk:APA91bFXy79AmofgTnBm5CfBpyeEFJsSHq0Xcdk..."
}
```

## 🎨 Customizing The Notification

### Sending data

Using the `data` method you can specify the custom key-value pairs of the notification payload:

```php
/**
 * Get the Firebase Message representation of the notification.
 *
 * @param  mixed  $notifiable
 * @return \Williamcruzme\Fcm\Messages\FcmMessage
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
 * @return \Williamcruzme\Fcm\Messages\FcmMessage
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
 * @return \Williamcruzme\Fcm\Messages\FcmMessage
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

### Custom payload

Using the `payload` method you can specify a custom payload to the notification:

```php
/**
 * Get the Firebase Message representation of the notification.
 *
 * @param  mixed  $notifiable
 * @return \Williamcruzme\Fcm\Messages\FcmMessage
 */
public function toFcm($notifiable)
{
    return (new FcmMessage)
                ->notification([
                    'title' => 'Happy Code!',
                    'body' => 'This is a test',
                ])
                ->payload([
                    'android_channel_id' => '500'
                ]);
}
```

## 🎨 Customizing The Controller

First of all, create your own `DeviceController` controller and add the `ManageDevices` trait.

Second, modify the namespace of the `Device` facade routes to :

```php
Device::routes('App\Http\Controllers');
```

### Custom request validations

The `createRules` `deleteRules` `validationErrorMessages` methods in the `DeviceController` allows you override the default request validations:

```php
<?php

namespace App\Http\Controllers;

use Williamcruzme\Fcm\Traits\ManageDevices;

class DeviceController extends Controller
{
    use ManageDevices;
    
    /**
     * Get the validation rules that apply to the create a device.
     *
     * @return array
     */
    protected function createRules()
    {
        return [
            'token' => ['required', 'string'],
        ];
    }

    /**
     * Get the validation rules that apply to the delete a device.
     *
     * @return array
     */
    protected function deleteRules()
    {
        return [
            'token' => ['required', 'string', 'exists:devices,token'],
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

### Custom response

The `sendResponse` and `sendDestroyResponse` method in the `DeviceController` allows you override the default response:

```php
<?php

namespace App\Http\Controllers;

use Williamcruzme\Fcm\Traits\ManageDevices;

class DeviceController extends Controller
{
    use ManageDevices;
    
    /**
     * Get the response for a successful storing device.
     *
     * @param  Williamcruzme\Fcm\Device  $model
     * @return \Illuminate\Http\JsonResponse
     */
    protected function sendResponse($model)
    {
        return response()->json($model);
    }

    /**
     * Get the response for a successful deleting device.
     *
     * @param  Williamcruzme\Fcm\Device  $model
     * @return \Illuminate\Http\JsonResponse
     */
    protected function sendDestroyResponse($model)
    {
        return response()->json('', 204);
    }
}
```

### Custom guards

The `guard` method in the `DeviceController` allows you override the default guard:

```php
<?php

namespace App\Http\Controllers;

use Williamcruzme\Fcm\Traits\ManageDevices;

class DeviceController extends Controller
{
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
