<?php
namespace JMinayaT\Modules\Commands;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Str;
use JMinayaT\Modules\Commands\ManagerCommands;
use JMinayaT\Modules\Models\Module;

class CreateMigration extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'module:make-migration {name_module} {name_migration} {--m|model=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new module migration';

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
        $name_migration = $this->argument('name_migration');
        $path = base_path('modules/'.$name_module .'/');

        if (!$this->mnc->hasModule($name_module)) {
            $this->error('Module "'.$name_module. '" does not exist!; run module:create NameModule');
            return false;
        }

        $timestamp = date('Y_m_d_His', time());
        $path_migration = $path . 'Database/migrations/'.$timestamp.'_'.ucfirst($name_migration).'.php';

        $type = 'plain';
        if ($this->option('model')) {
            $type = 'model';
            $model = $this->option('model');
        }
        if($type == 'plain'){
            $this->files->put($path_migration, $this->builMigration($name_migration, $type));
        }
        else if($type == 'model'){
            $path_migrationM = $path . 'Database/migrations/'.$timestamp.'_create_'.$model.'_table.php';
            $this->files->put($path_migrationM, $this->builMigrationModel($name_migration, $model ,$type));
        }

      $this->info('Migration Model created successfully');
    }

    protected function builMigration($name_migration, $type)
    {
        $stub = $this->files->get(__DIR__.'/stubs/migration.'.$type.'.stub');
        return $this->mnc->replaceClass($stub, $name_migration);
    }

    protected function builMigrationModel($name_migration,$model, $type)
    {
        $stub = $this->files->get(__DIR__.'/stubs/migration.'.$type.'.stub');
        return $this->mnc->replaceModelTable($this->mnc->replaceClass($stub, $name_migration), $model);
    }

}
