<?php

namespace JMinayaT\Modules\Commands;

use Illuminate\Console\Command;
use JMinayaT\Modules\Util\ModuleData;

class ModuleListCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'module:list';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'show list of all modules';

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
        $headers = ['#','Name', 'Alias', 'Type','Description','Active', 'Created at', 'Updated at'];
        $modules = $this->moduledt->getArray();
        foreach ($modules as $pos => $module) {
            if($module['active'] == 1){
                $modules[$pos]['active'] = 'true';
            }
            else if ($module['active'] == 0){
                $modules[$pos]['active'] = 'false';
            }
        }
        $this->table($headers, $modules);
    }


}
