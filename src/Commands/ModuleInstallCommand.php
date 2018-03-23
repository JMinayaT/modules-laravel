<?php
namespace JMinayaT\Modules\Commands;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Chumper\Zipper\Zipper;
use JMinayaT\Modules\Util\ModuleData;

class ModuleInstallCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'module:install {path} {--m|migration}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Install module from zip file';

    protected $files;
    protected $moduledt;
    protected $zipper;

    public function __construct(Filesystem $files, ModuleData $moduledt, Zipper $zipper)
    {
        parent::__construct();
        $this->files = $files;
        $this->moduledt = $moduledt;
        $this->zipper = $zipper;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $path = $this->argument('path');
        $this->zipper->make($path)->extractTo(storage_path("public"), array('module.json'), Zipper::WHITELIST);
        $file = $this->files->get(storage_path('public/module.json'));
        $json = json_decode($file, true);
        if ($this->moduledt->exists($json['name']) ) {
            if ($this->moduledt->version($json['name']) > $json['version']){
                $this->error('Current module version is higher');
                return false;
            }
            $this->zipper->make($path)->extractTo(base_path('modules/'.$json['name'].'/'));
            $this->moduledt->updateModuleDB($json['name'],$json['description'], $json['alias']);
        }
        else {
            $this->zipper->make($path)->extractTo(base_path('modules/'.$json['name'].'/'));
            $this->moduledt->registerModuleDB($json['name'],$json['description'], $json['alias']);
        }
        $this->files->delete(storage_path('module.json'));
        $this->info('Module install successfully!.');
    }
  }
