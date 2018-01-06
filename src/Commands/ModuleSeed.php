<?php
namespace JMinayaT\Modules\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Str;
use JMinayaT\Modules\Commands\ManagerCommands;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use JMinayaT\Modules\Models\Module;
use Illuminate\Filesystem\Filesystem;

class ModuleSeed extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'module:seed {module?} {--database=} {--force=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Module Seed the database with records';


    public function __construct(Filesystem $files ,ManagerCommands $mnc)
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
    
        if($this->argument('module')) {
            $name_module = Str::studly($this->argument('module'));
            if (!$this->mnc->hasModule($name_module)) {
                $this->error('Module "'.$name_module. '" does not exist!; run module:create <NameModule>');
                return false;
            }
            if(! $this->isFileDBS($name_module)){
                return false;
            }
            $this->dbSeed('Modules\\'.$name_module.'\Database\Seeds\DatabaseSeeder');
            return false;
        }

        $modules = Module::all();
        foreach ($modules as $module) {
            if(! $this->isFileDBS($module->name)) {
                continue;
            }
            $this->dbSeed('Modules\\'.$module->name.'\Database\Seeds\DatabaseSeeder');
        }
     
    }

    protected function dbSeed($class)
    {
        $params = ['--class' => $class];
        if ($option = $this->option('database')) {
            $params['--database'] = $option;
        }
        if ($option = $this->option('force')) {
            $params['--force'] = $option;
        }
        $this->call('db:seed', $params);
    }

    protected function isFileDBS($name_module)
    {
        $path = base_path('modules/'.$name_module .'/') . 'Database/Seeds/DatabaseSeeder.php';
        if($this->files->isFile($path)) {
            return true;
        }
        return false;
    }


}
