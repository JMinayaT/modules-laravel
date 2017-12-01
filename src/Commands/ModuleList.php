<?php

namespace JMinayaT\Modules\Commands;

use JMinayaT\Modules\Models\Module;
use Illuminate\Console\Command;

class ModuleList extends Command
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

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $headers = ['Name','Alias', 'Description', 'Type','Active'];
        $modules = Module::all(['name', 'alias', 'description', 'type','active'])->toArray();
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
