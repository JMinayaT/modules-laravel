<?php
namespace JMinayaT\Modules\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Str;
use JMinayaT\Modules\Util\ModuleData;

class ModuleActiveCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'module:active {module : name of the module} {option : true or false}';

    /**
     * The console command description.php a
     *
     * @var string
     */
    protected $description = 'activate or disable module ';

    protected $moduledt;

    public function __construct(ModuleData $moduledt)
    {
        parent::__construct();
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
            $this->error('Module "'.$module. '" does not exist!');
            return false;
        }
        $option = $this->argument('option');

        if ($option == 'true' || $option == 'false') {
            if($option == 'true') {
                $this->moduledt->active($name,1);
                $this->info('module is activated!.');
            }
            else if($option == 'false') {
                $this->moduledt->active($name,0);
              $this->info('module is disable!.');
            }
        }
        else {
            $this->error('option not valid, please insert "true or false"');
            return false;
        }
    }
}
