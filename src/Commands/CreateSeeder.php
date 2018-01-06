<?php
namespace JMinayaT\Modules\Commands;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Str;
use JMinayaT\Modules\Commands\ManagerCommands;
use JMinayaT\Modules\Models\Module;

class CreateSeeder extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'module:make-seeder {name_module} {name_seeder}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new module seeder class';

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
        $name_seeder = ucfirst($this->argument('name_seeder'));
        $path = base_path('modules/'.$name_module .'/');

        if (!$this->mnc->hasModule($name_module)) {
            $this->error('Module "'.$name_module. '" does not exist!; run module:create NameModule');
            return false;
        }

        $path_seeder = $path . 'Database/Seeds/'.$name_seeder.'.php';

        if($this->files->isFile($path_seeder)){
            $this->error('Module "'.$name_module. '" seeder class already exists!');
            return false;
        }

        $name = $this->mnc->qualifyClass($name_module);
        $this->files->put($path_seeder, $this->mnc->buildClass($name, $name_seeder,'seeder'));

        $this->info('Module Seeder class created successfully');
    }

}
