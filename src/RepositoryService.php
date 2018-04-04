<?php
namespace JMinayaT\Modules;

use JMinayaT\Modules\Contracts\RepositoryInterface;
use JMinayaT\Modules\Util\ModuleData;

class RepositoryService implements RepositoryInterface
{
    protected $moduledt;

    public function __construct()
    {
        $this->moduledt = new ModuleData;
    }
    public function all(){
        return $this->moduledt->getArray();
    }

    /**
     * Get list of enabled modules.
     *
     * @return mixed
     */

    public function allEnabled() 
    {
        return $this->moduledt->getModulesActive();
    }
    /**
     * Get list of disabled modules.
     *
     * @return mixed
     */
    public function allDisabled()
    {
        return $this->moduledt->getModulesDisable();
    }

    /**
     * get a specific module.
     *
     * @param $name
     *
     * @return mixed
     */
    public function get($name)
    {
        $module = [];
        foreach($this->moduledt->getArray() as $key => $value ){
            if(strtolower($value['name'])==strtolower($name)){
                $module = $value;
            }
        }
        return $module;
    }

    /**
     * Check the specified module
     *
     * @param $name
     *
     * @return mixed
     */
    public function has($name)
    {
        foreach($this->moduledt->getArray() as $key => $value ){
            if(strtolower($value['name'])==strtolower($name)){
                return true;
            }
        }
        return false;
    }

}