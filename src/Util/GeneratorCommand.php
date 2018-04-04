<?php

namespace JMinayaT\Modules\Util;

use Illuminate\Support\Str;
use Illuminate\Filesystem\Filesystem;
use Symfony\Component\Console\Input\InputArgument;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use JMinayaT\Modules\Models\Module;

abstract class GeneratorCommand extends Command
{
    /**
     * The filesystem instance.
     *
     * @var \Illuminate\Filesystem\Filesystem
     */
    protected $files;

    /**
     * The ModuleData instance.
     *
     * @var \JMinayaT\Modules\Util\ModuleData
     */
    protected $moduledt;

    /**
     * The type of class being generated.
     *
     * @var string
     */
    protected $type;

    /**
     * Create a new controller creator command instance.
     *
     * @param  \Illuminate\Filesystem\Filesystem  $files
     * @return void
     */
    public function __construct(Filesystem $files, ModuleData $moduledt)
    {
        parent::__construct();
        $this->files = $files;
        $this->moduledt = $moduledt;
    }

    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    abstract protected function getStub();

    /**
     * Execute the console command.
     *
     * @return bool|null
     */
    public function handle()
    {
        $name = $this->qualifyClass($this->getNameInput());
        $module = $this->getStudly();
        if(! $this->moduleExists($module)) {
            $this->error('Module "'.$module. '" does not exist!; run module:create <name>');
            return false;
        }
        
        $path = $this->getPath($name);

        // First we will check to see if the class already exists. If it does, we don't want
        // to create the class and overwrite the user's code. So, we will bail out so the
        // code is untouched. Otherwise, we will continue generating this class' files.
        if ((! $this->hasOption('force') ||
             ! $this->option('force')) &&
             $this->alreadyExists($this->getNameInput())) {
            $this->error($this->type.' already exists!');

            return false;
        }

        // Next, we will generate the path to the location where this class' file should get
        // written. Then, we will build the class and make the proper replacements on the
        // stub files so that it gets the correctly formatted namespace and class name.
        $this->makeDirectory($path);

        $this->files->put($path, $this->buildClass($name));

        $this->info($this->type.' created successfully.');
    }

    /**
     * Parse the class name and format according to the root namespace.
     *
     * @param  string  $name
     * @return string
     */
    protected function qualifyClass($name)
    {
        $name = ltrim($name, '\\/');
        $rootNamespace = $this->rootNamespace();

        if (Str::startsWith($name, $rootNamespace)) {
            return $name;
        }
        $name = str_replace('/', '\\', $name);
        return $this->qualifyClass(
            $this->getDefaultNamespace(trim($rootNamespace, '\\')).'\\'.$name
        );
    }

    /**
     * Get the default namespace for the class.
     *
     * @param  string  $rootNamespace
     * @return string
     */
    protected function getDefaultNamespace($rootNamespace)
    {
        return $rootNamespace;
    }

    /**
     * Determine if the class already exists.
     *
     * @param  string  $rawName
     * @return bool
     */
    protected function alreadyExists($rawName)
    {
        return $this->files->exists($this->getPath($this->qualifyClass($rawName)));
    }

    /**
     * Get the destination class path.
     *
     * @param  string  $name
     * @return string
     */
    protected function getPath($name)
    {
        $name = Str::replaceFirst($this->rootNamespace(), '', $name);
        return $this->getBasePath().str_replace('\\', '/', $name).'.php';
    }

    /**
     * Get the module base path.
     *
     * @return string
     */
    protected function getBasePath()
    {
        return base_path('modules/'.$this->getStudly().'/');
    }

    /**
     * Get the module database path.
     *
     * @param  string  $name
     * @return string
     */
    protected function getDatabaseBasePath()
    {
        return base_path('modules/'.$this->getStudly().'/Database');
    }

    /**
     * Build the directory for the class if necessary.
     *
     * @param  string  $path
     * @return string
     */
    protected function makeDirectory($path)
    {
        if (! $this->files->isDirectory(dirname($path))) {
            $this->files->makeDirectory(dirname($path), 0777, true, true);
        }

        return $path;
    }

    /**
     * Build the class with the given name.
     *
     * @param  string  $name
     * @return string
     */
    protected function buildClass($name)
    {
        $stub = $this->files->get($this->getStub());

        return $this->replaceNamespace($stub, $name)->replaceClass($stub, $name);
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
        $stub = str_replace(
            ['DummyNamespace', 'DummyRootNamespace', 'NamespacedDummyUserModel', 'DummyModule'],
            [$this->getNamespace($name), $this->rootNamespace(), config('auth.providers.users.model'),str_replace($this->getNamespace($name).'\\', '', $name)],
            $stub
        );

        return $this;
    }

    /**
     * Get the full namespace for a given class, without the class name.
     *
     * @param  string  $name
     * @return string
     */
    protected function getNamespace($name)
    {
        return trim(implode('\\', array_slice(explode('\\', $name), 0, -1)), '\\');
    }

    /**
     * Replace the class name for the given stub.
     *
     * @param  string  $stub
     * @param  string  $name
     * @return string
     */
    protected function replaceClass($stub, $name)
    {
        $class = str_replace($this->getNamespace($name).'\\', '', $name);

        return str_replace('DummyClass', $class, $stub);
    }

    /**
     * Get the desired class name from the input.
     *
     * @return string
     */
    protected function getNameInput()
    {
        return trim($this->argument('name'));
    }

    /**
     * Get the desired class name module from the input.
     *
     * @return string
     */
    protected function getModuleNameInput()
    {
        return trim($this->argument('module'));
    }


    /**
     * Get the desired class name from the input.
     *
     * @return string
     */
    protected function getStudly()
    {
        return Str::studly($this->getModuleNameInput());
    }
     /**
     * Get the desired class name from the input.
     *
     * @return string
     */
    protected function getTitleCase()
    {
        return title_case($this->getModuleNameInput());
    }
    /**
     * Get the desired class name from the input.
     *
     * @return string
     */
    protected function getSnake()
    {
        return Str::snake($this->getModuleNameInput());
    }

    /**
     * Get the root namespace for the class.
     *
     * @return string
     */
    protected function rootNamespace()
    {
        return 'Modules\\'.$this->getStudly().'\\';
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

    /**
     * Get the console command arguments.
     *
     * @return array
     */
    protected function getArguments()
    {
        return [
            ['module', InputArgument::REQUIRED, 'The name of the module'],
            ['name',   InputArgument::REQUIRED, 'The name of the class'],
        ];
    }
}
