<?php


namespace App\Http\Controllers\Stocks\Update;

use App\Http\Controllers\Stocks\Download\TseDownloader;
use App\Http\Controllers\Util\Util;
use App\Setting;
use App\Stock;
use App\StockDailyInfo;
use App\StockGroup;
use App\StockInstantInfo;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;


class TseInstantUpdater {

  public function __construct() {

  }



  public function updateInstantAllStocksPricesAndClientTypes(){
    $downloader = new TseDownloader();
    $time = date('YmdHis');

    $stocks = $downloader->downloadAllStocksAndRecentTradesNow();
    (count($stocks) == 0) ? $stocks = $downloader->downloadAllStocksAndRecentTradesNow() : $stocks = $stocks;
    (count($stocks) == 0) ? $stocks = $downloader->downloadAllStocksAndRecentTradesNow() : $stocks = $stocks;
    (count($stocks) == 0) ? $stocks = $downloader->downloadAllStocksAndRecentTradesNow() : $stocks = $stocks;

    $client_types = $downloader->downloadAllClientTypesNow();
    (count($client_types) == 0) ? $client_types = $downloader->downloadAllClientTypesNow() : $client_types = $client_types;
    (count($client_types) == 0) ? $client_types = $downloader->downloadAllClientTypesNow() : $client_types = $client_types;
    (count($client_types) == 0) ? $client_types = $downloader->downloadAllClientTypesNow() : $client_types = $client_types;


    $i=0;
    foreach ($stocks as $stock){
      $j=0;
      foreach ($client_types as $type){
        if ($stock['ind'] == $type['ind']){
          try {
            $s = Stock::where('ind', '=', $stock['ind'])->first();
            $s_instant = StockInstantInfo::create([
              'stock_id' => ($s != null) ? $s->id : 0,
              'ind' => $stock['ind'],
              'first' => $stock['first'],
              'high' => $stock['high'],
              'low' => $stock['low'],
              'close' => $stock['close'],
              'value' => $stock['value'],
              'vol' => $stock['vol'],
              'openint' => $stock['openint'],
              'open' => $stock['open'],
              'last' => $stock['last'],
              'change_percent' => (($stock['close'] - $stock['open']) / $stock['open']) * 100,

              'individual_buy_count' => $type['individual_buy_count'],
              'corporate_buy_count' => $type['corporate_buy_count'],
              'individual_buy_vol' => $type['individual_buy_vol'],
              'corporate_buy_vol' => $type['corporate_buy_vol'],
              'individual_sell_count' => $type['individual_sell_count'],
              'corporate_sell_count' => $type['corporate_sell_count'],
              'individual_sell_vol' => $type['individual_sell_vol'],
              'corporate_sell_vol' => $type['corporate_sell_vol'],

              'sell_count1' => (array_key_exists('sell_count1', $stock)) ? $stock['sell_count1'] : 0,
              'sell_vol1' => (array_key_exists('sell_vol1', $stock)) ? $stock['sell_vol1'] : 0,
              'sell_price1' => (array_key_exists('sell_price1', $stock)) ? $stock['sell_price1'] : 0,
              'buy_count1' => (array_key_exists('buy_count1', $stock)) ? $stock['buy_count1'] : 0,
              'buy_vol1' => (array_key_exists('buy_vol1', $stock)) ? $stock['buy_vol1'] : 0,
              'buy_price1' => (array_key_exists('buy_price1', $stock)) ? $stock['buy_price1'] : 0,

              'sell_count2' => (array_key_exists('sell_count2', $stock)) ? $stock['sell_count2'] : 0,
              'sell_vol2' => (array_key_exists('sell_vol2', $stock)) ? $stock['sell_vol2'] : 0,
              'sell_price2' => (array_key_exists('sell_price2', $stock)) ? $stock['sell_price2'] : 0,
              'buy_count2' => (array_key_exists('buy_count2', $stock)) ? $stock['buy_count2'] : 0,
              'buy_vol2' => (array_key_exists('buy_vol2', $stock)) ? $stock['buy_vol2'] : 0,
              'buy_price2' => (array_key_exists('buy_price2', $stock)) ? $stock['buy_price2'] : 0,

              'sell_count3' => (array_key_exists('sell_count3', $stock)) ? $stock['sell_count3'] : 0,
              'sell_vol3' => (array_key_exists('sell_vol3', $stock)) ? $stock['sell_vol3'] : 0,
              'sell_price3' => (array_key_exists('sell_price3', $stock)) ? $stock['sell_price3'] : 0,
              'buy_count3' => (array_key_exists('buy_count3', $stock)) ? $stock['buy_count3'] : 0,
              'buy_vol3' => (array_key_exists('buy_vol3', $stock)) ? $stock['buy_vol3'] : 0,
              'buy_price3' => (array_key_exists('buy_price3', $stock)) ? $stock['buy_price3'] : 0,

              'eps' => $stock['eps'],
              'pe' => (strlen($stock['close']) > 0 && $stock['eps'] > 0) ? $stock['close']/$stock['eps'] : null,
              'base_volume' => $stock['base_volume'],
              'stock_count' => $stock['stock_count'],

              'time' => $time


            ]);



            unset($client_types[$j]);

          }catch (\Exception $e){}
        }

        $j++;
      }

      $i++;
    }



  }


}