<?php
namespace JMinayaT\Modules\Commands;

use Illuminate\Console\Command;
use JMinayaT\Modules\Models\Module;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use JMinayaT\Modules\Commands\ManagerCommands;

class ModuleDelete extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'module:delete {module : name of the module}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'delete module';

    protected $mnc;
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
        $name_module = Str::studly($this->argument('module'));
        if (!$this->mnc->hasModule($name_module)) {
            $this->error('Module "'.$name_module. '" does not exist!; run module:create NameModule');
            return false;
        }

        $migration_directory = base_path('modules/'.$name_module.'/Database/migrations');
        if ($this->confirm('Do you wish to continue?')) {
            $module = Module::where('name', $name_module)->first();
            $module->delete();
            $path = base_path('modules/'.$name_module.'/');

            $this->callSilent('migrate:rollback', [
                '--path' => $migration_directory
            ]);

            $this->files->deleteDirectory($path);
            $this->info('Module deleted successfully!.');
        }
    }

}
