# JMinayaT Laravel - Modules
`jminayat/modules-laravel` is a package for the administration of your laravel application in modules. compatible with Laravel version 5.5 .
* [Installation](#installation)
* [Usage](#usage)
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

**Creating A Module**

To create a new module, simply run the following command:
``` bash
php artisan module:create <module-name>
```
- `<module-name>` - Replace with the name of the desired module.
- `module description` - Write the description of the module created.

To automatically add controller, model and migration when creating a new module use: `-c -d -m`
``` bash
php artisan module:create <module-name> -c -d -m
```
- `-c` or `--controller` - Create controller.
- `-d` or `--model` - Create model.
- `-m` or `--migration` - Create migration.

**Folder Structure**
```
modules/
  ├── Blog/
      ├── Controllers/
      ├── Database/
          ├── migrations/
      ├── Models/
      ├── Resources/
          ├── Assets/
          ├── lang/
          ├── views/
      ├── Route/
          ├── web.php
          ├── api.php
      ├── module.json

```

## Artisan Commands

****Note that the command names use "test" as the name of the example module****

**module:active**

activate | disable module, use true or false.
```
php artisan module:active test true
```

**module:create**

create a new module.
```
php artisan module:create test
```
options

- `-c` or `--controller` - Create controller.
- `-d` or `--model` - Create model.
- `-m` or `--migration` - Create migration.


**module:delete**

delete module.
```
php artisan module:delete test
```

**module:install**

Install module from zip file.
```
php artisan module:install var/this-path/test.zip
```

**module:list**

show list of all modules.
```
php artisan module:list
```


**module:make-controller**

Create a new module controller.
```
php artisan module:make-controller test TestController
```

**module:make-model**

Create a new module model.
```
php artisan module:make-model test TestModel
```

options
- `-m` or `--migration` - Create migration.

**module:make-migration**

Create a new module migration.
```
php artisan module:make-migration test create_tests_table
```

**module:publish**

Publish module zip file.
```
php artisan module:publish test
```

**module:up**

Up config file module json.
```
php artisan module:up test
```

**module:migrate**

Migrate database for all modules.
```
php artisan module:migrate
```
For migrate a specific module to use:
```
php artisan module:migrate test
```
**module:rollback**

Rollback the last module database migration.
```
php artisan module:rollback
```
For rollback a specific module to use:
```
php artisan module:rollback test
```

## Facade Methods

## Module Methods

## Publish Module


## License
The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
