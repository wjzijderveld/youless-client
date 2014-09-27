# Youless API

[![Build Status](https://travis-ci.org/wjzijderveld/youless-api.svg?branch=master)](https://travis-ci.org/wjzijderveld/youless-api)

The goal of this library is to provide a simple API to a [Youless] webservice.

[Youless]: http://youless.nl

## Installation

```bash
$ composer install
```

## Running the tests

```bash
$ ./vendor/bin/phpspec run
```

## Usage

Instantiate the client with a Buzz\Browser instance and the URL where your youless webservice can be reached.

```php
$browser = new Buzz\Browser();
$client = new Wjzijderveld\Youless\Api\Client($browser, 'http://youless.yrl.here');
```

The client has 5 methods for data retrieval:

```php
// Request current usage
$client->getRecentData();
// Wjzijderveld\Youless\Api\Response\Recent

// Request data per day for a given month
// (1 - 12, 1 = January, 12 = December)
$client->getDataForMonth(1);
// Wjzijderveld\Youless\Api\Response\History

// Request data per hour for a given day in the last week
// (0 - 6, 0 is today, 6 is 6 days ago)
$client->getDataForDay(1);
// Wjzijderveld\Youless\Api\Response\History

// Request data per 10 minutes for the last 8 hours
// (1 = last 8 hours, 2 = 8 till 16 hours ago, 3 = 16 - 32 hours ago)
$client->getDataFor8Hours(1);
// Wjzijderveld\Youless\Api\Response\History

// Request data per minute for the last 30 minutes
// (1 = last 30 minutes, 2 = 30 minutes till 60 minutes ago)
$client->getDataFor30MInutes(1);
// Wjzijderveld\Youless\Api\Response\History
```

The `Recent` object provides data for the current usage and the total usage.

The `History` object provides the values in watt, the delta in seconds between each value and the date of the first measurement for this request.
