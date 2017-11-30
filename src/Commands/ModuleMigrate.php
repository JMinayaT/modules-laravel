<?php
namespace JMinayaT\Modules\Commands;

use Illuminate\Console\Command;
use JMinayaT\Modules\Util\ModuleMigrator;
use JMinayaT\Modules\Models\Module;
use Illuminate\Support\Str;
use JMinayaT\Modules\Commands\ManagerCommands;

class ModuleMigrate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'module:migrate {module?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Migrate module';

    protected $migrator;

    public function __construct(ManagerCommands $mnc, ModuleMigrate $migrator)
    {
        parent::__construct();
        $this->mnc = new $mnc;
        $this->migrator = new $migrator;
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
                $this->error('Module "'.$name_module. '" does not exist!; run module:create NameModule');
                return false;
            }
            $this->migrator->migrate($name_module);
            $this->info('Migration successfully');
            return false;
        }
        $modules = Module::all();
        foreach ($modules as $module) {
            $this->migrator->migrate($module->name);
        }
        $this->info('Migrations successfully');
    }


}
