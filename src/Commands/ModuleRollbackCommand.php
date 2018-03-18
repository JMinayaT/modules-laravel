<?php
namespace JMinayaT\Modules\Commands;

use Illuminate\Console\Command;
use JMinayaT\Modules\Models\Module;
use Illuminate\Support\Str;
use JMinayaT\Modules\Commands\ManagerCommands;
use JMinayaT\Modules\Util\ModuleMigrator;
use JMinayaT\Modules\Util\ModuleData;

class ModuleRollbackCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'module:rollback {module?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Rollback the last module database migration';

    protected $migrator;

    protected $moduledt;

    public function __construct(ModuleData $moduledt, ModuleMigrator $migrator)
    {
        parent::__construct();
        $this->moduledt = $moduledt;
        $this->migrator = $migrator;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        if($this->argument('module')) {
            $name = Str::studly($this->argument('module'));
            if(! $this->moduledt->exists($name)) {
                $this->error('Module "'.$name. '" does not exist!');
                return false;
            }
            $this->migrator->rollback($name);
            $notes = $this->migrator->cmdGetMigrateNotes();
            foreach ($notes as $note) {
                $this->output->writeln($note);
            }
            return false;
        }

        $modules = Module::all();
        foreach ($modules as $module) {
            $this->output->writeln('Module Name: '.$module->name);
            $this->migrator->rollback($module->name);
            $notes = $this->migrator->cmdGetMigrateNotes();
            foreach ($notes as $note) {
                $this->output->writeln($note);
            }
        }
    }


}
