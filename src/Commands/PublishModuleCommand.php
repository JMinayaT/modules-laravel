<?php
namespace JMinayaT\Modules\Commands;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Str;
use JMinayaT\Modules\Util\ModuleData;
use Chumper\Zipper\Zipper;

class PublishModuleCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'module:publish {module}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'publish module zip file';

    
    protected $moduledt;
    protected $zipper;

    public function __construct(ModuleData $moduledt, Zipper $zipper)
    {
        parent::__construct();
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
        $name = Str::studly($this->argument('module'));
        if(! $this->moduledt->exists($name)) {
            $this->error('Module "'.$name. '" does not exist!');
            return false;
        }

        $this->zipper->make(base_path('modules/publish_modules/'.$name.'.zip'))
                      ->add( base_path('modules/'.$name) )->close();

        $this->info('Module Publish successfully');
    }

}
