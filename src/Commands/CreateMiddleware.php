<?php
namespace JMinayaT\Modules\Commands;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Str;
use JMinayaT\Modules\Commands\ManagerCommands;
use JMinayaT\Modules\Models\Module;

class CreateMiddleware extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'module:make-middleware {name_module} {name_middleware}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new module middleware class';

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
        $name_middleware = ucfirst($this->argument('name_middleware'));
        $path = base_path('modules/'.$name_module .'/');

        if (!$this->mnc->hasModule($name_module)) {
            $this->error('Module "'.$name_module. '" does not exist!; run module:create NameModule');
            return false;
        }

        $path_middleware = $path . 'Http/Middleware/'.$name_middleware.'.php';

        if($this->files->isFile($path_middleware)){
            $this->error('Module "'.$name_module. '" middleware class already exists!');
            return false;
        }

        $name = $this->mnc->qualifyClass($name_module);
        $this->files->put($path_middleware, $this->mnc->buildClass($name, $name_middleware,'middleware'));

        $this->info('Module middleware class created successfully');
    }

}
