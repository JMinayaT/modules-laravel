<?php

namespace JMinayaT\Modules\Models;

use Illuminate\Database\Eloquent\Model;

class Module extends Model
{
  protected $table;

  public function __construct(array $attributes = [])
  {
    $this->setTable(config('modules.table_name'));
    parent::__construct($attributes);
  }

}
