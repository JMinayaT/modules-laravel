<?php
namespace JMinayaT\Modules\Contracts;

interface RepositoryInterface
{
    /**
     * Get all modules.
     *
     * @return mixed
     */
    public function all();

    /**
     * Get list of enabled modules.
     *
     * @return mixed
     */

    public function allEnabled();
    /**
     * Get list of disabled modules.
     *
     * @return mixed
     */
    public function allDisabled();

    /**
     * get a specific module.
     *
     * @param $name
     *
     * @return mixed
     */
    public function get($name);


    
}