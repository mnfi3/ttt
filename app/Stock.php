<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Stock extends Model
{
  use SoftDeletes;

  protected $fillable = ['parent_id', 'type', 'stock_group_id', 'ind', 'code', 'symbol', 'name'];

  public function dailyInfos(){
    return $this->hasMany('App\StockDailyInfo');
  }
}
