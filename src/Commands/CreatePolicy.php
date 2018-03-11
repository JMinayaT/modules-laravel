<?php
namespace JMinayaT\Modules\Commands;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Str;
use JMinayaT\Modules\Commands\ManagerCommands;
use JMinayaT\Modules\Models\Module;

class CreatePolicy extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'module:make-policy {name_module} {name_policy}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new module policy class';

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
        $name_policy = ucfirst($this->argument('name_policy'));
        $path = base_path('modules/'.$name_module .'/');

        if (!$this->mnc->hasModule($name_module)) {
            $this->error('Module "'.$name_module. '" does not exist!; run module:create NameModule');
            return false;
        }

        $path_policy = $path . 'Policies/'.$name_policy.'.php';

        if($this->files->isFile($path_policy)){
            $this->error('Module "'.$name_module. '" policy class already exists!');
            return false;
        }

        $name = $this->mnc->qualifyClass($name_module);
        $this->files->put($path_policy, 
                $this->replaceUserNamespace($this->mnc->buildClass($name, $name_policy,'policy.plain')));

        $this->info('Module policy class created successfully');
    }
    /**
     * Replace the User model namespace.
     *
     * @param  string  $stub
     * @return string
     */
    protected function replaceUserNamespace($stub)
    {
        if (! config('auth.providers.users.model')) {
            return $stub;
        }
        $stub2 = str_replace(
            ['NamespacedDummyUserModel'],
            [config('auth.providers.users.model')],
            $stub
        );
        return str_replace(
            $this->mnc->rootNamespace().'User',
            config('auth.providers.users.model'),
            $stub2
        );
    }

}
