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
      return true;
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
        return true;
    }

    protected function getMigrationPaths($paths)
    {
        return collect($paths)->map(function ($path) {
            return base_path().'/'.$path;
        })->all();
    }

    public function viewGetMigrateNotes()
    {
        $notes = $this->migrator->getNotes();
        $newNotes = [];
        foreach($notes as $note) {
            $newNote = str_replace("<comment>", "", $note);
            $newNote = str_replace("</comment>", "", $newNote);
            $newNote = str_replace("<info>", "",$newNote);
            $newNote = str_replace("</info>", "", $newNote);
            $newNotes[] = $newNote;
        }
        return $newNotes; 
    }
    public function viewGetRollbackNotes()
    {
        $notes = $this->migrator->getNotes();
        $newNotes = [];
        foreach($notes as $note) {
            $newNote = str_replace("<comment>", "", $note);
            $newNote = str_replace("</comment>", "", $newNote);
            $newNote = str_replace("<info>", "", $newNote);
            $newNote = str_replace("</info>", "", $newNote);
            (strpos($newNote, 'Migration not found') == false ) ?  $newNotes[] = $newNote : null;
        }
        return (! empty($newNotes) ) ? $newNotes :  'Nothing to migrate'; 
    }

    public function cmdGetMigrateNotes()
    {
        $notes[] = (! empty($this->migrator->getNotes()) ) ? $this->migrator->getNotes() : 'Nothing to migrate'; 
        return $notes;
    }

    public function cmdGetRollbackNotes()
    {
        $newNotes = [];
        foreach($this->migrator->getNotes() as $note) {
            (strpos($note, 'Migration not found') == false ) ?  $newNotes[] = $note : null;
        }
        $notes[] = (! empty($newNotes) ) ? $newNotes : 'Nothing to rollback'; 
        return $notes;
    }
}
