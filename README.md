# Laravel VoipNow

[![Latest Stable Version](https://poser.pugx.org/gotrex/laravel-voipnow/v/stable)](https://packagist.org/packages/gotrex/laravel-voipnow)
[![Total Downloads](https://poser.pugx.org/gotrex/laravel-voipnow/downloads)](https://packagist.org/packages/gotrex/laravel-voipnow)
[![License](https://poser.pugx.org/gotrex/laravel-voipnow/license)](https://packagist.org/packages/gotrex/laravel-voipnow)

A Laravel 5 wrapper for the VoipNow SystemAPI.

**Note:** The token credential information is stored in a JSON file for application wide usage. If you prefer multi user usage please refer to the [Multiuser Support](#multiser-support) section of the readme.

## Installation

You can install the package via composer:

```bash
composer require gotrex/laravel-voipnow
```

From the command-line run:

```bash
php artisan vendor:publish --provider="Gotrex\VoipNow\VoipNowServiceProvider"
```

Add the following keys to your .env file.

```env
VOIPNOW_VERSION=
VOIPNOW_DOMAIN=
VOIPNOW_MULTI_USER=false
VOIPNOW_KEY=
VOIPNOW_SECRET=
```

The following key is optional

```env
VOIPNOW_PARENT_IDENTIFIER=
```

## Usage

You can call a VoipNow SystemAPI method directly by using the facace (e.g. `VoipNow::{VOIPNOWFUNCTION}`). For a full reference of all the available functions refer to the [VoipNow SystemAPI documenatation](https://wiki.4psa.com/display/VNUAPI30/VoipNow+SystemAPI).

### Examples

Retrieve a  list of all the service providers

``` php
use VoipNow;

return VoipNow::GetServiceProviders();
```

Retrieve the organization account details

```php
use VoipNow;

return VoipNow::GetOrganizationDetails(['identifier' => 'XXX']);
```

If you do not use the Facade, you can call it with the app() helper.

```php
$voipNow = app('voipnow');

return $voipNow->GetOrganizationDetails(['identifier' => 'XXX']);
```

### Multiuser support

Out of the box this package stores the token information in a JSON file. This means every request uses the same token credentials. If you would like to give your users the opportunity to make individual requests you can do so by setting the following parameter inside your .env file:

```env
VOIPNOW_MULTI_USER=true
```

Currently the package does not provide a migration. Add the following definition to the user migration - or create the fields directly in the database table.

```php
$table->text('voipnow_access_token')->nullable();
$table->integer('voipnow_expires_in')->nullable();
$table->timestamp('voipnow_expired_at')->nullable();
```

Add the fields to the $fillable property of the User model.

```php
protected $fillable = [
    'name', 'email', 'password', 'voipnow_access_token', 'voipnow_expires_in', 'voipnow_expired_at'
];
```

## Testing

``` bash
composer test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

### Security

If you discover any security related issues, please email development@go-trex.com instead of using the issue tracker.

## Credits

- [Ortwin van Vessem](https://github.com/ovvessem)

## Support

[Please open an issue in github](https://github.com/gotrex/laravel-voipnow/issues)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
