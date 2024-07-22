# Laravel Nova Zadarma VOIP Integration

## Description

This package provides integration between Laravel Nova and the Zadarma VOIP service. It allows you to make, receive and manage phone calls directly from your Nova interface!


## Installation

### Step 1: Install the package

Run the following command to install the package:

```sh
composer require webard/nova-zadarma
```

### Step 2: Publish the configuration

Publish the package configuration using the following command:

```sh
php artisan vendor:publish --provider="Webard\NovaZadarma\NovaZadarmaServiceProvider" --tag=config
```

### Step 3: Provide API keys from Zadarma

Add this lines to .env file:

```sh
ZADARMA_KEY=
ZADARMA_SECRET=
ZADARMA_SIP_LOGIN=
```

Zadarma Secret and Key you can find in Settings -> Integrations and API -> Keys and API:

![Zadarma API Keys](screenshots/screenshot_1.png)

Zadarma SIP Login is the suffix of PBX number, which can be found under My PBX -> Extensions.

Your SIP Login is behind the painted field.

![Zadarma SIP Login](screenshots/screenshot_2.png)

### Step 3: Publish the migrations

Publish the package migrations using the following command:

```sh
php artisan vendor:publish --provider="Webard\NovaZadarma\NovaZadarmaServiceProvider" --tag=migrations
```

### Step 4: Register tool in `NovaServiceProvider`

```php
use Webard\NovaZadarma\NovaZadarmaTool;

public function tools()
{
    return [
        ...
        NovaZadarmaTool::make(),
    ];
}
```

### Step 5: Update the User model

Add the `HasPhoneCalls` trait to the User model:

```php
use Webard\NovaZadarma\Traits\HasPhoneCalls;

class User extends Authenticatable {
    use HasPhoneCalls;
}
```

### Step 6: Add the phone calls field to the User resource

```php
use Webard\NovaZadarma\Nova\Fields\UserPhoneCalls;

class User extends Resource {
    public function fields(NovaRequest $request)
    {
        return [
            ...
            UserPhoneCalls::make(),
        ];
    }
}
```

### Step 7: Add the phone call action to the User resource

Add the `MakePhoneCall` action to the User resource:

```php
use Webard\NovaZadarma\Nova\Actions\MakePhoneCall;

class User extends Resource {
    public function actions(NovaRequest $request)
    {
        return [
            ...
            MakePhoneCall::make()
                ->sole()
        ];
    }
}
```

> [!WARNING]
> `MakePhoneCall` action must be `sole`, because User can make call to only one user at time.

> [!TIP]
> You can add `->withoutConfirmation()` method to action to allow making phone calls directly after clicking action.

### Step 8: Fill SIP Number in your User profile of Nova

Go to your User edit form and fill `Zadarma SIP` according to SIP number in Zadarma panel. Default created SIP number is 100:

![Zadarma SIP User](screenshots/screenshot_3.png)

## Webhooks

In Zadarma Integrations Notifications set PBX call webhook url to:

```
https://YOUR-DOMAIN.com/nova-vendor/webard/nova-zadarma/webhook/pbx-call
```

and enable all checkboxes.

Set events webhook url to:

```
https://YOUR-DOMAIN.com/nova-vendor/webard/nova-zadarma/webhook/event
```

and enable all checkboxes.

Add entry to `$except` property in `App\Http\Middleware\VerifyCsrfToken` class:

```
'nova-api/webard/nova-zadarma/webhook/*'
```

If you have `fruitcake/laravel-telescope-toolbar` installed, add this entry too:

```
'nova-api/webard/nova-zadarma/webhook/*'
```

to `ignore_paths` in `config/telescope-toolbar.php`


## TODO

I'm are actively seeking contributions to enhance this package. Here are some features I would love to see implemented:

- [ ] multi-language

## Contributing

We welcome contributions to improve this plugin! Please follow these steps to contribute:

1. Fork the repository.
2. Create a new branch for your feature or bug fix.
3. Make your changes and commit them with descriptive messages.
4. Push your changes to your forked repository.
5. Open a pull request to the main repository.

## License

This project is licensed under the MIT License. See the [LICENSE.md](LICENSE.md) file for more details.

## Contact

For questions or support, please open an issue on GitHub.
