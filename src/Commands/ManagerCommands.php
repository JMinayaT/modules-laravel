<?php
namespace JMinayaT\Modules\Commands;

use Illuminate\Support\Str;
use Illuminate\Console\AppNamespaceDetectorTrait;
use Illuminate\Container\Container;
use Illuminate\Filesystem\Filesystem;
use JMinayaT\Modules\Models\Module;

class ManagerCommands
{
    public function qualifyClass($name)
    {
        $name = ltrim($name, '\\/');
        $rootNamespace = $this->rootNamespace();

        if (Str::startsWith($name, $rootNamespace)) {
            return $name;
        }

        $name = str_replace('/', '\\', $name);

        return $this->qualifyClass($this->getDefaultNamespace(trim($rootNamespace, '\\')).'\\'.$name);
    }

    public function rootNamespace()
    {
        return Container::getInstance()->getNamespace();
    }

    public function getDefaultNamespace($rootNamespace)
    {
        return $rootNamespace;
    }

    /**
     * Get the full namespace for a given class, without the class name.
     *
     * @param  string  $name
     * @return string
     */
     public function getNamespace($name)
     {
        return trim(implode('\\', array_slice(explode('\\', $name), 0, -1)), '\\');
    }

    public function buildClass($name, $class,$type)
    {
        $file = new Filesystem();
        $stub = $file->get($this->getStub($type));
        return $this->replaceNamespace($stub, $name)->replaceClass($stub, $class);
    }

    public function getStub($name)
    {
      return __DIR__.'/stubs/'.$name.'.stub';
    }

    public function getFileStub($name)
    {
        $file = new Filesystem();
        return $file->get(__DIR__.'/stubs/'.$name.'.stub');
    }

    /**
     * Replace the namespace for the given stub.
     *
     * @param  string  $stub
     * @param  string  $name
     * @return $this
     */
    protected function replaceNamespace(&$stub, $name)
    {
        $stub = str_replace(['DummyName', 'DummyRootNamespace','DummyModule','DummyModule'],
                [$this->getNamespace($name), $this->rootNamespace(), str_replace($this->getNamespace($name).'\\', '', $name)],
                $stub
              );
        return $this;
    }

    /**
     * Replace the class name for the given stub.
     *
     * @param  string  $stub
     * @param  string  $name
     * @return string
     */
    public function replaceClass($stub, $name)
    {
        $class = str_replace($this->getNamespace($name).'\\', '', $name);
        return str_replace('DummyClass', ucfirst($class), $stub);
    }

    public function replaceModelTable($stub, $name)
    {
        return str_replace('DummyModel', $name, $stub);
    }

    public function registerModuleDB($name_module, $description,$alias)
    {
        $module = new Module;
        $module->name = $name_module;
        $module->alias =$alias;
        $module->description = $description;
        $module->save();
    }

    public function hasModule($name)
    {
        $count = Module::where('name', $name)->count();
        if ( $count == 0 )  {
            return false;
        }
        return true;
    }

}
