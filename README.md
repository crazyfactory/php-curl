
# Curl

This wrapper around php's curl functions. PHP 5.3 compatible for sad reasons.

## Installation

Via composer 

`composer require crazyfactory/curl`

## Usage

### Simple
Load a page
```php
$responseBody = (new Curl)->get('http://www.example.org');
```

### Sending fields
Pass in GET parameters as an array
```php
$responseBody = (new Curl)->get($url, [
    'foo' => 'bar'
]);
```

Same for POST
```php
$responseBody = (new Curl)->post($url, [
    'foo' => 'bar'
]);
```

For other methods, use `call()` directly.

### Curl options and info
`call()` accept an array of CURLOPT-constants and merges these with the defaults from `getDefaultOptions()`. `post()` and `get()` accept this array as 3rd parameter as well.

The last argument for all of these function is a reference to the returned curl-info for fancier things.

### Exceptions
`call()` will throw `CrazyFactory/Curl/Exception` when any error happens. It will also throw an Exception on 400+ status codes.

```php
try {
    (new Curl)->get('http://httpbin.org/status/404');
}
catch (CrazyFactory/Curl/Exception $e) {
    echo "Whoops we had a {$e->getHttpCode()}!"; 
}
```
