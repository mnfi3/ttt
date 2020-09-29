<?php

namespace App\Http\Controllers\Stocks\Update;

use App\Http\Controllers\Stocks\Download\TseDownloader;
use App\Http\Controllers\Util\Util;
use App\Setting;
use App\Stock;
use App\StockDailyInfo;
use App\StockGroup;
use App\StockMarketType;
use Illuminate\Support\Facades\Log;


class TseDailyUpdater {

  public function __construct() { }



  //update stocks info at morning
  public function updateStocksInfo(){
    $downloader = new TseDownloader();

    //download stock with 3 retry
    $stocks = $downloader->downloadAllStocksNow();
    (count($stocks) == 0)? $stocks = $downloader->downloadAllStocksNow() : $stocks = $stocks;
    (count($stocks) == 0)? $stocks = $downloader->downloadAllStocksNow() : $stocks = $stocks;
    (count($stocks) == 0)? $stocks = $downloader->downloadAllStocksNow() : $stocks = $stocks;

    foreach ($stocks as $stock) {
      //find stock if exist
      $s = Stock::where('ind', '=', $stock['ind'])->first();

      if ($s == null) {
        try {
          //download stock data with 3 retry
          $info = $downloader->downloadStockOtherDataNow($stock['ind']);
          (count($info) == 0) ? $info = $downloader->downloadStockOtherDataNow($stock['ind']) : $info = $info;
          (count($info) == 0) ? $info = $downloader->downloadStockOtherDataNow($stock['ind']) : $info = $info;
          (count($info) == 0) ? $info = $downloader->downloadStockOtherDataNow($stock['ind']) : $info = $info;


          //find  stock group and market type--------------------------------------------------

          $sg = StockGroup::where('name', 'like', '%' . $info['group_name'] . '%')->first();
          $smt = StockMarketType::where('name', 'like', '%' . $info['market_type'] . '%')->first();
          (is_null($smt)) ? $smt_id = null : $smt_id = $smt->id;

          if ($sg == null) {
            $sg = StockGroup::create([
              'code' => '',
              'name' => $info['group_name'],
            ]);
          }


          $s = Stock::create([
            'stock_group_id' => $sg->id,
            'stock_market_type_id' => $smt_id,
            'ind' => $stock['ind'],
            'code' => $stock['code'],
            'symbol' => $stock['symbol'],
            'name' => $stock['name'],
          ]);

          $setting = Setting::get(Setting::KEY_STOCKS_NAME_LAST_UPDATE_ID);
          $setting->value = $s->id;
          $setting->save();


        } catch (\Exception $e) {
          echo 'catch ->' . $e;
          Log::error('updateStocksInfo.error=' . $e->getMessage() . '\tstock_ind=' . $stock['ind']);
        }
      }


    }

    $setting = Setting::get(Setting::KEY_STOCKS_NAME_UPDATE_TIME);
    $setting->value = date('Y-m-d H:i:s');
    $setting->save();


  }










}