<?php
namespace JMinayaT\Modules\Commands;

use JMinayaT\Modules\Models\Module;
use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Str;
use JMinayaT\Modules\Commands\ManagerCommands;

class CreateModule extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'module:create {name_module} {--c|controller} {--d|model}  {--m|migration}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new module';

    protected $mnc;
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
        $name = Str::lower($this->argument('name_module'));
        $name_module = Str::studly($this->argument('name_module'));
        $count = Module::where('name', $name_module)->count();

        if ($this->mnc->hasModule($name_module)) {
            $this->error('Module already exists!');
            return false;
        }

        $dcp = $this->ask('module description?');
        $path = base_path('modules/'.$name_module .'/');
        $this->makeDirectorys($path);
        $this->copyRoutes($path);
        $this->copyModuleFilesClass($path,$name_module);

        $this->mnc->registerModuleDB($name_module,$dcp, $name);

        if ($this->option('controller')) {
            $this->callSilent('module:make-controller', [
              'name_module' => $name_module, 'name_controller' => $name_module.'Controller'
            ]);
        }

        if ($this->option('model') && $this->option('migration')) {
            $this->callSilent('module:make-model', [
              'name_module' => $name_module, 'name_model' => $name_module, '--migration' => 'default',
            ]);
        }

        else {
            if ($this->option('model')) {
                $this->callSilent('module:make-model', [
                  'name_module' => $name_module, 'name_model' => $name_module
                ]);
            }
        }

        $this->info('Module created successfully!.');
    }

    protected function makeDirectorys($path)
    {
        if (! $this->files->isDirectory($path)) {
            $this->files->makeDirectory($path, 0775, true);
            $this->files->makeDirectory($path.'Controllers/', 0775, true);
            $this->files->makeDirectory($path.'Resources/Assets/js/', 0775, true);
            $this->files->makeDirectory($path.'Resources/Assets/css/', 0775, true);
            $this->files->makeDirectory($path.'Resources/Lang/', 0775, true);
            $this->files->makeDirectory($path.'Resources/Views/', 0775, true);
            $this->files->makeDirectory($path.'Routes/', 0775, true);
            $this->files->makeDirectory($path.'Models/', 0775, true);
            $this->files->makeDirectory($path.'Database/', 0775, true);
            $this->files->makeDirectory($path.'Database/Migrations/', 0775, true);
            $this->files->makeDirectory($path.'Database/Factories/', 0775, true);
            $this->files->makeDirectory($path.'Database/Seeds/', 0775, true);
            $this->files->makeDirectory($path.'Tests/', 0775, true);
      }
    }

    protected function copyRoutes($path)
    {
        $this->files->put($path.'Routes/web.php', $this->files->get($this->mnc->getStub('route.web')));
        $this->files->put($path.'Routes/api.php', $this->files->get($this->mnc->getStub('route.api')));
    }

    protected function copyModuleFilesClass($path,$name_module)
    {
        $name = $this->mnc->qualifyClass($name_module);

        $this->files->put($path.'Database/Seeds/DatabaseSeeder.php',$this->mnc->buildClass($name, '','database-seeder'));          
    }

}
