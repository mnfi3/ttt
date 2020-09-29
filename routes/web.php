<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/


use Spatie\Browsershot\Browsershot;

Route::get('/', function () {



  $handler = new \App\Http\Controllers\Stocks\Update\TseLocalHandler();
  $handler->handleArabicCharacters();
  $result = $handler->handleBuyOption();
  $result = $handler->handleSellOption();




  die();





   $downloader = new \App\Http\Controllers\Stocks\Download\TseDownloader();
//  $result = $downloader->downloadStockOtherDataNow('31898914913027754');
//  print_r($result);
//  die();
//   $downloader->downloadStocksPricesNow();

   //$stocks = $downloader->downloadAllStocks();
   //print_r($stocks);


//  $items = $downloader->downloadStockHistory('14231831499205396');
//  print_r($items);


//  $str = $downloader->downloadAllClientTypes();
//  print_r( $str);

//  $str = $downloader->downloadStockClientType('35366681030756042');
//  print_r($str);

//
//  $data = $downloader->downloadStockOtherDataNow('14231831499205396');
//  return $data;
//  echo $data['market_type'];
//  print_r($data);




  $updater = new \App\Http\Controllers\Stocks\Update\TseUpdater();
//  $updater->updateAllStocksInfo();
//  $updater->updateAllStocksDailyInfo(['from_date'=>20200700]);
//  $updater->updateTodayAllStocksClientTypes();
//  $updater->updateTodayAllStocksDailyInfo();

});




Route::get('/file', function () {


//   $downloader = new \App\Http\Controllers\Stocks\Download\TseDownloader();
//   $arr = $downloader->getStockHistoryFromFile('778253364357513');
////   print_r($arr);
//   return;

  //$stocks = $downloader->downloadAllStocks();
  //print_r($stocks);


//  $items = $downloader->downloadStockHistory('14231831499205396');
//  print_r($items);


//  $str = $downloader->downloadAllClientTypes();
//  print_r( $str);

//  $str = $downloader->downloadStockClientType('35366681030756042');
//  print_r($str);

//
//  $data = $downloader->downloadStockOtherDataNow('14231831499205396');
//  print_r($data);



  $updater = new \App\Http\Controllers\Stocks\Update\TseUpdater();
//  $updater->updateAllStocksInfo();
  $updater->updateAllStocksDailyInfoFromFile(['from_date'=>20200700]);
//  $updater->updateTodayAllStocksClientTypes();

});




Route::get('/test', function () {


//   $downloader = new \App\Http\Controllers\Stocks\Download\TseDownloader();
//   $arr = $downloader->getStockHistoryFromFile('778253364357513');
////   print_r($arr);
//   return;

  //$stocks = $downloader->downloadAllStocks();
  //print_r($stocks);


//  $items = $downloader->downloadStockHistory('14231831499205396');
//  print_r($items);


//  $str = $downloader->downloadAllClientTypes();
//  print_r( $str);

//  $str = $downloader->downloadStockClientType('35366681030756042');
//  print_r($str);

////
//  $data = $downloader->downloadStockOtherDataNow('14231831499205396');
//  print_r($data);



//  $updater = new \App\Http\Controllers\Stocks\Update\TseUpdater();
//  $updater->updateAllStocksInfo();
//  $updater->updateAllStocksDailyInfoFromFile(['from_date'=>20200700]);
//  $updater->updateTodayAllStocksClientTypes();



  $stocks = \App\Stock::all();
  $result = array();
  foreach ($stocks as $stock){
    $infos = $stock->dailyInfos()->where('date', '>', \App\Http\Controllers\Util\Util::getTradeDate() - 9)->where('date', '<', \App\Http\Controllers\Util\Util::getTradeDate())->get();

    if (count($infos) == 0 ) continue;

    $sum_9 = 0;
    foreach ($infos as $info){
      if ($info->api_individual_sell_vol == null) continue;
      $sum_9+=$info->api_individual_sell_vol + $info->api_corporate_sell_vol;
    }
    $mean_9 = $sum_9/count($infos);

    $today = $stock->dailyInfos()->where('date', '=', \App\Http\Controllers\Util\Util::getTradeDate())->first();
    if ($today == null) continue;
    $today_vol = $today->individual_sell_vol + $today->corporate_sell_vol;

    if ($mean_9 == 0) continue;
    if ($today_vol / $mean_9 > 2.5){
      $result [] = $stock->symbol;
    }


  }



  print_r($result);




});
