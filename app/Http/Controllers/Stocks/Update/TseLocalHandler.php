<?php


namespace App\Http\Controllers\Stocks\Update;


use App\Http\Controllers\Util\PDate;
use App\Stock;
use App\StockGroup;
use App\StockMarketType;
use Illuminate\Support\Facades\DB;

class TseLocalHandler {

  public function __construct() { }




  public function handleArabicCharacters(){
    $stocks = Stock::all();
    foreach ($stocks as $stock){
      $stock->symbol = str_replace('ي','ی', $stock->symbol);
      $stock->symbol = str_replace('ك','ک', $stock->symbol);
      $stock->name = str_replace('ي','ی', $stock->name);
      $stock->name = str_replace('ك','ک', $stock->name);
      $stock->save();
    }

    $stock_markets = StockMarketType::all();
    foreach ($stock_markets as $stock){
      $stock->name = str_replace('ي','ی', $stock->name);
      $stock->name = str_replace('ك','ک', $stock->name);
      $stock->save();
    }

    $stock_groups = StockGroup::all();
    foreach ($stock_groups as $stock){
      $stock->name = str_replace('ي','ی', $stock->name);
      $stock->name = str_replace('ك','ک', $stock->name);
      $stock->save();
    }
  }


  //===================handle stocks types==========================

  public function handlePriority(){
    $stocks = Stock::where('stock_type_id', '=', null)->where('symbol', 'like', '%'.'ح')->where('name', 'like', 'ح'.'%')->get();

    foreach ($stocks as $stock) {
      $symbol = $stock->symbol;
      $symbol = substr($symbol, 0, -2);

      $s = Stock::where('symbol', '=', $symbol)->first();
      if ($s != null){
        $stock->parent_id = $s->id;
        $stock->stock_type_id = 2;// 2 hage tagadom
        $stock->save();
      }
    }
  }


  public function handleBuyOption(){
    $stocks = Stock::where('stock_type_id', '=', null)->where('name', 'like', '%'.'اختیارخ'.'%')->get();
    foreach ($stocks as $stock){
      try {
        $arr = explode('-', $stock->name);
        $symbol = explode(' ', $arr[0])[1];

        $s = Stock::where('symbol', '=', $symbol)->first();
        if ($s != null){
          $pdate = new PDate();
          $apply_price = $arr[1];
          $apply_price = str_replace(' ', '', $apply_price);

          $apply_date = $pdate->toGregorian(str_replace(' ', '',$arr[2]));
          $apply_date = str_replace('-', '', $apply_date);

          $stock->parent_id = $s->id;
          $stock->stock_type_id = 3; //ekhtiyare kharid
          $stock->apply_date = $apply_date;
          $stock->apply_price = $apply_price;
          $stock->save();
        }

      }catch(\Exception $e){}
    }
  }


  public function handleSellOption(){
    $stocks = Stock::where('stock_type_id', '=', null)->where('name', 'like', '%'.'اختیارف'.'%')->get();
    foreach ($stocks as $stock){
      try {
        $arr = explode('-', $stock->name);
        $symbol = explode(' ', $arr[0])[1];

        $s = Stock::where('symbol', '=', $symbol)->first();
        if ($s != null){
          $pdate = new PDate();
          $apply_price = $arr[1];
          $apply_price = str_replace(' ', '', $apply_price);

          $apply_date = $pdate->toGregorian(str_replace(' ', '',$arr[2]));
          $apply_date = str_replace('-', '', $apply_date);

          $stock->parent_id = $s->id;
          $stock->stock_type_id = 4; //ekhtiyare forush
          $stock->apply_date = $apply_date;
          $stock->apply_price = $apply_price;
          $stock->save();
        }

      }catch(\Exception $e){}
    }
  }

  //===================handle stocks types==========================


  public function handleLastDayActiveStocks(){
    DB::update("update stocks set is_active = 0");
  }


  public function handleLastDayInstantInfo(){
    DB::table("stock_instant_infos")->truncate();
  }




}