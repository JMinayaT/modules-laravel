<?php
namespace JMinayaT\Modules\Commands;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Str;
use JMinayaT\Modules\Commands\ManagerCommands;
use JMinayaT\Modules\Models\Module;

class CreateRequest extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'module:make-request {name_module} {name_request}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new module request class';

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
        $name_request = ucfirst($this->argument('name_request'));
        $path = base_path('modules/'.$name_module .'/');

        if (!$this->mnc->hasModule($name_module)) {
            $this->error('Module "'.$name_module. '" does not exist!; run module:create NameModule');
            return false;
        }

        $path_request = $path . 'Http/Requests/'.$name_request.'.php';

        if($this->files->isFile($path_request)){
            $this->error('Module "'.$name_module. '" request class already exists!');
            return false;
        }

        $name = $this->mnc->qualifyClass($name_module);
        $this->files->put($path_request, $this->mnc->buildClass($name, $name_request,'request'));

        $this->info('Module request class created successfully');
    }

}
