<?php
namespace JMinayaT\Modules\Commands;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Str;
use JMinayaT\Modules\Commands\ManagerCommands;
use JMinayaT\Modules\Models\Module;

class CreateModel extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'module:model {name_module} {name_model} {--m|migration}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new module model';

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
        $name_model = ucfirst($this->argument('name_model'));
        $path = base_path('modules/'.$name_module .'/');

        if (!$this->mnc->hasModule($name_module)) {
            $this->error('Module "'.$name_module. '" does not exist!; run module:create NameModule');
            return false;
        }

        $path_model = $path . 'Models/'.$name_model.'.php';

        if($this->files->isFile($path_model)){
            $this->error('Module "'.$name_module. '" Model already exists!');
            return false;
        }

        $name = $this->mnc->qualifyClass($name_module);
        $this->files->put($path_model, $this->mnc->buildClass($name, $name_model,'model.plain'));

        if ($this->option('migration')) {
            $nameMigration = 'Create'.$name_model.'sTable';
            $nameTable = strtolower($name_model).'s';
            $this->callSilent('module:migration', [
              'name_module' => $name_module, 'name_migration' => $nameMigration, '--model' => $nameTable
            ]);
        }

        $this->info('Module Model created successfully');
    }

}
