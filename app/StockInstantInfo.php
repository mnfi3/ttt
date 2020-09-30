<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class StockInstantInfo extends Model
{
  use SoftDeletes;

  protected $fillable = [
    'stock_id',
    'ind',

    'first',//avalin gimat
    'high',//bishtarin gimat
    'low',//kamtarin gimat
    'close',//gimate payani
    'value',//arzeshe moamelat
    'vol',//hajme moamelat
    'openint',//tedade moamelat
    'open',//gimate diruz(gimate baz shodan)
    'last',//akharin moamele
    'change_percent',//darsade taghyirat

    'individual_buy_count',//tedade kharide hagigi
    'corporate_buy_count',//tedade kharide hogugi
    'individual_sell_count',//tedade forushe hagigi
    'corporate_sell_count',//tedade forushe hogugi
    'individual_buy_vol',//hajme kharide hagigi
    'corporate_buy_vol',//hajme kharide hogugi
    'individual_sell_vol',//hajme forushe hagigi
    'corporate_sell_vol',//hajme forushe hogugi
    'individual_buy_value',//arzeshe kharide hagigi
    'corporate_buy_value',//arzeshe kharie hogugi
    'individual_sell_value',//arzeshe forushe hagigi
    'corporate_sell_value',//arzeshe forushe hogugi

    //recent trades
    'sell_count1',
    'sell_vol1',
    'sell_price1',
    'buy_count1',
    'buy_vol1',
    'buy_price1',

    'sell_count2',
    'sell_vol2',
    'sell_price2',
    'buy_count2',
    'buy_vol2',
    'buy_price2',

    'sell_count3',
    'sell_vol3',
    'sell_price3',
    'buy_count3',
    'buy_vol3',
    'buy_price3',

    //other data
    'eps',
    'pe',
    'base_volume',
    'stock_count',

    'time',//zaman
  ];
}
