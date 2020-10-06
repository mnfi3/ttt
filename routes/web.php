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


//  $updater = new \App\Http\Controllers\Stocks\Update\TseUpdater();
//  $updater->updateAllStocksInfo();
////  $updater->updateAllStocksDailyInfo(['from_date'=>20200700]);
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



//  $updater = new \App\Http\Controllers\Stocks\Update\TseUpdater();
//  $updater->updateAllStocksInfo();
//  $updater->updateAllStocksDailyInfoFromFile(['from_date'=>20200700]);
//  $updater->updateTodayAllStocksDailyInfo();
//  $updater->updateTodayAllStocksClientTypes();

});




Route::get('/test', function () {



//  $stocks = \Illuminate\Support\Facades\DB::select("select symbol,ind from stocks");
//
//
//  $array = [];
//  foreach ($stocks as $stock){
//    $array[$stock->symbol] = $stock->ind;
//  }
//
////  $array = json_encode($array, JSON_UNESCAPED_UNICODE);
//
//  file_put_contents('2_names.json',json_encode($array, JSON_UNESCAPED_UNICODE));
//  header('Content-Type: application/json');
//  echo json_encode($array, JSON_UNESCAPED_UNICODE);
//
//  return;
//
//  $handler = new \App\Http\Controllers\Stocks\Update\TseLocalHandler();
//  $handler->handleArabicCharacters();
//
//
//  die();
//
//
//$updater = new \App\Http\Controllers\Stocks\Update\TseInstantUpdater();
//$updater->updateInstantAllStocksPricesAndClientTypes();
//
//echo time() . "<br>";



});
