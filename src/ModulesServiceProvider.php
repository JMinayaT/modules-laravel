<?php
namespace JMinayaT\Modules;

use Illuminate\Support\ServiceProvider;
use JMinayaT\Modules\Commands\CreateModuleCommand;
use JMinayaT\Modules\Commands\CreateModelCommand;
use JMinayaT\Modules\Commands\CreateControllerCommand;
use JMinayaT\Modules\Commands\CreateMigrationCommand;
use JMinayaT\Modules\Commands\CreateFactoryCommand;
use JMinayaT\Modules\Commands\CreateSeederCommand;
use JMinayaT\Modules\Commands\CreateMiddlewareCommand;
use JMinayaT\Modules\Commands\CreatePolicyCommand;
use JMinayaT\Modules\Commands\CreateRequestCommand;
use JMinayaT\Modules\Commands\CreateTestCommand;
use JMinayaT\Modules\Commands\ModuleActiveCommand;
use JMinayaT\Modules\Commands\ModuleListCommand;
use JMinayaT\Modules\Commands\ModuleMigrateCommand;
use JMinayaT\Modules\Commands\ModuleRollbackCommand;
use JMinayaT\Modules\Commands\ModuleSeedCommand;
use JMinayaT\Modules\Commands\ModuleDeleteCommand;
use JMinayaT\Modules\Commands\PublishModuleCommand;
use JMinayaT\Modules\Commands\ModuleInstallCommand;

class ModulesServiceProvider extends ServiceProvider
{

    /**
     * name module.
     *
     * @var string
     */
    protected $name;

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

    /**
     * Create modules directory if it does not exist
     *
     * @return void
     */
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
    /**
     * Publishes commands module.
     *
     * @return void
     */
    protected function publishesCommands()
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                CreateModuleCommand::class,
                CreateModelCommand::class,
                CreateControllerCommand::class,
                CreateMigrationCommand::class,
                CreateFactoryCommand::class,
                CreateSeederCommand::class,
                CreateMiddlewareCommand::class,
                CreatePolicyCommand::class,
                CreateRequestCommand::class,
                CreateTestCommand::class,
                ModuleActiveCommand::class,
                ModuleListCommand::class,
                ModuleMigrateCommand::class,
                ModuleRollbackCommand::class,
                ModuleSeedCommand::class,
                ModuleDeleteCommand::class,
                PublishModuleCommand::class,
                ModuleInstallCommand::class,
            ]);
        }
    }

   
}
