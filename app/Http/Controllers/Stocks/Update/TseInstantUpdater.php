<?php


namespace App\Http\Controllers\Stocks\Update;

use App\Http\Controllers\Stocks\Download\TseDownloader;
use App\Http\Controllers\Util\Util;
use App\Setting;
use App\Stock;
use App\StockDailyInfo;
use App\StockGroup;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;


class TseInstantUpdater {

  public function __construct() {

  }




  //get all stocks and create new stocks in database (can run at morning)
  public function updateInstantAllStocksInfo(){
    $downloader = new TseDownloader();

    //download stock with 3 retry
    $stocks = $downloader->downloadAllStocksNow();
    (count($stocks) == 0)? $stocks = $downloader->downloadAllStocksNow() : $stocks = $stocks;
    (count($stocks) == 0)? $stocks = $downloader->downloadAllStocksNow() : $stocks = $stocks;
    (count($stocks) == 0)? $stocks = $downloader->downloadAllStocksNow() : $stocks = $stocks;

    foreach ($stocks as $stock) {
      //find stock if exist
      $s = Stock::where('ind', '=', $stock['ind'])->first();

      if ($s != null) continue;


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



      } catch (\Exception $e) {
        echo 'catch ->' . $e;
        Log::error('TseInstantUpdater=>updateAllStocksInfo.error=' . $e->getMessage() . '\tstock_ind=' . $stock['ind']);
      }


    }

    $setting = Setting::get(Setting::KEY_STOCKS_NAME_UPDATE_TIME);
    $setting->value = date('Y-m-d H:i:s');
    $setting->save();


  }



  public function updateInstantAllStocksPrices(){

  }
}