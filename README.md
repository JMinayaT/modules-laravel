# JMinayaT Laravel - Modules
`jminayat/modules-laravel` is a package for the administration of your laravel application in modules. compatible with Laravel version 5.5 .
* [Installation](#installation)
* [Usage](#usage)
* [Avanced Usage](#avanced-usage)
  * [Artisan Commands](#artisan-commands)
  * [Facade methods](#facade-methods)
  * [Module Methods](#module-methods)
  * [Publish Module](#publish-module)


## Installation
Install the package through the composer.

``` bash
composer require javierminayat/modules-laravel
```

You need to load the module folder since it does not load automatically. You can autoload your modules using `psr-4`.

Edit main composer file, and add:
``` json
{
  "autoload": {
    "psr-4": {
      "App\\": "app/",
      "Modules\\": "modules/"
    }
  }
}
```
**Do not forget to execute `composer dump-autoload` .**


You can publish the Migrations.
``` bash
php artisan vendor:publish --provider="JMinayaT\Modules\ModulesServiceProvider" --tag="migrations"
```

After the migration has been published, you can create the table of modules by executing the migrations:
```bash
php artisan migrate
```


You can publish the Config file (it's optional).
``` bash
php artisan vendor:publish --provider="JMinayaT\Modules\ModulesServiceProvider" --tag="migrations"
```
When published, the `config/modules.php ` Config file contains:
```php
<?php

return [

  /*
   * Name of the table to use
   * default value but you may easily change it to any table you like.
   */

   'table_name'  =>  'modules',

];
```
## Usage

## Avanced Usage

### Artisan Commands

### Facade Methods

### Module Methods

### Publish Module


## License
The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
