<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class StockDailyInfo extends Model
{
  use SoftDeletes;

  protected $fillable = [
    'stock_id',

    'first',//avalin gimat
    'high',//bishtarin gimat
    'low',//kamtarin gimat
    'close',//gimate payani
    'value',//arzeshe moamelat
    'vol',//hajme moamelat
    'openint',//tedade moamelat
    'per',//dore gozaresh(ruzane)
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

    //etelaate api(in mored ba mored bala ehtemalan farg mikone)
    'api_individual_buy_count',//tedade kharide hagigi
    'api_corporate_buy_count',//tedade kharide hogugi
    'api_individual_sell_count',//tedade forushe hagigi
    'api_corporate_sell_count',//tedade forushe hogugi
    'api_individual_buy_vol',//hajme kharide hagigi
    'api_corporate_buy_vol',//hajme kharide hogugi
    'api_individual_sell_vol',//hajme forushe hagigi
    'api_corporate_sell_vol',//hajme forushe hogugi
    'api_individual_buy_value',//arzeshe kharide hagigi
    'api_corporate_buy_value',//arzeshe kharie hogugi
    'api_individual_sell_value',//arzeshe forushe hagigi
    'api_corporate_sell_value',//arzeshe forushe hogugi



    'stock_count',//tedade sahm
    'base_volume',//hajme mabna
    'floating_stocks',//sahame shenavar %
    'month_mean_volume',//miyangine hajme mah
    'eps',//eps
    'pe',//p/e
    'group_pe',//p/e guruh
    'status',//vaziat

    'date',//tarikh (format YYMMDD)
  ];
}
