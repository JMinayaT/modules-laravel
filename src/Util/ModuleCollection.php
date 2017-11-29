<?php
namespace JMinayaT\Modules\Util;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Str;
use Illuminate\Filesystem\Filesystem;
use JMinayaT\Modules\Util\ModuleMigrator;


class ModuleCollection extends Collection
{
    public function active()
    {
        $item = $this->first();
        $item->active = 1;
        $item->save();
        return true;
    }

    public function disable()
    {
        $item = $this->first();
        $item->active = 0;
        $item->save();
        return true;
    }

    public function delete()
    {
        $migrator = new ModuleMigrator();
        $item = $this->first();
        $path =  $this->getPath();
        $migrator->rollback($item->name);
        \File::deleteDirectory($path);
        $item->delete();
        return true;
    }

    public function getPath()
    {
        $item = $this->first();
        return base_path('modules/'.$item->name .'/');
    }

    public function status()
    {
        $item = $this->first();
        if($item->active == 0) {
            return false;
        }
        if($item->active == 1) {
            return true;
        }
    }

    public function studlyName()
    {
        $item = $this->first();
        return Str::studly($item->name);
    }

    public function getModuleJson()
    {
        $item = $this->first();
        $json = \File::get($this->getPath().'module.json');
        return json_decode($json, true);
    }
}
