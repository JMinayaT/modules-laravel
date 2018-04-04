<?php

namespace JMinayaT\Modules\Util;

use Illuminate\Support\Facades\Cache;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\Schema;
use JMinayaT\Modules\Models\Module;

class ModuleData
{
    public function cacheForget(){
        Cache::forget(config('modules.cache.key'));
    }
    
    /**
     * Get the config chache enable.
     *
     * @return bool
     */
    public function isCaching()
    {
        return config('modules.cache.enabled');
    }

    /**
     * Get cached modules.
     *
     * @return array
     */
    public function getModuleCached()
    {
        return Cache::rememberForever(config('modules.cache.key'), function() {
            return $this->getModulesArray();
        });
    }

    /**
     * Get DB modules.
     *
     * @return array
     */
    public function getModuleDB()
    {
        return $this->getModulesArray();
    }

    public function hasTable() {
        if( ! Schema::hasTable(config('modules.table_name'))){
            return false;
        }
        return true;
    }
    protected function getModulesArray()
    {
       
        return Module::all()->toArray();
       
    }

    public function getArray()
    {
        if(! $this->isCaching()) {
            return $this->getModuleDB();
        }
        return $this->getModuleCached();
    }

    public function exists($module)
    {
        return in_array($module , $this->getModuleNameArray($this->getArray()));
    }

    public function delete($name)
    {
        $module = Module::where('name', $name)->first();
        $module->delete();
        $this->cacheForget();
    }

    protected function getModuleNameArray($array)
    {
        $names = [];
        foreach($array as $key => $value ){
            $names[] = $value['name'];
        }
        return $names;
    }
    public function getModulesActive()
    {
        $actives = [];
        foreach($this->getArray() as $key => $value ){
            if($value['active']) {
                $actives[] = $value;
            }
            
        }
        return $actives;
    }

    public function getModulesDisable()
    {
        $disables = [];
        foreach($this->getArray() as $key => $value ){
            if(! $value['active']) {
                $disables[] = $value;
            }
            
        }
        return $disables;
    }
    public function active($name, $value)
    {
        $module = Module::where('name', $name)->first();
        $module->active = $value;
        $module->save();
        $this->cacheForget();
    }

    public function getPath($name) {
        return base_path('modules/'.$name.'/');
    }
    protected function getJson($name)
    {
        $files = new Filesystem;
        $json = $files->get($this->getPath($name).'module.json');
        return json_decode($json, true);
    }

    public function version($name)
    {
        return $this->getJson($name)['version'];
    }

    public function registerModuleDB($name_module, $description, $alias)
    {
        $this->cacheForget();
        $module = new Module;
        $module->name = $name_module;
        $module->alias =$alias;
        $module->description = $description;
        $module->save();
    }

    public function updateModuleDB($name_module, $description, $alias)
    {
        $this->cacheForget();
        $module = Module::where('name', $name_module)->first();
        $module->name = $name_module;
        $module->alias =$alias;
        $module->description = $description;
        $module->save();
    }

    public function getModulesNameSpace()
    {
        $namespace = [];
        foreach($this->getModuleNameArray($this->getArray()) as $key => $value ){
            $namespace[$value] = 'Modules\\'.$value;
        }
        return $namespace;
    }
    public function getModulesBootNameSpace()
    {
        $namespace = [];
        foreach($this->getArray() as $key => $value ){
            if($value['active']) {
                $namespace[$value['name']] = 'Modules\\'.$value['name'];
            }
        }
        return $namespace;
    }

}