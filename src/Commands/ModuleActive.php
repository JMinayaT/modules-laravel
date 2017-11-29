<?php
namespace JMinayaT\Modules\Commands;

use Illuminate\Console\Command;
use JMinayaT\Modules\Models\Module;
use Illuminate\Support\Str;
use JMinayaT\Modules\Commands\ManagerCommands;

class ModuleActive extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'module:active {module : name of the module} {option : true | false}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'activate | disable module ';

    protected $mnc;
    public function __construct(ManagerCommands $mnc)
    {
        parent::__construct();
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
        $option = $this->argument('option');
        $module = Module::where('name', $name_module)->first();

        if ($option == 'true' || $option == 'false') {
            if($option == 'true'){
                $module->active = 1;
                $module->save();
                $this->info('module is activated!.');
            }
            else if($option == 'false') {
              $module->active = 0;
              $module->save();
              $this->info('module is disable!.');
            }
        }

        else {
            $this->error('option not valid,  "true | false"');
            return false;
        }

    }

}
