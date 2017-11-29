<?php
namespace JMinayaT\Modules\Commands;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Str;
use JMinayaT\Modules\Commands\ManagerCommands;
use JMinayaT\Modules\Models\Module;

class ModuleUp extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'module:up {name_module}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Up config file module json';

    public function __construct(Filesystem $files, ManagerCommands $mnc)
    {
        parent::__construct();
        $this->files = $files;
        $this->mnc = new $mnc;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $name_module = Str::studly($this->argument('name_module'));
        if (! $this->mnc->hasModule($name_module)) {
            $this->error('Module "'.$name_module. '" does not exist!; run module:create NameModule');
            return false;
        }
        $module = Module::where('name', $name_module)->get()->first();

        $nameAuthor = $this->ask('Author of the module?');
        $emailAuthor = $this->ask('Email author of the module?');
        $type = 'installed';
        $version = $this->ask('module version?');
        $path = base_path('modules/'.$name_module .'/');

        $this->files->put($path.'module.json', $this->buildJson($name_module, $module->alias, $module->description,$nameAuthor,$emailAuthor,$type,$version));
        $this->info('Module Up successfully');
    }

    protected function buildJson($name, $alias, $description,$nameAuthor,$emailAuthor,$type,$version)
    {
        $getStub = $this->files->get($this->mnc->getStub('module.json'));
        $stub = str_replace(
                ['DummyName', 'DummyAlias','DummyDescription','DummyAuthorName','DummyEmailAuthor','dummyType','dummyVersion'],
                [$name, $alias, $description,$nameAuthor,$emailAuthor,$type,$version],
                $getStub
        );
        return $stub;
    }

  }
