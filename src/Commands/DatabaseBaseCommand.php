<?php

namespace JMinayaT\Modules\Commands;

use Illuminate\Console\Command;
use JMinayaT\Modules\Util\ModuleData;

class DatabaseBaseCommand extends Command
{

    protected $module;

    /**
     * The ModuleData instance.
     *
     * @var \JMinayaT\Modules\Util\ModuleData
     */
    protected $moduledt;

    /**
     * Create a new controller creator command instance.
     *
     * @param  \Illuminate\Filesystem\Filesystem  $files
     * @return void
     */
    public function __construct(ModuleData $moduledt)
    {
        parent::__construct();
        $this->moduledt = $moduledt;
    }

    /**
     * Get all of the migration paths.
     *
     * @return array
     */
    protected function getMigrationPaths()
    {
        // Here, we will check to see if a path option has been defined. If it has we will
        // use the path relative to the root of the installation folder so our database
        // migrations may be run for any customized path from within the application.
        if ($this->input->hasOption('path') && $this->option('path')) {
            return collect($this->option('path'))->map(function ($path) {
                return ! $this->usingRealPath()
                                ? $this->getBasePath().'/'.$path
                                : $path;
            })->all();
        }

        return array_merge(
            [$this->getMigrationPath()], $this->migrator->paths()
        );
    }

    /**
     * Determine if the given path(s) are pre-resolved "real" paths.
     *
     * @return bool
     */
    protected function usingRealPath()
    {
        return $this->input->hasOption('realpath') && $this->option('realpath');
    }

    /**
     * Get the path to the migration directory.
     *
     * @return string
     */
    protected function getMigrationPath()
    {
        return $this->getDatabaseBasePath().DIRECTORY_SEPARATOR.'Migrations';
    }

    /**
     * Get the module base path.
     *
     * @param  string  $name
     * @return string
     */
    protected function getBasePath()
    {
        return base_path('modules/'.$this->module);
    }

    /**
     * Get the module base path.
     *
     * @param  string  $name
     * @return string
     */
    protected function getDatabaseBasePath()
    {
        return base_path('modules/'.$this->module.'/database');
    }

    /**
     * Determine if the class already exists.
     *
     * @param  string  $module
     * @return bool
     */
    protected function moduleExists($module)
    {
        if(! $this->moduledt->isCaching()) {
           return in_array($module ,$this->getModuleNameArray($this->moduledt->getModuleDB()));
        }
        return in_array($module , $this->getModuleNameArray($this->moduledt->getModuleCached()));
    }

    protected function getModuleNameArray($array)
    {
        $names = [];
        foreach($array as $key => $value ){
            $names[] = $value['name'];
        }
        return $names;
    }
}
