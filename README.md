# Giata Hotels API

[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE.md)
[![Travis](https://img.shields.io/travis/amirhamdy/giata-hotels-api.svg?style=flat-square)]()
[![Total Downloads](https://img.shields.io/packagist/dt/amirhamdy/giata-hotels-api.svg?style=flat-square)](https://packagist.org/packages/amirhamdy/giata-hotels-api)

## Install
`composer require amirhamdy/giata-hotels-api`

For versions < 5.5 of Laravel, add the service provider of the package and alias the package.

Open your `config/app.php` file.

Add a new line to the `providers` array:

	GiataHotels\GiataHotelsServiceProvider::class

And add a new line to the `aliases` array:
    
    'GiataAPI' => GiataHotels\GiataHotelsFacade::class
	
Now you're ready to start using the GiataAPI in your application.

## Quick start

```php
use GiataAPI;

$response = GiataAPI::getHotelsByCountry('EG', true);
```
```js
// $response sample:

{
  "property": [{
      "giataId": "3",
      "lastUpdate": "2020-04-26T03:18:15+02:00",
      "href": "https://multicodes.giatamedia.com/webservice/rest/1.latest/properties/3"
    },
    {
      "giataId": "4",
      "lastUpdate": "2020-04-26T03:18:15+02:00",
      "href": "https://multicodes.giatamedia.com/webservice/rest/1.latest/properties/4"
    },
    ...
    ...
  ],
  "country": "EG",
  "lastUpdate": "2020-04-30"
}
```

## Usage
This package gives you the following methods to use:
* [Get All Hotels By Country Code](#giataapigethotelsbycountry)
* [Get A Hotel By Giata ID](#giataapigethotelbygiataid)
* [Get Hotel's Images By Giata ID](#giataapigetimagesbygiataid)
* [Get Hotel's Translations By Giata ID](#giataapigettextsbygiataid)

#### GiataAPI::getHotelsByCountry()
Getting all hotels in a country, you can just use the `getHotelsByCountry()` method.

- In its most basic form you can specify the countryCode.

```php
GiataAPI::getHotelsByCountry('EG');
```

- As an optional second parameter you can pass it, the multi `boolean` option so you can get all information for each hotel in one request.

```php
GiataAPI::getHotelsByCountry('EG', true);
```

#### GiataAPI::getHotelByGiataId()
- Getting hotel's information using its Giata ID.

```php
GiataAPI::getHotelByGiataId(3);
```

#### GiataAPI::getImagesByGiataId()
- Getting hotel's images using its Giata ID.

```php
GiataAPI::getImagesByGiataId(3);
```

#### GiataAPI::getTextsByGiataId()
- Getting hotel's translations in a specific language `default='ar` using its Giata ID.

```php
GiataAPI::getTextsByGiataId(3);
```

- As a second parameter you can pass the language.

```php
GiataAPI::getTextsByGiataId(3, 'fr');
```

## Other packages you may be interested in
- [giata-hotels-commands](https://github.com/amirhamdy/giata-hotels-commands)

## Contributing
Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Security
If you discover any security-related issues, please email amirhamdy4@gmail.com instead of using the issue tracker.

## License
The MIT License (MIT). Please see [License File](/LICENSE.md) for more information.
