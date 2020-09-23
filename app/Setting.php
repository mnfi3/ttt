<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Setting extends Model
{
  use SoftDeletes;


  protected $fillable = ['key', 'value'];


  public static function get($key){
    $setting = Setting::orderBy('id', 'desc')->where('key', '=', $key)->first();
    if (is_null($setting)) {
      $setting = new Setting();
      $setting->key = $key;
    }
    return $setting;
  }

//  const KEY_INFO_UDATE_STATUS = 'info-update-status';
  //stock name update
  const KEY_STOCKS_NAME_UPDATE_TIME = 'stocks-update-time';//last update all stocks time
  const KEY_STOCKS_NAME_LAST_UPDATE_ID = 'stocks-name-last-update-id';//last id of updated stocks names

  //stock daily history update
  const KEY_STOCK_HISTORY_PRICE_UPDATE_LAST_ID = 'stock-history-price-update-last-id';
  const KEY_STOCK_HISTORY_CLIENT_TYPE_UPDATE_LAST_ID = 'stock-history-client-type-update-last-id';

  //stock today client types
  const KEY_STOCK_TODAY_CLIENT_TYPES_UPDATE_LAST_ID = 'stock-today-client-types-update-last-id';
  const KEY_STOCK_TODAY_CLIENT_TYPES_UPDATE_TIME = 'stock-today-client-types-update-time';
}
