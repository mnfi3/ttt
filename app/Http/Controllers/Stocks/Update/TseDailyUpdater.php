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
  public function updateStocksInfoBeforeMarket(){
    $downloader = new TseDownloader();

    //download stock with 3 retry
    $stocks = $downloader->downloadAllStocksNow();
    (count($stocks) == 0)? $stocks = $downloader->downloadAllStocksNow() : $stocks = $stocks;
    (count($stocks) == 0)? $stocks = $downloader->downloadAllStocksNow() : $stocks = $stocks;
    (count($stocks) == 0)? $stocks = $downloader->downloadAllStocksNow() : $stocks = $stocks;

    foreach ($stocks as $stock) {
      //find stock if exist
      $s = Stock::where('ind', '=', $stock['ind'])->first();


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

          if ($s == null) {

            $s = Stock::create([
              'stock_group_id' => $sg->id,
              'stock_market_type_id' => $smt_id,
              'ind' => $stock['ind'],
              'code' => $stock['code'],
              'symbol' => $stock['symbol'],
              'name' => $stock['name'],
              'is_active' => 1,
            ]);


          }else{
            $s->stock_group_id = $sg->id;
            $s->stock_market_type_id = $smt_id;
            $s->code = $stock['code'];
            $s->symbol = $stock['symbol'];
            $s->name = $stock['name'];
            $s->is_active = 1;
            $s->save();
          }


          $setting = Setting::get(Setting::KEY_STOCKS_NAME_LAST_UPDATE_ID);
          $setting->value = $s->id;
          $setting->save();


        } catch (\Exception $e) {
          echo 'catch ->' . $e;
          Log::error('updateStocksInfoBeforeMarket.error=' . $e->getMessage() . '\tstock_ind=' . $stock['ind']);
        }



    }

    $setting = Setting::get(Setting::KEY_STOCKS_NAME_UPDATE_TIME);
    $setting->value = date('Y-m-d H:i:s');
    $setting->save();


  }



  //update stocks info after market
  public function updateStocksInfoAfterMarket(){
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
            'ind' => $s->ind,
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


          $smt = StockMarketType::where('name', 'like', '%' . $info['market_type'] . '%')->first();
          (is_null($smt))? $smt_id = null : $smt_id = $smt->id;


          $stock_daily_info->stock_market_type_id = $smt_id;
          $stock_daily_info->stock_count = $info['stock_count'];
          $stock_daily_info->base_volume = $info['base_volume'];
          $stock_daily_info->floating_stocks = $info['floating_stocks'];
          $stock_daily_info->month_mean_volume = $info['month_mean_volume'];
          $stock_daily_info->eps = $info['eps'];
          $stock_daily_info->group_pe = $info['group_pe'];
          $stock_daily_info->save();

        }

        //update stock

        $sg = StockGroup::where('name', 'like', '%' . $info['group_name'] . '%')->first();
        $smt = StockMarketType::where('name', 'like', '%' . $info['market_type'] . '%')->first();
        (is_null($smt))? $smt_id = null : $smt_id = $smt->id;

        if ($sg == null) {
          $sg = StockGroup::create([
            'code' => '',
            'name' => $info['group_name'],
          ]);
        }

        $s->stock_group_id = $sg->id;
        $s->stock_market_type_id = $smt_id;
        $s->code = $stock['code'];
        $s->symbol = $stock['symbol'];
        $s->name = $stock['name'];
        $s->save();


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
          $smt = StockMarketType::where('name', 'like', '%' . $info['market_type'] . '%')->first();
          (is_null($smt))? $smt_id = null : $smt_id = $smt->id;

          if ($sg == null) {
            $sg = StockGroup::create([
              'code' => '',
              'name' => $info['group_name'],
            ]);
          }




          if ($s == null) {
            $s = Stock::create([
              'stock_group_id' => $sg->id,
              'stock_market_type_id' => $smt_id,
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
          Log::error('updateStocksInfoAfterMarket.error=' . $e->getMessage() . '\tstock_ind=' . $stock['ind']);
        }
      }


    }

    $setting = Setting::get(Setting::KEY_STOCKS_NAME_UPDATE_TIME);
    $setting->value = date('Y-m-d H:i:s');
    $setting->save();


  }



  //update prices after market
  public function updateStocksPricesAfterMarket(){
    $downloader = new TseDownloader();
    $records = $downloader->downloadStocksPricesNow();
    (count($records) == 0)? $records = $downloader->downloadStocksPricesNow() : $records = $records;
    (count($records) == 0)? $records = $downloader->downloadStocksPricesNow() : $records = $records;
    (count($records) == 0)? $records = $downloader->downloadStocksPricesNow() : $records = $records;

    foreach ($records as $price){
      $stock = Stock::where('ind', '=', $price['ind'])->first();
      if ($stock == null) continue;

      $stock_daily_info = StockDailyInfo::where('stock_id', '=', $stock->id)->where('date', '=', Util::getTradeDate())->first();
      if ($stock_daily_info == null) {
        $stock_daily_info = StockDailyInfo::create([
          'stock_id' => $stock->id,
          'ind' => $stock->ind,
          'first' => $price['first'],
          'high' => $price['high'],
          'low' => $price['low'],
          'close' => $price['close'],
          'value' => $price['value'],
          'vol' => $price['vol'],
          'openint' => $price['openint'],
          'per' => 'D',
          'open' => $price['open'],
          'last' => $price['last'],
          'change_percent' => (($price['close'] - $price['open']) / $price['open']) * 100,
          'eps' => $price['eps'],
          'pe' => ($price['eps'] != 0)? $price['close'] / $price['eps'] : 0,
          'base_volume' => $price['base_volume'],
          'stock_count' => $price['stock_count'],
          'date' => Util::getTradeDate(),
        ]);
      } else {
        $stock_daily_info->first = $price['first'];
        $stock_daily_info->high = $price['high'];
        $stock_daily_info->low = $price['low'];
        $stock_daily_info->close = $price['close'];
        $stock_daily_info->value = $price['value'];
        $stock_daily_info->vol = $price['vol'];
        $stock_daily_info->openint = $price['openint'];
        $stock_daily_info->per = 'D';
        $stock_daily_info->open = $price['open'];
        $stock_daily_info->last = $price['last'];
        $stock_daily_info->change_percent = (($price['close'] - $price['open']) / $price['open']) * 100;
        $stock_daily_info->eps = $price['eps'];
        $stock_daily_info->pe = ($price['eps'] != 0)? $price['close'] / $price['eps'] : $stock_daily_info->pe;
        $stock_daily_info->base_volume = $price['base_volume'];
        $stock_daily_info->stock_count = $price['stock_count'];
        $stock_daily_info->save();
      }

      $setting = Setting::get(Setting::KEY_STOCK_HISTORY_PRICE_UPDATE_LAST_ID);
      $setting->value = $stock->id;
      $setting->save();

    }

  }



  //update client types after market
  public function updateStocksClientTypesAfterMarket(){
    $downloader = new TseDownloader();
    $records = $downloader->downloadAllClientTypesNow();
    (count($records) == 0)? $records = $downloader->downloadAllClientTypesNow() : $records = $records;
    (count($records) == 0)? $records = $downloader->downloadAllClientTypesNow() : $records = $records;
    (count($records) == 0)? $records = $downloader->downloadAllClientTypesNow() : $records = $records;


    foreach ($records as $record){
      $stock = Stock::where('ind', '=', $record['ind'])->first();
      if ($stock == null) continue;

      $stock_daily_info = StockDailyInfo::where('stock_id', '=', $stock->id)->where('date', '=', Util::getTradeDate())->first();
      if ($stock_daily_info == null){
        $stock_daily_info = StockDailyInfo::create([
          'stock_id' => $stock->id,
          'ind' => $stock->ind,
          'individual_buy_count' => $record['individual_buy_count'],
          'corporate_buy_count' => $record['corporate_buy_count'],
          'individual_buy_vol' => $record['individual_buy_vol'],
          'corporate_buy_vol' => $record['corporate_buy_vol'],
          'individual_sell_count' => $record['individual_sell_count'],
          'corporate_sell_count' => $record['corporate_sell_count'],
          'individual_sell_vol' => $record['individual_sell_vol'],
          'corporate_sell_vol' => $record['corporate_sell_vol'],
          'date' => Util::getTradeDate(),
        ]);
      }else{
        $stock_daily_info->individual_buy_count = $record['individual_buy_count'];
        $stock_daily_info->corporate_buy_count = $record['corporate_buy_count'];
        $stock_daily_info->individual_buy_vol = $record['individual_buy_vol'];
        $stock_daily_info->corporate_buy_vol = $record['corporate_buy_vol'];
        $stock_daily_info->individual_sell_count = $record['individual_sell_count'];
        $stock_daily_info->corporate_sell_count = $record['corporate_sell_count'];
        $stock_daily_info->individual_sell_vol = $record['individual_sell_vol'];
        $stock_daily_info->corporate_sell_vol = $record['corporate_sell_vol'];
        $stock_daily_info->save();
      }

      $setting = Setting::get(Setting::KEY_STOCK_TODAY_CLIENT_TYPES_UPDATE_LAST_ID);
      $setting->value = $stock->id;
      $setting->save();
    }

    $setting = Setting::get(Setting::KEY_STOCK_TODAY_CLIENT_TYPES_UPDATE_TIME);
    $setting->value = date('Y-m-d H:i:s');
    $setting->save();
  }



  //update client types from api at night
  public function updateStocksClientTypesFromApi(){
    $downloader = new TseDownloader();
    $stocks = Stock::where('active', '=', 1)->get();

    foreach ($stocks as $stock){
      //download client types history----------------------------------------------------

      try {
        $records = $downloader->downloadStockClientTypeHistory($stock->ind);
        (count($records) == 0) ? $records = $downloader->downloadStockClientTypeHistory($stock->ind) : $records = $records;
        (count($records) == 0) ? $records = $downloader->downloadStockClientTypeHistory($stock->ind) : $records = $records;
        (count($records) == 0) ? $records = $downloader->downloadStockClientTypeHistory($stock->ind) : $records = $records;

        foreach ($records as $record) {
          if ($record['date'] != Util::getTradeDate() && $record['date'] != (Util::getTradeDate() - 1)) continue;
          $stock_daily_info = StockDailyInfo::where('stock_id', '=', $stock->id)->where('date', '=', $record['date'])->first();
          if ($stock_daily_info == null) {
            $stock_daily_info = StockDailyInfo::create([
              'stock_id' => $stock->id,
              'ind' => $stock->ind,
              'api_individual_buy_count' => $record['api_individual_buy_count'],
              'api_corporate_buy_count' => $record['api_corporate_buy_count'],
              'api_individual_sell_count' => $record['api_individual_sell_count'],
              'api_corporate_sell_count' => $record['api_corporate_sell_count'],
              'api_individual_buy_vol' => $record['api_individual_buy_vol'],
              'api_corporate_buy_vol' => $record['api_corporate_buy_vol'],
              'api_individual_sell_vol' => $record['api_individual_sell_vol'],
              'api_corporate_sell_vol' => $record['api_corporate_sell_vol'],
              'api_individual_buy_value' => $record['api_individual_buy_value'],
              'api_corporate_buy_value' => $record['api_corporate_buy_value'],
              'api_individual_sell_value' => $record['api_individual_sell_value'],
              'api_corporate_sell_value' => $record['api_corporate_sell_value'],
              'date' => $record['date'],
            ]);
          } else {
            $stock_daily_info->api_individual_buy_count = $record['api_individual_buy_count'];
            $stock_daily_info->api_corporate_buy_count = $record['api_corporate_buy_count'];
            $stock_daily_info->api_individual_sell_count = $record['api_individual_sell_count'];
            $stock_daily_info->api_corporate_sell_count = $record['api_corporate_sell_count'];
            $stock_daily_info->api_individual_buy_vol = $record['api_individual_buy_vol'];
            $stock_daily_info->api_corporate_buy_vol = $record['api_corporate_buy_vol'];
            $stock_daily_info->api_individual_sell_vol = $record['api_individual_sell_vol'];
            $stock_daily_info->api_corporate_sell_vol = $record['api_corporate_sell_vol'];
            $stock_daily_info->api_individual_buy_value = $record['api_individual_buy_value'];
            $stock_daily_info->api_corporate_buy_value = $record['api_corporate_buy_value'];
            $stock_daily_info->api_individual_sell_value = $record['api_individual_sell_value'];
            $stock_daily_info->api_corporate_sell_value = $record['api_corporate_sell_value'];
            $stock_daily_info->save();
          }
        }
      }catch (\Exception $e){
        Log::error('updateAllStocksDailyInfo -> client type history.error=' . $e->getMessage(). '\tstock_id=' . $stock->id);
      }
    }


  }










}