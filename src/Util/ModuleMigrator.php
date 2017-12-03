<?php
namespace JMinayaT\Modules\Util;

use Illuminate\Support\Facades\DB;

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

    public function rollback($module_name)
    {
        $mgt = DB::select('SELECT batch from migrations ORDER by batch DESC LIMIT 1');
        $step = 0;
        foreach($mgt as $n) {
            $step = $n->batch;
        }
        $this->prepareDatabase();
        $this->migrator->rollback($this->getMigrationPaths('modules/'.$module_name.'/Database/migrations/'),
        ['pretend' =>  null,
          'step'   =>  $step,
        ]);
        return $this->migrator->getNotes();
    }

    public function dbGetNotes()
    {
        $notes = $this->migrator->getNotes();
        $nNotes = [];
        foreach($notes as $note) {
            $newNote = null;
            $newNote = str_replace("<comment>", "", $note);
            $newNote = str_replace("</comment>", "", $newNote);
            $newNote = str_replace("<info>", "", $newNote);
            $newNote = str_replace("</info>", "", $newNote);
            ( strpos($newNote, 'back') == true ) ?  $nNotes[] = $newNote : null;
        }
        return (! empty($nNotes) ) ? $nNotes : 'Nothing to rollback'; 
    }

    protected function getMigrationPaths($paths)
    {
        return collect($paths)->map(function ($path) {
            return base_path().'/'.$path;
        })->all();
    }

}
