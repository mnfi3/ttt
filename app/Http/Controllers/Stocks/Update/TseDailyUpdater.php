<?php

namespace App\Http\Controllers\Stocks\Update;

use App\Http\Controllers\Stocks\Download\TseDownloader;
use App\Http\Controllers\Util\Util;
use App\Setting;
use App\Stock;
use App\StockDailyInfo;
use App\StockGroup;
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




      //stock exist in database and just update other data
      if ($s != null) {
        //create stock other data
        $stock_daily_info = StockDailyInfo::where('stock_id', '=', $s->id)->where('date', '=', Util::getTradeDate())->first();
        //create stock other
        if ($stock_daily_info == null){
          $info = $downloader->downloadStockOtherDataNow($stock['ind']);
          (count($info) == 0) ? $info = $downloader->downloadStockOtherDataNow($stock['ind']) : $info = $info;
          (count($info) == 0) ? $info = $downloader->downloadStockOtherDataNow($stock['ind']) : $info = $info;
          (count($info) == 0) ? $info = $downloader->downloadStockOtherDataNow($stock['ind']) : $info = $info;

          $stock_daily_info = StockDailyInfo::create([
            'stock_id' => $s->id,
            'date' => Util::getTradeDate(),
            'stock_count' => $info['stock_count'],
            'base_volume' => $info['base_volume'],
            'floating_stocks' => $info['floating_stocks'],
            'month_mean_volume' => $info['month_mean_volume'],
            'eps' => $info['eps'],
            'group_pe' => $info['group_pe'],
          ]);
        }else{

          //daily info of other data not exist
          $info = $downloader->downloadStockOtherDataNow($stock['ind']);
          (count($info) == 0) ? $info = $downloader->downloadStockOtherDataNow($stock['ind']) : $info = $info;
          (count($info) == 0) ? $info = $downloader->downloadStockOtherDataNow($stock['ind']) : $info = $info;
          (count($info) == 0) ? $info = $downloader->downloadStockOtherDataNow($stock['ind']) : $info = $info;

          $stock_daily_info->stock_count = $info['stock_count'];
          $stock_daily_info->base_volume = $info['base_volume'];
          $stock_daily_info->floating_stocks = $info['floating_stocks'];
          $stock_daily_info->month_mean_volume = $info['month_mean_volume'];
          $stock_daily_info->eps = $info['eps'];
          $stock_daily_info->group_pe = $info['group_pe'];
          $stock_daily_info->save();

        }
        $setting = Setting::get(Setting::KEY_STOCKS_NAME_LAST_UPDATE_ID);
        $setting->value = $s->id;
        $setting->save();

      }else {


        try {
          //download stock data with 3 retry
          $info = $downloader->downloadStockOtherDataNow($stock['ind']);
          (count($info) == 0) ? $info = $downloader->downloadStockOtherDataNow($stock['ind']) : $info = $info;
          (count($info) == 0) ? $info = $downloader->downloadStockOtherDataNow($stock['ind']) : $info = $info;
          (count($info) == 0) ? $info = $downloader->downloadStockOtherDataNow($stock['ind']) : $info = $info;


          //find or stock group--------------------------------------------------

          $sg = StockGroup::where('name', 'like', '%' . $info['group_name'] . '%')->first();

          if ($sg == null) {
            $sg = StockGroup::create([
              'code' => '',
              'name' => $info['group_name'],
            ]);
          }

          if ($s == null) {
            $s = Stock::create([
              'stock_group_id' => $sg->id,
              'ind' => $stock['ind'],
              'code' => $stock['code'],
              'symbol' => $stock['symbol'],
              'name' => $stock['name'],
            ]);
          }

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