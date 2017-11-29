<?php
namespace JMinayaT\Modules\Commands;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Str;
use JMinayaT\Modules\Commands\ManagerCommands;
use JMinayaT\Modules\Models\Module;

class PublishModule extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'module:publish {name_module}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'publish module zip file';

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
        $name_module = Str::studly($this->argument('name_module'));
        $count = Module::where('name', $name_module)->count();
        if (! $this->mnc->hasModule($name_module)) {
            $this->error('Module "'.$name_module. '" does not exist!; run module:create NameModule');
            return false;
        }

        if(! file_exists(base_path('modules/'.$name_module.'/module.json'))) {
            $this->error('Module is not UP please run  module:up NameModule');
            return false;
        }

        \Zipper::make( base_path('modules/publish_modules/'.$name_module.'.zip'))
                      ->add( base_path('modules/'.$name_module) )->close();

        $this->info('Module Publish successfully');
    }

}
