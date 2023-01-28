# signalads-PHP

[![Latest Stable Version](https://poser.pugx.org/signalads-co/php/v/stable.svg)](https://packagist.org/packages/signalads-co/php)
[![Total Downloads](https://poser.pugx.org/signalads-co/php/downloads.svg)](https://packagist.org/packages/signalads-co/php)

# <a href="https://document.signalads.com">SignalAds RESTful API Document</a>

If you need to future information about API document Please visit RESTful Document

## Installation

<p>
After that you just need to pick API-KEY up from <a href="https://panel.signalads.com/Client/setting/index">My Account</a> section.
</p>
<hr>

Use in these ways :

```php
composer require signalads-co/php
```

or add

```php
"signalads/php": "*"
```

And run following command to download extension using **composer**

```php
$ composer update
```

Usage
-----

- Required Autoload

```php
require __DIR__ . '/vendor/autoload.php';
```

- Exception Handler

```php
try{
	// call SignalAdsApi function
}
catch(\SignalAds\Exceptions\ApiException $e){
	// در صورتی که خروجی وب سرویس 200 نباشد این خطا رخ می دهد
	echo $e->errorMessage();
}
catch(\SignalAds\Exceptions\HttpException $e){
	// در زمانی که مشکلی در برقرای ارتباط با وب سرویس وجود داشته باشد این خطا رخ می دهد
	echo $e->errorMessage();
}
```

- Send Single SMS

```php
$api = new \SignalAds\SignalAdsApi( "API Key" );
$sender = "10004346";
$message = "خدمات پیام کوتاه سیگنال";
$receptor = "09123456789";
$api->Send($sender,$receptor,$message);
```

`Sample Output`

```json
{
  "data": {
    "message_id": "28561b88-8403-45b8-a114-508abdb9c436",
    "price": 120
  },
  "message": "پیام شما با موفقیت در صف ارسال قرار گرفت",
  "error": {
    "message": null,
    "errors": null
  }
}
```

- Send Multiple SMS With Same Text

```php
$api = new \SignalAds\SignalAdsApi( "API Key" );
$sender = "10004346";
$message = "خدمات پیام کوتاه سیگنال";
$receptors = array("09123456789","09367891011");
$api->SendGroup($sender,$receptors,$message);
```

`Sample Output`

```json
{
  "data": {
    "message_id": "55800454-fe52-44b3-9c44-43c87d6f29b2",
    "price": 240
  },
  "message": "پیام شما با موفقیت در صف ارسال قرار گرفت",
  "error": {
    "message": null,
    "errors": null
  }
}
```

- Send Sms With Pattern

```php
$api = new \SignalAds\SignalAdsApi( "API Key" );
$sender = "10004346";
$pattern_id = "10004346";
$pattern_params = ["param 1", "param 2"];
$receptors = array("09123456789","09367891011");
$api->SendPattern($sender,$pattern_id,$pattern_params,$receptors);
```

`Sample Output`

```json
{
  "data": {
    "message_id": "28561b88-8403-45b8-a114-508abdb9c436"
  },
  "message": "پیام شما با موفقیت در صف ارسال قرار گرفت",
  "error": {
    "message": null,
    "errors": null
  }
}
```

- Get Message Status

```php
$api = new \SignalAds\SignalAdsApi( "API Key" );
$messageid=123;
$api->Status($messageid);
```
- Get Message Status With Filter
```php
$api = new \SignalAds\SignalAdsApi( "API Key" );
$messageid=123;
$limit=10;
$offset=0;
$status=1;
$receptor="09xxxxxxxxx";
$api->Status($messageid, $limit, $offset, $status, $receptor);
```
`Statuses`
```json
PENDING = 1
SENDING = 2
BLACKLIST = 3
DELIVERED = 4
NOT_DELIVERED = 5
NOT_SENDING = 6
ERROR = 7
```

`Sample Output`

```json
{
  "data": {
    "items": [
      {
        "number": "09xxxxxxxxx",
        "status": 1
      },
      {
        "number": "09xxxxxxxxx",
        "status": 2
      },
      {
        "number": "09xxxxxxxxx",
        "status": 3
      },
      {
        "number": "09xxxxxxxxx",
        "status": 4
      },
      {
        "number": "09xxxxxxxxx",
        "status": 5
      },
      {
        "number": "09xxxxxxxxx",
        "status": 6
      },
      {
        "number": "09xxxxxxxxx",
        "status": 7
      }
    ],
    "count": 7,
    "sum": 0
  },
  "message": null,
  "error": {
    "message": null,
    "errors": null
  }
}
```
- Get account credit

```php
$api = new \SignalAds\SignalAdsApi( "API Key" );
$api->GetCredit();
```

`Sample Output`

```json
{
  "data": {
    "credit": 12345
  },
  "message": null,
  "error": {
    "message": null,
    "errors": null
  }
}
```

- Get Package Price

```php
$api = new \SignalAds\SignalAdsApi( "API Key" );
$api->GetPackagePrice();
```

`Sample Output`

```json
{
  "data": {
    "id": 415,
    "gift_charge": "123",
    "price": "123",
    "name": "testsdf",
    "package_prices": [
      {
        "id": 156,
        "english_price": "210",
        "persian_price": "180",
        "operator": {
          "id": 1,
          "title": "همراه اول"
        }
      }
    ]
  },
  "message": null,
  "error": {
    "message": null,
    "errors": null
  }
}
```

