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

    public function __construct(ManagerCommands $mnc, ModuleMigrator $migrator)
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
            $notes = $this->migrator->cmdGetMigrateNotes();
            foreach ($notes as $note) {
                $this->output->writeln($note);
            }
            return false;
        }
        $modules = Module::all();
        foreach ($modules as $module) {
            $this->output->writeln('Module Name: '.$module->name);
            $this->migrator->migrate($module->name);
            $notes = $this->migrator->cmdGetMigrateNotes();
            foreach ($notes as $note) {
                $this->output->writeln($note);
            }
        }
    }


}
