<?php
namespace JMinayaT\Modules\Commands;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Str;
use JMinayaT\Modules\Commands\ManagerCommands;
use JMinayaT\Modules\Models\Module;

class CreateController extends Command
{

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'module:make-controller {name_module} {name_controller}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new module controller';

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
        $name_controller = $this->argument('name_controller');

        if (!$this->mnc->hasModule($name_module)) {
            $this->error('Module "'.$name_module. '" does not exist!; run module:create NameModule');
            return false;
        }
        $path = base_path('modules/'.$name_module .'/');
        $path_controller = $path . 'Controllers/'.ucfirst($name_controller).'.php';

        if($this->files->isFile($path_controller)){
            $this->error('Module "'.$name_module. '" Controller already exists!');
            return false;
        }

        $name = $this->mnc->qualifyClass($name_module);
        $this->files->put($path_controller, $this->mnc->buildClass($name, $name_controller,'controller.plain'));

        $this->info('Module Controller created successfully');
    }

}
