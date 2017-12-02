<?php
namespace JMinayaT\Modules\Util;

class ModuleMigrator
{
    /**
     * The migrator instance.
     *
     * @var \Illuminate\Database\Migrations\Migrator
     */
    protected $migrator;

    public function __construct()
    {
        $this->migrator = app('migrator');
    }

    /**
     * Prepare the migration database for running.
     *
     * @return void
     */
    protected function prepareDatabase()
    {
        $this->migrator->setConnection(null);
    }

    public function migrate($module_name)
    {
        $this->prepareDatabase();
        $this->migrator->run($this->getMigrationPaths('modules/'.$module_name.'/Database/migrations/'),
        ['pretend' =>  null,
          'step'   =>  null,
        ]);
        return $this->migrator->getNotes();
    }
    
    public function reset($module_name)
    {
        $this->prepareDatabase();
        $this->migrator->reset(
            $this->getMigrationPaths('modules/'.$module_name.'/Database/migrations/'), null
        );
        return $this->migrator->getNotes();
    }

    public function rollback($module_name)
    {
        $this->prepareDatabase();
        $this->migrator->rollback($this->getMigrationPaths('modules/'.$module_name.'/Database/migrations/'),
        ['pretend' =>  null,
          'step'   =>  null,
        ]);
        return $this->migrator->getNotes();
    }

    protected function getMigrationPaths($paths)
    {
        return collect($paths)->map(function ($path) {
            return base_path().'/'.$path;
        })->all();
    }

}
