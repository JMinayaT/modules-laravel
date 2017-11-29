<?php
namespace JMinayaT\Modules\Commands;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use JMinayaT\Modules\Commands\ManagerCommands;
use Chumper\Zipper\Zipper;

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

    public function __construct(Filesystem $files, ManagerCommands $mnc)
    {
        parent::__construct();
        $this->files = $files;
        $this->mnc = new $mnc;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $zipper = new Zipper;
        $path = $this->argument('path');
        $zipper->make($path)->extractTo(storage_path(), array('module.json'), Zipper::WHITELIST);
        $file = $this->files->get(storage_path('module.json'));
        $json = json_decode($file, true);

        if ( $this->mnc->hasModule($json['name']) ) {
            $this->error('Module exist!');
            return false;
        }

        $zipper->make($path)->extractTo(base_path('modules/'.$json['name'].'/'));
        $this->mnc->registerModuleDB($json['name'],$json['alias'],$json['description']);
        $this->files->delete(storage_path('module.json'));
        $this->info('Module install successfully!.');
    }
  }
