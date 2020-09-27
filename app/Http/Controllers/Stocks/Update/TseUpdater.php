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

class TseUpdater {

  public function __construct() {
  }


  //can run every time
  public function updateAllStocksInfo(){
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
          $smt = StockMarketType::where('name', 'like', '%' . $info['market_type'] . '%')->first();
          if ($sg == null) {
            $sg = StockGroup::create([
              'code' => '',
              'name' => $info['group_name'],
            ]);
          }

          if ($smt == null) {
            $smt = StockMarketType::create([
              'name' => $info['market_type'],
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



  //update all stock daily info from_date (can run daily)
  public function updateAllStocksDailyInfo($options = []){
    $options = json_decode(json_encode($options), true);
    (!array_key_exists('from_date', $options))? $options['from_date'] = 0: $options['from_date'] = $options['from_date'];
    (!array_key_exists('to_date', $options))? $options['to_date'] = 999999999999: $options['to_date'] = $options['to_date'];
    (!array_key_exists('from_id', $options ))? $options['from_id'] = 0: $options['from_id'] = $options['from_id'];
    (!array_key_exists('to_id', $options ))? $options['to_id'] = 999999999999: $options['to_id'] = $options['to_id'];



    //options => ['from_date', 'to_date', 'from_id', 'to_id']
    $downloader = new TseDownloader();
    $stocks = Stock::where('id', '>', $options['from_id'])->where('id', '<', $options['to_id'])->get();

    foreach ($stocks as $stock) {

      //download price history------------------------------------------
      try {

        $prices = $downloader->downloadStockHistory($stock->ind);
        (count($prices) == 0) ? $prices = $downloader->downloadStockHistory($stock->ind) : $prices = $prices;
        (count($prices) == 0) ? $prices = $downloader->downloadStockHistory($stock->ind) : $prices = $prices;
        (count($prices) == 0) ? $prices = $downloader->downloadStockHistory($stock->ind) : $prices = $prices;

        foreach ($prices as $price) {
          if (!array_key_exists('date', $price)) continue;
          if ($price['date'] < $options['from_date'] || $price['date'] > $options['to_date']) continue;

          $stock_daily_info = StockDailyInfo::where('stock_id', '=', $stock->id)->where('date', '=', $price['date'])->first();
          if ($stock_daily_info == null) {
            $stock_daily_info = StockDailyInfo::create([
              'stock_id' => $stock->id,
              'first' => $price['first'],
              'high' => $price['high'],
              'low' => $price['low'],
              'close' => $price['close'],
              'value' => $price['value'],
              'vol' => $price['vol'],
              'openint' => $price['openint'],
              'per' => $price['per'],
              'open' => $price['open'],
              'last' => $price['last'],
              'change_percent' => (($price['close'] - $price['open']) / $price['open']) * 100,
              'date' => $price['date'],
            ]);
          } else {
            $stock_daily_info->first = $price['first'];
            $stock_daily_info->high = $price['high'];
            $stock_daily_info->low = $price['low'];
            $stock_daily_info->close = $price['close'];
            $stock_daily_info->value = $price['value'];
            $stock_daily_info->vol = $price['vol'];
            $stock_daily_info->openint = $price['openint'];
            $stock_daily_info->per = $price['per'];
            $stock_daily_info->open = $price['open'];
            $stock_daily_info->last = $price['last'];
            $stock_daily_info->change_percent = (($price['close'] - $price['open']) / $price['open']) * 100;
            $stock_daily_info->save();
          }

        }

        $setting = Setting::get(Setting::KEY_STOCK_HISTORY_PRICE_UPDATE_LAST_ID);
        $setting->value = $stock->id;
        $setting->save();
      }catch (\Exception $e){
        Log::error('updateAllStocksDailyInfo -> price history.error=' . $e->getMessage(). '\tstock_id=' . $stock->id);
      }




      //download client types history----------------------------------------------------

      try {
        $records = $downloader->downloadStockClientTypeHistory($stock->ind);
        (count($records) == 0) ? $records = $downloader->downloadStockClientTypeHistory($stock->ind) : $records = $records;
        (count($records) == 0) ? $records = $downloader->downloadStockClientTypeHistory($stock->ind) : $records = $records;
        (count($records) == 0) ? $records = $downloader->downloadStockClientTypeHistory($stock->ind) : $records = $records;

        foreach ($records as $record) {
          if ($record['date'] < $options['from_date'] || $record['date'] > $options['to_date']) continue;
          $stock_daily_info = StockDailyInfo::where('stock_id', '=', $stock->id)->where('date', '=', $record['date'])->first();
          if ($stock_daily_info == null) {
            $stock_daily_info = StockDailyInfo::create([
              'stock_id' => $stock->id,
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

      $setting = Setting::get(Setting::KEY_STOCK_HISTORY_CLIENT_TYPE_UPDATE_LAST_ID);
      $setting->value = $stock->id;
      $setting->save();

    }

  }



  //update all stock daily info today (can run daily)
  public function updateTodayAllStocksDailyInfoFromHistory(){

    $date = Util::getTradeDate();


    //options => ['from_date', 'to_date', 'from_id', 'to_id']
    $downloader = new TseDownloader();
    $stocks = Stock::all();

    foreach ($stocks as $stock) {

      //download price history------------------------------------------
      try {

        $prices = $downloader->downloadStockHistory($stock->ind);
        (count($prices) == 0) ? $prices = $downloader->downloadStockHistory($stock->ind) : $prices = $prices;
        (count($prices) == 0) ? $prices = $downloader->downloadStockHistory($stock->ind) : $prices = $prices;
        (count($prices) == 0) ? $prices = $downloader->downloadStockHistory($stock->ind) : $prices = $prices;

        foreach ($prices as $price) {
          if (!array_key_exists('date', $price)) continue;
          if ($price['date'] != $date) continue;

          $stock_daily_info = StockDailyInfo::where('stock_id', '=', $stock->id)->where('date', '=', $price['date'])->first();
          if ($stock_daily_info == null) {
            $stock_daily_info = StockDailyInfo::create([
              'stock_id' => $stock->id,
              'first' => $price['first'],
              'high' => $price['high'],
              'low' => $price['low'],
              'close' => $price['close'],
              'value' => $price['value'],
              'vol' => $price['vol'],
              'openint' => $price['openint'],
              'per' => $price['per'],
              'open' => $price['open'],
              'last' => $price['last'],
              'change_percent' => (($price['close'] - $price['open']) / $price['open']) * 100,
              'date' => $price['date'],
            ]);
          } else {
            $stock_daily_info->first = $price['first'];
            $stock_daily_info->high = $price['high'];
            $stock_daily_info->low = $price['low'];
            $stock_daily_info->close = $price['close'];
            $stock_daily_info->value = $price['value'];
            $stock_daily_info->vol = $price['vol'];
            $stock_daily_info->openint = $price['openint'];
            $stock_daily_info->per = $price['per'];
            $stock_daily_info->open = $price['open'];
            $stock_daily_info->last = $price['last'];
            $stock_daily_info->change_percent = (($price['close'] - $price['open']) / $price['open']) * 100;
            $stock_daily_info->save();
          }

        }

        $setting = Setting::get(Setting::KEY_STOCK_HISTORY_PRICE_UPDATE_LAST_ID);
        $setting->value = $stock->id;
        $setting->save();
      }catch (\Exception $e){
        Log::error('updateTodayAllStocksDailyInfo -> price history.error=' . $e->getMessage(). '\tstock_id=' . $stock->id);
      }




      //download client types history----------------------------------------------------

      try {
        $records = $downloader->downloadStockClientTypeHistory($stock->ind);
        (count($records) == 0) ? $records = $downloader->downloadStockClientTypeHistory($stock->ind) : $records = $records;
        (count($records) == 0) ? $records = $downloader->downloadStockClientTypeHistory($stock->ind) : $records = $records;
        (count($records) == 0) ? $records = $downloader->downloadStockClientTypeHistory($stock->ind) : $records = $records;

        foreach ($records as $record) {
          if ($date != $record['date'] ) continue;
          $stock_daily_info = StockDailyInfo::where('stock_id', '=', $stock->id)->where('date', '=', $record['date'])->first();
          if ($stock_daily_info == null) {
            $stock_daily_info = StockDailyInfo::create([
              'stock_id' => $stock->id,
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
        Log::error('updateTodayAllStocksDailyInfo -> client type history.error=' . $e->getMessage(). '\tstock_id=' . $stock->id);
      }

      $setting = Setting::get(Setting::KEY_STOCK_HISTORY_CLIENT_TYPE_UPDATE_LAST_ID);
      $setting->value = $stock->id;
      $setting->save();

    }

  }



  //can run every time
  public function updateTodayAllStocksClientTypes(){
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


  //update all stock today prices (can run every time)
  public function updateTodayAllStocksDailyInfo(){
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
        $stock_daily_info->base_volume = $price['base_volume'];
        $stock_daily_info->stock_count = $price['stock_count'];
        $stock_daily_info->save();
      }

      $setting = Setting::get(Setting::KEY_STOCK_HISTORY_PRICE_UPDATE_LAST_ID);
      $setting->value = $stock->id;
      $setting->save();

    }



  }







  ///==============================from file======================================================
  //update all stock daily info from_date (can run daily)
  public function updateAllStocksDailyInfoFromFile($options = []){
    $options = json_decode(json_encode($options), true);
    (!array_key_exists('from_date', $options))? $options['from_date'] = 0: $options['from_date'] = $options['from_date'];
    (!array_key_exists('to_date', $options))? $options['to_date'] = 999999999999: $options['to_date'] = $options['to_date'];
    (!array_key_exists('from_id', $options ))? $options['from_id'] = 0: $options['from_id'] = $options['from_id'];
    (!array_key_exists('to_id', $options ))? $options['to_id'] = 999999999999: $options['to_id'] = $options['to_id'];



    //options => ['from_date', 'to_date', 'from_id', 'to_id']
    $downloader = new TseDownloader();
    $stocks = Stock::where('id', '>', $options['from_id'])->where('id', '<', $options['to_id'])->get();

    foreach ($stocks as $stock) {

      //download price history------------------------------------------
      try {

        $prices = $downloader->getStockHistoryFromFile($stock->ind);

        foreach ($prices as $price) {
          if (!array_key_exists('date', $price)) continue;
          if ($price['date'] < $options['from_date'] || $price['date'] > $options['to_date']) continue;

          $stock_daily_info = StockDailyInfo::where('stock_id', '=', $stock->id)->where('date', '=', $price['date'])->first();
          if ($stock_daily_info == null) {
            $stock_daily_info = StockDailyInfo::create([
              'stock_id' => $stock->id,
              'first' => $price['first'],
              'high' => $price['high'],
              'low' => $price['low'],
              'close' => $price['close'],
              'value' => $price['value'],
              'vol' => $price['vol'],
              'openint' => $price['openint'],
              'per' => $price['per'],
              'open' => $price['open'],
              'last' => $price['last'],
              'change_percent' => (($price['close'] - $price['open']) / $price['open']) * 100,
              'date' => $price['date'],
            ]);
          } else {
            $stock_daily_info->first = $price['first'];
            $stock_daily_info->high = $price['high'];
            $stock_daily_info->low = $price['low'];
            $stock_daily_info->close = $price['close'];
            $stock_daily_info->value = $price['value'];
            $stock_daily_info->vol = $price['vol'];
            $stock_daily_info->openint = $price['openint'];
            $stock_daily_info->per = $price['per'];
            $stock_daily_info->open = $price['open'];
            $stock_daily_info->last = $price['last'];
            $stock_daily_info->change_percent = (($price['close'] - $price['open']) / $price['open']) * 100;
            $stock_daily_info->save();
          }

        }

        $setting = Setting::get(Setting::KEY_STOCK_HISTORY_PRICE_UPDATE_LAST_ID);
        $setting->value = $stock->id;
        $setting->save();
      }catch (\Exception $e){
        Log::error('updateAllStocksDailyInfoFromFile -> price history.error=' . $e->getMessage(). '\tstock_id=' . $stock->id);
      }




      //download client types history----------------------------------------------------

      try {
        $records = $downloader->downloadStockClientTypeHistory($stock->ind);
        (count($records) == 0) ? $records = $downloader->downloadStockClientTypeHistory($stock->ind) : $records = $records;
        (count($records) == 0) ? $records = $downloader->downloadStockClientTypeHistory($stock->ind) : $records = $records;
        (count($records) == 0) ? $records = $downloader->downloadStockClientTypeHistory($stock->ind) : $records = $records;

        foreach ($records as $record) {
          if ($record['date'] < $options['from_date'] || $record['date'] > $options['to_date']) continue;
          $stock_daily_info = StockDailyInfo::where('stock_id', '=', $stock->id)->where('date', '=', $record['date'])->first();
          if ($stock_daily_info == null) {
            $stock_daily_info = StockDailyInfo::create([
              'stock_id' => $stock->id,
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

      $setting = Setting::get(Setting::KEY_STOCK_HISTORY_CLIENT_TYPE_UPDATE_LAST_ID);
      $setting->value = $stock->id;
      $setting->save();

    }

  }


}