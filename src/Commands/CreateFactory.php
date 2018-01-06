<?php
namespace JMinayaT\Modules\Commands;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Str;
use JMinayaT\Modules\Commands\ManagerCommands;
use JMinayaT\Modules\Models\Module;

class CreateFactory extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'module:make-factory {name_module} {name_factory}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new module model factory';

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
        $name_factory = ucfirst($this->argument('name_factory'));
        $path = base_path('modules/'.$name_module .'/');

        if (!$this->mnc->hasModule($name_module)) {
            $this->error('Module "'.$name_module. '" does not exist!; run module:create NameModule');
            return false;
        }

        $path_factory = $path . 'Database/Factories/'.$name_factory.'.php';

        if($this->files->isFile($path_factory)){
            $this->error('Module "'.$name_module. '" Factory already exists!');
            return false;
        }

        $this->files->put($path_factory, $this->mnc->getFileStub('factory'));

        $this->info('Module model factory created successfully');
    }

}
