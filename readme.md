# Laravel DataMapper

[![Latest Version on Packagist][ico-version]][link-packagist]
[![Total Downloads][ico-downloads]][link-downloads]

* [Installation](#installation)
* [Usage](#usage)
* [Configuration](#configuration)
* [Options](#options)
* [Testing](#testing)
* [Troubleshoot](#troubleshoot)
* [Contributing](#contributing)
* [License](#license)

The laravel-datamapper provides a middleware that is able to convert an entity kind passed by request or url path to its respective model object . Take a look at [contributing.md](contributing.md) to see a to do list.

## Installation

Via Composer

``` bash
$ composer require agilize/laravel_datamapper
```

## Usage
To allow Laravel Datamapper for all your routes, add the `DataMappingMiddleware` middleware at `$middleware` or `$routeMiddleware` property of  `app/Http/Kernel.php` class:

```php
protected $middleware = [
  \Agilize\LaravelDataMapper\DataMappingMiddleware::class,
    // ...
];
```

or 

```php
protected $routeMiddleware = [
  'datamapping' => \Agilize\LaravelDataMapper\DataMappingMiddleware::class,
    // ...
];
```

and add it in your routes:

```php
Route::get('/user/{id}', function (Request $request) {
    // ...
})->middleware('datamapping');
```

The middleware will search for a matching Model referencing the key passed on path, query string or parameter bag.

Eg.: http://yourdomain.com/api/v1/user/1 will search for User.php (instance of Eloquent ORM Model) that exists on your database with id `1`. See [Configuration](#configuration) for more details.

It also supports hyphen and underscore separated entities. Eg.: http://yourdomain.com/api/v1/user-role/1

### Relationships 
By default, the middleware is set to bring all existent relationships from Model. However, you need to create a scope in your model:
```php
public function scopeWithAll($query)
{
    $query->with('relation1', 'relation2');
}
```

If you don't want do bring any relationships from your Model, you can add a middleware parameter in your route to disable it:
```php
Route::get('/user/{id}', function (Request $request) {
    // ...
})->middleware('datamapping:no-relations');
```

## Configuration

The defaults are set in `config/datamapping.php`. Publish the config to copy the file to your own config:
```sh
php artisan vendor:publish --tag="datamapping"
```

### Options

| Option                   | Description                                                                          | Default value |
|--------------------------|--------------------------------------------------------------------------------------|---------------|
| entity_directory         | Default model directory of your project, eg. `'Packages'`.                           | `string`      |
| primary_key_type         | Default primary key type of your database tables, eg. `'integer'` or `'uuid'`.       | `string`      |
| api_version              | Main version of your REST API, eg. `'v1'`.                                             | `string`      |

## Testing

``` bash
$ composer test
```

## Troubleshoot

### Invalid key value type on Configuration file.

    RuntimeException: DataMapping config `some_key` should be a string.
    
### Invalid directory on Configuration file.
    
    RecursiveDirectoryIterator::__construct(...) error.
    
Any other not supported or invalid configuration may not cause an Exception, but will not attempt to exactly match the Model.

## Contributing

Please see [contributing.md](contributing.md) for details and a todolist.

## License

Released under the MIT License. Please see the [license](license.md) for more information.

[ico-version]: https://img.shields.io/packagist/v/agilize/laravel_datamapper.svg?style=flat-square
[ico-downloads]: https://img.shields.io/packagist/dt/agilize/laravel_datamapper.svg?style=flat-square

[link-packagist]: https://packagist.org/packages/agilize/laravel_datamapper
[link-downloads]: https://packagist.org/packages/agilize/laravel_datamapper
[link-author]: https://github.com/agilize
