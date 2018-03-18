<?php

namespace JMinayaT\Modules\Tests;

use JMinayaT\Modules\ModulesServiceProvider;
use JMinayaT\Modules\Facades\Module as ModuleFacade;
use Orchestra\Testbench\TestCase as OrchestraTestCase;

class TestCase extends OrchestraTestCase
{

    public function setUp()
    {
        parent::setUp();
        $this->artisan('migrate');
    }
    /**
     * Load package service provider
     * @param  \Illuminate\Foundation\Application $app
     * @return lasselehtinen\MyPackage\ModulesServiceProvider
     */
    protected function getPackageProviders($app)
    {
        return [ModulesServiceProvider::class];
    }

    /**
     * Load package alias
     * @param  \Illuminate\Foundation\Application $app
     * @return array
     */
    protected function getPackageAliases($app)
    {
        return [
            'Module' => ModuleFacade::class,
        ];
    }

     /**
     * Set up the environment.
     *
     * @param \Illuminate\Foundation\Application $app
     */
    protected function getEnvironmentSetUp($app)
    {
        $app['config']->set('database.default', 'sqlite');
        $app['config']->set('database.connections.sqlite', array(
            'driver' => 'sqlite',
            'database' => ':memory:',
            'prefix' => '',
        ));
        $app['config']->set('modules', [
            'table_name'  =>  'modules',
            'cache' => [
                'enabled' => true,
                'key' => 'app-modules',
            ],
        ]);
    }
}