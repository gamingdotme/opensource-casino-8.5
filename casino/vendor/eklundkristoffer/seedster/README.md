[![Latest Stable Version](https://poser.pugx.org/eklundkristoffer/seedster/v/stable.svg)](https://packagist.org/packages/eklundkristoffer/seedster)
[![Total Downloads](https://poser.pugx.org/eklundkristoffer/seedster/d/total.svg)](https://packagist.org/packages/eklundkristoffer/seedster)
[![License: MIT](https://img.shields.io/badge/License-MIT-yellow.svg)](https://opensource.org/licenses/MIT)

# seedster
Register laravel seeds from a package

# Installation
require the project using Composer:
```
composer require eklundkristoffer/seedster
```
or manually update your require block and run `composer update`
```
{
    "require": {
        "eklundkristoffer/seedster": "^3.4"
    }
}
```

# Usage
You can now register a seed from any service provider
```php
use Mypackage\Database\Seeds\PostsTableSeeder;
use Mypackage\Database\Seeds\UsersTableSeeder;

public function register()
{
    $this->app['seed.handler']->register(
        UsersTableSeeder::class
    );

    // Or register an array of seeds
    $this->app['seed.handler']->register(
        [UsersTableSeeder::class, PostsTableSeeder::class, ...]
    );
}
```
