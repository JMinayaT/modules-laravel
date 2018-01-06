<?php
namespace JMinayaT\Modules\Commands;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Str;
use JMinayaT\Modules\Commands\ManagerCommands;
use JMinayaT\Modules\Models\Module;

class CreateTest extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'module:make-test {name_module} {name_test}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new module test class';

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
        $name_test = ucfirst($this->argument('name_test'));
        $path = base_path('modules/'.$name_module .'/');

        if (!$this->mnc->hasModule($name_module)) {
            $this->error('Module "'.$name_module. '" does not exist!; run module:create <NameModule>');
            return false;
        }

        $path_test = $path . 'Tests/'.$name_test.'.php';

        if($this->files->isFile($path_test)) {
            $this->error('Module "'.$name_module. '" test class already exists!');
            return false;
        }

        $name = $this->mnc->qualifyClass($name_module);
        $this->files->put($path_test, $this->mnc->buildClass($name, $name_test,'test'));

        $this->info('Module test class created successfully');
    }

}
