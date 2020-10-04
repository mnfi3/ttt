<?php

namespace App\Http\Controllers\Util;


class Util {


  public static function getTradeDate($format = 'Ymd'){
    date_default_timezone_set('Asia/Tehran');
    $hour = date('H');

    if ($hour >= 0 && $hour < 8){
      return date($format,strtotime("-1 days"));
    }
    return date($format);
  }


  public static function getDayOfWeek($date = null){
    date_default_timezone_set('Asia/Tehran');

    $date = (is_null($date)) ? date('Y-m-d') : $date;
    return date('w', strtotime($date));
  }
}