<?php

namespace JMinayaT\Modules;

use Illuminate\Support\ServiceProvider;
use JMinayaT\Modules\Util\ModuleData;


class BootModuleServiceProvider extends ServiceProvider
{

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
       
    }

    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $md = new ModuleData;
        if($md->hasTable()) { 
            foreach ($md->getModulesBootNameSpace() as $key => $value) {
                $class = $value.'\Providers\\'.$key.'ServiceProvider';
                if(class_exists($class)){
                    
                    $this->app->register($class);
                }
            }
        }
    }
}