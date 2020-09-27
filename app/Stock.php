<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Stock extends Model
{
  use SoftDeletes;

  protected $fillable = [
    'parent_id',
    'stock_type_id', //noe sahm.asli - hage tagadom - ekhtiyare kharid - ekhtiyare forush
    'stock_market_type_id', //noe bazar.burs - paye - faraburs - ...
    'stock_group_id', // goruhe sahm
    'ind',
    'code',
    'symbol',
    'name',
    'apply_date', //tarikhe emale hage kharid ya forush
    'apply_price', //gimate emale hage kharid ya forush
    'is_active', //aya emruz in sahm moamele mishavad?
  ];



  public function dailyInfos(){
    return $this->hasMany('App\StockDailyInfo');
  }
}
