<?php
namespace JMinayaT\Modules\Commands;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Chumper\Zipper\Zipper;
use JMinayaT\Modules\Util\ModuleData;

class ModuleInstall extends Command
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
        $zipper->make($path)->extractTo(storage_path(), array('module.json'), Zipper::WHITELIST);
        $file = $this->files->get(storage_path('module.json'));
        $json = json_decode($file, true);
        echo $this->moduledt->version($json['name']);
        return false;
        if ( $this->moduledt->exists($json['name']) ) {
            $this->error('Module exist!');
            return false;
        }

        $zipper->make($path)->extractTo(base_path('modules/'.$json['name'].'/'));
        $this->mnc->registerModuleDB($json['name'],$json['alias'],$json['description']);
        $this->files->delete(storage_path('module.json'));
        $this->info('Module install successfully!.');
    }
  }
