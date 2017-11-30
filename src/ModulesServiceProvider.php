<?php
namespace JMinayaT\Modules;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;
use JMinayaT\Modules\Models\Module;
use JMinayaT\Modules\Commands\CreateModule;
use JMinayaT\Modules\Commands\CreateController;
use JMinayaT\Modules\Commands\CreateModel;
use JMinayaT\Modules\Commands\CreateMigration;
use JMinayaT\Modules\Commands\ModuleList;
use JMinayaT\Modules\Commands\ModuleActive;
use JMinayaT\Modules\Commands\ModuleDelete;
use JMinayaT\Modules\Commands\ModuleUp;
use JMinayaT\Modules\Commands\PublishModule;
use JMinayaT\Modules\Commands\ModuleInstall;
use JMinayaT\Modules\Commands\ModuleMigrate;
use JMinayaT\Modules\Commands\ModuleRollback;

class ModulesServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->createModulesDirectory();
        $this->publishesConfig();
        $this->publishesMigrations();
        $this->publishesCommands();
        $module = new Module();

          if (Schema::hasTable($module->getTable()) ) {
              $modules = Module::all();

              foreach ($modules as $module) {

                  if($module->active){
                      $path = base_path('modules/' . $module->name . '/');

                      if (is_dir($path)) {
                          $this->webRoutes($module->name);
                          $this->apiRoutes($module->name);

                          if (is_dir(base_path('modules/' . $module->name . '/Resources/Views/'))) {
                              $this->loadViews($module->name);
                          }

                          if (is_dir(base_path('modules/' . $module->name . '/Resources/Lang/'))) {
                              $this->loadTranslations($module->name);
                          }

                          if (is_dir(base_path('modules/' . $module->name . '/Database/migrations/'))) {
                              $this->loadMigrations($module->name);
                          }
                      }

                      if (is_dir(base_path('modules/' . $module->name . '/Resources/Assets/js/'))) {
                          $this->JsFileRoute(base_path('modules/' . $module->name . '/Resources/Assets/js/'),$module->name);
                      }

                      if (is_dir(base_path('modules/' . $module->name . '/Resources/Assets/css/'))) {
                          $this->CssFileRoute(base_path('modules/' . $module->name . '/Resources/Assets/css/'),$module->name);
                      }
                  }
              }
          }
      }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('modules', function($app) {
            return new ModuleService();
        });
        $this->mergeConfigFrom(
            __DIR__.'/Config/modules.php',
            'modules'
        );
    }

    protected function createModulesDirectory()
    {
        if ( !(is_dir(base_path('modules/'))) ) {
            \File::makeDirectory(base_path('modules/'),0775, true, true);
        }
    }

    /**
     * publishes Config for application modules.
     *
     * @return void
     */
    protected function publishesConfig()
    {
        $this->publishes([
            __DIR__.'/Config/modules.php' => config_path('modules.php'),
            ], 'config');
    }

    /**
     * publishes Migrations for application modules.
     *
     * @return void
     */
    protected function publishesMigrations()
    {
          $timestamp = date('Y_m_d_His', time());
          $this->publishes(
            [__DIR__.'/Database/migrations/create_modules_table.php' => database_path('migrations/'.$timestamp.'_create_modules_table.php')
            ],'migrations');
    }

    protected function loadMigrations($module)
    {
        $this->loadMigrationsFrom(base_path('modules/'.$module.'/Database/migrations/'));
    }

    protected function publishesCommands()
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                CreateModule::class,
                CreateController::class,
                CreateModel::class,
                CreateMigration::class,
                ModuleList::class,
                ModuleActive::class,
                ModuleDelete::class,
                ModuleUp::class,
                PublishModule::class,
                ModuleInstall::class,
                ModuleMigrate::class,
                ModuleRollback::class
            ]);
        }
    }

    /**
     * Register the Web Routes for application modules.
     *
     * @return void
     */
    protected function webRoutes($module)
    {
        Route::middleware('web')->namespace('Modules\\'.$module.'\Controllers')
                                ->group(base_path('modules/'.$module.'/Routes/web.php'));
    }

    /**
     * Register the Api Routes for application modules.
     *
     * @return void
     */
    protected function apiRoutes($module)
    {
        Route::prefix('api')->middleware('api')->namespace('Modules\\'.$module.'\Controllers')->group(base_path('modules/'.$module.'/Routes/api.php'));
    }

    /**
     * Register the Api Routes for application modules.
     *
     * @return void
     */
    protected function loadViews($module)
    {
        $this->loadViewsFrom(base_path('modules/'.$module.'/Resources/Views'),$module);
    }

    /**
     * Register the Translations for application modules.
     *
     * @return void
     */
    protected function loadTranslations($module)
    {
        $this->loadTranslationsFrom(base_path('modules/'.$module.'/Resources/Lang'),$module);
    }

    protected $routeJS = '';
    protected function JsFileRoute($rute,$module)
    {
          $this->routeJS = $rute;
          \Route::get('modules/'.$module.'/{filename}', function ($filename){
              $path =  $this->routeJS . $filename;
              $file = \File::get($path);
              $type = \File::mimeType($path);
              $response = \Response::make($file, 200);
              $response->header("Content-Type", $type);
              return $response;
          });
    }
    protected $routeCSS = '';
    protected function CssFileRoute($rute,$module)
    {
        $this->routeCSS = $rute;
        \Route::get('modules/'.$module.'/{filename}', function ($filename){
            $path =  $this->routeCSS . $filename;
            $file = \File::get($path);
            $type = \File::mimeType($path);
            $response = \Response::make($file, 200);
            $response->header("Content-Type", $type);
            return $response;
        });
    }

}
