<?php
namespace JMinayaT\Modules\Commands;

use Illuminate\Console\Command;
use JMinayaT\Modules\Models\Module;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use JMinayaT\Modules\Commands\ManagerCommands;
use JMinayaT\Modules\Util\ModuleData;

class ModuleDeleteCommand extends Command
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

    protected $files;
    protected $moduledt;

    public function __construct(Filesystem $files, ModuleData $moduledt)
    {
        parent::__construct();
        $this->files = $files;
        $this->moduledt = $moduledt;

    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $name = Str::studly($this->argument('module'));
        if(! $this->moduledt->exists($name)) {
            $this->error('Module "'.$name. '" does not exist!');
            return false;
        }
        $migration_directory = base_path('modules/'.$name.'/Database/Migrations');
        if ($this->confirm('Do you wish to continue?')) {
            $this->moduledt->delete($name);
            $path = base_path('modules/'.$name.'/');
            $this->callSilent('migrate:rollback', [
                '--path' => $migration_directory
            ]);
            $this->files->deleteDirectory($path);
            $this->info('Module deleted successfully!.');
        }
    }
}
