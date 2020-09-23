<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class StockGroup extends Model
{
  use SoftDeletes;

  protected $fillable = ['code', 'name'];
}
