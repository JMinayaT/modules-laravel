<?php
namespace JMinayaT\Modules;

use JMinayaT\Modules\Models\Module as BaseModel;
use JMinayaT\Modules\Util\ModuleCollection;
use JMinayaT\Modules\Util\ModuleMigrator;
use Illuminate\Filesystem\Filesystem;
use Chumper\Zipper\Zipper;

class ModuleService extends BaseModel
{
    protected $table;
    protected $migrator;

    public function __construct(array $attributes = [])
    {
        $this->table = config('modules.table_name');
        $this->setTable($this->table);
        $this->migrator = new ModuleMigrator();
    }

    public function newCollection(array $models = [])
    {
        return new ModuleCollection($models);
    }

    public function get($name)
    {
        return $this->where('name', $name)->get();
    }

    public function getAll()
    {
        return $this->all();
    }

    public function has($name)
    {
        return (count($this->where('name', $name)->get()) == 0) ? false : true;
    }

    public function getEnabled()
    {
        return $this->where('active', 1)->get();
    }

    public function getDisabled()
    {
        return $this->where('active', 0)->get();
    }

    public function count()
    {
        return $this->all()->count();
    }

    public function install($path)
    {
        $files = new Filesystem();
        $zipper = new Zipper;
        $zipper->make($path)->extractTo(storage_path(), array('module.json'), Zipper::WHITELIST);
        $file = $files->get(storage_path('module.json'));
        $json = json_decode($file, true);
        if ($this->has($json['name'])) {
            return false;
        }
        $zipper->make($path)->extractTo(base_path('modules/'.$json['name'].'/'));
        $module = new $this;
        $module->name = $json['name'];
        $module->alias =$json['alias'];
        $module->description = $json['description'];
        $module->save();
        $this->moduleMigrate($json['name']);
        $files->delete(storage_path('module.json'));
        return true;
    }

    public function moduleMigrate($module)
    {  
        $this->migrator->migrate($module);
        return $this->migrator->viewGetMigrateNotes();

    }

    public function moduleMigrateAll()
    {
        $modules = $this->all();
        $notes = [];
        foreach ($modules as $module) {
            $this->migrator->migrate($module->name);
            $mgtNotes = $this->migrator->viewGetMigrateNotes();;
            array_unshift($mgtNotes,$module->name);
            $notes[] = $mgtNotes;
        }
        return $notes;
    }

    public function moduleRollback($module)
    {
        $this->migrator->rollback($module);
        return $this->migrator->viewGetRollbackNotes();
    }

    public function moduleRollbackAll()
    {
        $modules = $this->all();
        $notes = [];
        foreach ($modules as $module) {
            $this->migrator->rollback($module->name);
            $mgtNotes = $this->migrator->viewGetRollbackNotes();;
            array_unshift($mgtNotes,$module->name);
            $notes[] = $mgtNotes;
        }
        return $notes;
    }
}