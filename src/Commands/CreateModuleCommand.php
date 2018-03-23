<?php

namespace JMinayaT\Modules\Commands;

use Illuminate\Support\Str;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use JMinayaT\Modules\Util\GeneratorCommand;
use JMinayaT\Modules\Models\Module;

class CreateModuleCommand extends GeneratorCommand
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'module:create';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new module';

    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub(){}

    /**
     * Execute the console command.
     *
     * @return bool|null
     */
    public function handle()
    {
        $name = $this->getStudly();
        if($this->moduleExists($name)) {
            $this->error('Module already exists!');
            return false;
        }
        $dcp = $this->ask('module description?');
        $path = $this->getBasePath();
        $this->makeDirectory($path);
        $this->makeDirectory($path.'Http/Controllers');
        $this->makeDirectory($path.'Resources/assets/js');
        $this->makeDirectory($path.'Resources/assets/css');
        $this->makeDirectory($path.'Resources/views');
        $this->makeDirectory($path.'Resources/lang');
        $this->makeDirectory($path.'Models');
        $this->makeDirectory($path.'Routes');
        $this->makeDirectory($path.'Database/Migrations');
        $this->makeDirectory($path.'Database/Factories');
        $this->makeDirectory($path.'Database/Seeds');

        $this->files->put($path.'Routes/web.php', $this->files->get(__DIR__.'/stubs/route.web.stub'));
        $this->files->put($path.'Routes/api.php', $this->files->get(__DIR__.'/stubs/route.api.stub'));

        $this->files->put($path.'Database/Seeds/DatabaseSeeder.php', $this->dataBaseSeederbuildClass($name));  
        $this->files->put($path.'module.json', $this->buildJson($name, $this->getSnake(), $dcp));
        $this->moduledt->registerModuleDB($name, $dcp, $this->getSnake());
        $this->info('Module created successfully.');

    }
    /**
     * Build the class with the given name.
     *
     * @param  string  $name
     * @return string
     */
    protected function dataBaseSeederbuildClass($name)
    {
        $stub = $this->files->get(__DIR__.'/stubs/database-seeder.stub');

        return $this->replaceNamespace($stub, $name)->replaceClass($stub, $name);
    }

    /**
     * Build the directory for the class if necessary.
     *
     * @param  string  $path
     * @return string
     */
    protected function makeDirectory($path)
    {
        if (! $this->files->isDirectory($path)) {
            $this->files->makeDirectory($path, 0777, true, true);
        }
        return $path;
    }

    protected function getOptions()
    {
        return [
            ['controller', 'c',  InputOption::VALUE_OPTIONAL, 'create controller module'],
            ['model',      'd',  InputOption::VALUE_OPTIONAL, 'create model module'],
            ['migration',  'm',  InputOption::VALUE_OPTIONAL, 'create migration for model module'],
        ];
    }

    /**
     * Get the console command arguments.
     *
     * @return array
     */
    protected function getArguments()
    {
        return [
            ['module',   InputArgument::REQUIRED, 'The name of the module'],
        ];
    }

    protected function buildJson($name, $alias, $description)
    {
        $getStub = $this->files->get(__DIR__.'/stubs/module.json.stub');
        $stub = str_replace(
                ['DummyName', 'DummyAlias','DummyDescription'],
                [$name, $alias, $description],
                $getStub
        );
        return $stub;
    }
}