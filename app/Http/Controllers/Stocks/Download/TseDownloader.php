<?php

namespace App\Http\Controllers\Stocks\Download;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Support\Facades\Log;
use Spatie\Browsershot\Browsershot;
use Maatwebsite\Excel\Facades\Excel;


class TseDownloader {

  private $client;
  private $url;

  public function __construct() {
    $this->client = new Client();
  }

  //get all stocks at now
  public function downloadAllStocksNow(){
    $this->url = Config::TSE_MARKET_WATCH_INIT;

    try {
      $str = $this->client->get($this->url)->getBody();
//      $str = substr($str, strpos($str, '@')+1);
//      $str = substr($str, strpos($str, '@')+1);
      $data = substr($str, strpos($str, 'IR')-16);
      $data = explode('@', $data);
      $str = $data[0];
      $recent_trades = $data[1];
      $str = explode(';', $str);


      $stocks = [];
      foreach ($str as $row) {

        $items = explode(',', $row);
        if (count($items)<10) continue;
        $i = 0;
        $stock = array();
        foreach ($items as $item) {
          $item = str_replace('ي','ی', $item);
          $item = str_replace('ك','ک', $item);
          switch ($i){
            case 0: $stock['ind'] = $item;break;
            case 1: $stock['code'] = $item;break;
            case 2: $stock['symbol'] = $item;break;
            case 3: $stock['name'] = $item;break;
            case 5: $stock['first'] = $item;break;
            case 6: $stock['close'] = $item;break;
            case 7: $stock['last'] = $item;break;
            case 8: $stock['openint'] = $item;break;
            case 9: $stock['vol'] = $item;break;
            case 10: $stock['value'] = $item;break;
            case 11: $stock['low'] = $item;break;
            case 12: $stock['high'] = $item;break;
            case 13: $stock['open'] = $item;break;
            case 14: $stock['eps'] = $item;break;
            case 15: $stock['base_volume'] = $item;break;
            case 18: $stock['group_code'] = $item;break;
            case 21: $stock['stock_count'] = $item;break;
            default:break;
          }

          $i++;
        }
        $stocks[] = $stock;

      }

      return $stocks;

    } catch (\Exception  $e) {
      Log::error('GET request failed.error=' . $e->getMessage(). '\turl=' . $this->url);
      return [];
    }
  }




  //get all stocks and recent trades at now
  public function downloadAllStocksAndRecentTradesNow(){

    $this->url = Config::TSE_MARKET_WATCH_INIT;

    try {
      $str = $this->client->get($this->url)->getBody();

      $data = substr($str, strpos($str, 'IR')-16);
      $data = explode('@', $data);
      $str = $data[0];
      $str = explode(';', $str);
      $recent_trades = $data[1];
      $recent_trades = explode(';', $recent_trades);


      $stocks = [];
      foreach ($str as $row) {

        $items = explode(',', $row);
        $i = 0;
        $stock = array();

        foreach ($items as $item) {
          $item = str_replace('ي','ی', $item);
          $item = str_replace('ك','ک', $item);


          switch ($i){
            case 0: $stock['ind'] = $item;break;
            case 1: $stock['code'] = $item;break;
            case 2: $stock['symbol'] = $item;break;
            case 3: $stock['name'] = $item;break;
            case 5: $stock['first'] = $item;break;
            case 6: $stock['close'] = $item;break;
            case 7: $stock['last'] = $item;break;
            case 8: $stock['openint'] = $item;break;
            case 9: $stock['vol'] = $item;break;
            case 10: $stock['value'] = $item;break;
            case 11: $stock['low'] = $item;break;
            case 12: $stock['high'] = $item;break;
            case 13: $stock['open'] = $item;break;
            case 14: (strlen($item) > 0) ? $stock['eps'] = $item : $stock['eps'] = 0 ;break;
            case 15: $stock['base_volume'] = $item;break;
            case 18: $stock['group_code'] = $item;break;
            case 21: $stock['stock_count'] = $item;break;
            default:break;
          }

          $i++;
        }
        $stocks[] = $stock;

      }




      $trades = [];
      foreach ($recent_trades as $trade){
        $items = explode(',', $trade);
        $trades[] = $items;
      }



      $i = 0;
      foreach ($stocks as $stock){
        $j = 0;
        foreach ($trades as $trade) {

          if ($trade[0] == $stock['ind']) {
            switch ($trade[1]) {
              case 1:
                $stock['sell_count1'] = (strlen($trade[2]) > 0) ? $trade[2] : 0;
                $stock['sell_vol1'] = (strlen($trade[7]) > 0) ? $trade[7] : 0;
                $stock['sell_price1'] = (strlen($trade[5]) > 0) ? $trade[5] : 0;
                $stock['buy_count1'] = (strlen($trade[3]) > 0) ? $trade[3] : 0;
                $stock['buy_vol1'] = (strlen($trade[6]) > 0) ? $trade[6] : 0;
                $stock['buy_price1'] = (strlen($trade[4]) > 0) ? $trade[4] : 0;
                break;

              case 2:
                $stock['sell_count2'] = (strlen($trade[2]) > 0) ? $trade[2] : 0;
                $stock['sell_vol2'] = (strlen($trade[7]) > 0) ? $trade[7] : 0;
                $stock['sell_price2'] = (strlen($trade[5]) > 0) ? $trade[5] : 0;
                $stock['buy_count2'] = (strlen($trade[3]) > 0) ? $trade[3] : 0;
                $stock['buy_vol2'] = (strlen($trade[6]) > 0) ? $trade[6] : 0;
                $stock['buy_price2'] = (strlen($trade[4]) > 0) ? $trade[4] : 0;
                break;

              case 3:
                $stock['sell_count3'] = (strlen($trade[2]) > 0) ? $trade[2] : 0;
                $stock['sell_vol3'] = (strlen($trade[7]) > 0) ? $trade[7] : 0;
                $stock['sell_price3'] = (strlen($trade[5]) > 0) ? $trade[5] : 0;
                $stock['buy_count3'] = (strlen($trade[3]) > 0) ? $trade[3] : 0;
                $stock['buy_vol3'] = (strlen($trade[6]) > 0) ? $trade[6] : 0;
                $stock['buy_price3'] = (strlen($trade[4]) > 0) ? $trade[4] : 0;
                break;

              default:
                break;

            }

            unset($trades[$j]);

            $stocks[$i] = $stock;
          }


          $j++;
        }

        $i++;
      }




      return $stocks;

    } catch (\Exception  $e) {
      Log::error('GET request failed.error=' . $e->getMessage(). '\turl=' . $this->url);
      return [];
    }
  }


  //get stock trades history (prices)
  public function downloadStockHistory($ind){
    $this->url = Config::TSE_TICKER_EXPORT_DATA_ADDRESS.$ind;

    try {
      $str = $this->client->get($this->url)->getBody();

      $lines = explode(PHP_EOL, $str);
      $array = array();
      foreach ($lines as $line) {
        $array[] = str_getcsv($line);
      }

      unset($array[0]);

      $prices = array();
      foreach ($array as $line) {
        $i=0;
        $price = array();
        foreach ($line as $item) {
          switch ($i){
            case 1 : $price['date'] = $item;break;
            case 2 : $price['first'] = $item;break;
            case 3 : $price['high'] = $item;break;
            case 4 : $price['low'] = $item;break;
            case 5 : $price['close'] = $item;break;
            case 6 : $price['value'] = $item;break;
            case 7 : $price['vol'] = $item;break;
            case 8 : $price['openint'] = $item;break;
            case 9 : $price['per'] = $item;break;
            case 10: $price['open'] = $item;break;
            case 11: $price['last'] = $item;break;
            default: break;
          }
          $i++;
        }
        $prices[] = $price;
      }

      return $prices;

    } catch (\Exception  $e) {
      Log::error('GET request failed.error=' . $e->getMessage(). '\turl=' . $this->url);
      return [];
    }
  }





  //get client types of all stocks at now
  public function downloadAllClientTypesNow(){
    $this->url = Config::TSE_CLIENT_TYPE_ALL;

    try {
      $str = $this->client->get($this->url)->getBody();

      $lines = explode(';', $str);
      $records = array();
      foreach ($lines as $line){
        $items = explode(',', $line);
        $i=0;
        $record = array();
        foreach ($items as $item){
          $i++;
          switch ($i){
            case 1: $record['ind'] = $item;break;
            case 2: $record['individual_buy_count'] = $item;break;
            case 3: $record['corporate_buy_count'] = $item;break;
            case 4: $record['individual_buy_vol'] = $item;break;
            case 5: $record['corporate_buy_vol'] = $item;break;
            case 6: $record['individual_sell_count'] = $item;break;
            case 7: $record['corporate_sell_count'] = $item;break;
            case 8: $record['individual_sell_vol'] = $item;break;
            case 9: $record['corporate_sell_vol'] = $item;break;
            default: break;
          }
        }
        $records[] = $record;
      }

      return $records;

    } catch (\Exception  $e) {
      Log::error('GET request failed.error=' . $e->getMessage(). '\turl=' . $this->url);
      return [];
    }
  }



  //get client type of one stock for all dates
  public function downloadStockClientTypeHistory($ind){
    $this->url = Config::TSE_CLIENT_TYPE_DATA_URL.$ind;

    try {
      $str = $this->client->get($this->url)->getBody();

      $lines = explode(';', $str);
      $records = array();
      foreach ($lines as $line){
        $items = explode(',', $line);
        $i=0;
        $record = array();
        foreach ($items as $item){
          $i++;
          switch ($i){
            case 1: $record['date'] = $item;break;
            case 2: $record['api_individual_buy_count'] = $item;break;
            case 3: $record['api_corporate_buy_count'] = $item;break;
            case 4: $record['api_individual_sell_count'] = $item;break;
            case 5: $record['api_corporate_sell_count'] = $item;break;
            case 6: $record['api_individual_buy_vol'] = $item;break;
            case 7: $record['api_corporate_buy_vol'] = $item;break;
            case 8: $record['api_individual_sell_vol'] = $item;break;
            case 9: $record['api_corporate_sell_vol'] = $item;break;
            case 10: $record['api_individual_buy_value'] = $item;break;
            case 11: $record['api_corporate_buy_value'] = $item;break;
            case 12: $record['api_individual_sell_value'] = $item;break;
            case 13: $record['api_corporate_sell_value'] = $item;break;
            default: break;
          }
        }
        $records[] = $record;
      }

      return $records;

    } catch (\Exception  $e) {
      Log::error('GET request failed.error=' . $e->getMessage(). '\turl=' . $this->url);
      return [];
    }
  }



  //get data from stock page (scrap from page)
  public function downloadStockOtherDataNow($ind){
    $this->url = Config::TSE_STOCK_ADDRESS.$ind;
    try {
      $str = $this->client->get($this->url)->getBody();
//      return $str;

      try {
        //find group_name;
        $array = array();
        preg_match("/LSecVal='(.*)'/", $str, $array);
        $value = explode(',', $array[0])[0];
        preg_match("/LSecVal='(.*)'/", $value, $array);
        $group_name = $array[1];
      }catch (\Exception $e){
        $group_name = '';
      }


      try {
        //find group pe
        $array = array();
        preg_match("/SectorPE='(.*)'/", $str, $array);
        $value = explode(',', $array[0])[0];
        preg_match("/SectorPE='(.*)'/", $value, $array);
        $group_pe = $array[1];
      }catch (\Exception $e){
        $group_pe = 0;
      }


      try {
        //eps
        $array = array();
        preg_match("/EstimatedEPS='(.*)'/", $str, $array);
        $value = explode(',', $array[0])[0];
        preg_match("/EstimatedEPS='(.*)'/", $value, $array);
        $eps = $array[1];
      }catch (\Exception $e){
        $eps = 0;
      }

      try {
        //title
        $array = array();
        preg_match("/Title='(.*)'/", $str, $array);
        $value = explode(',', $array[0])[0];
        preg_match("/Title='(.*)'/", $value, $array);
        $title = $array[1];
      }catch (\Exception $e){
        $title = '';
      }

      //find market type from title
      try{
        $array = explode('-', $title);
        $market_type = $array[count($array)-1];
      }catch (\Exception $e){
        $market_type = '';
      }



      try {
        //base vol
        $array = array();
        preg_match("/BaseVol=(.*)/", $str, $array);
        $value = explode(',', $array[0])[0];
        preg_match("/BaseVol=(.*)/", $value, $array);
        $base_volume = $array[1];
      }catch (\Exception $e){
        $base_volume = 0;
      }

      try {
        //stock count
        $array = array();
        preg_match("/ZTitad=(.*)/", $str, $array);
        $value = explode(',', $array[0])[0];
        preg_match("/ZTitad=(.*)/", $value, $array);
        $stock_count = $array[1];
      }catch (\Exception $e){
        $stock_count = 0;
      }

      try {
        //floating stocks
        $array = array();
        preg_match("/KAjCapValCpsIdx='(.*)'/", $str, $array);
        $value = explode(',', $array[0])[0];
        preg_match("/KAjCapValCpsIdx='(.*)'/", $value, $array);
        $floating_stocks = $array[1];
      }catch (\Exception $e){
        $floating_stocks = 0;
      }


      try {
        //month mean volume
        $array = array();
        preg_match("/QTotTran5JAvg='(.*)'/", $str, $array);
        $value = explode(',', $array[0])[0];
        preg_match("/QTotTran5JAvg='(.*)'/", $value, $array);
        $month_mean_volume = $array[1];
      }catch (\Exception $e){
        $month_mean_volume = 0;
      }

      (strlen($stock_count) == 0)? $stock_count = 0 : $stock_count = $stock_count;
      (strlen($base_volume) == 0)? $base_volume = 0 : $base_volume = $base_volume;
      (strlen($floating_stocks) == 0)? $floating_stocks = 0 : $floating_stocks = $floating_stocks;
      (strlen($month_mean_volume) == 0)? $month_mean_volume = 0 : $month_mean_volume = $month_mean_volume;
      (strlen($eps) == 0)? $eps = 0 : $eps = $eps;
      (strlen($group_pe) == 0)? $group_pe = 0 : $group_pe = $group_pe;
      (strlen($group_name) == 0)? $group_name = '' : $group_name = $group_name;

      $data = array();
      $data['stock_count'] = $stock_count;
      $data['base_volume'] = $base_volume;
      $data['floating_stocks'] = $floating_stocks;
      $data['month_mean_volume'] = $month_mean_volume;
      $data['eps'] = $eps;
      $data['title'] = $title;
      $data['market_type'] = $market_type;
      $data['group_pe'] = $group_pe;
      $data['group_name'] = $group_name;

      $data2 = array();
      foreach ($data as $key=>$value){
        $value = str_replace('ي','ی', $value);
        $value = str_replace('ك','ک', $value);
        $data2[$key] = $value;
      }

      return $data2;

    } catch (\Exception  $e) {
      Log::error('GET request failed.error=' . $e->getMessage(). '\turl=' . $this->url);
      return [];
    }
  }




  //get all price data
  public function downloadStocksPricesNow(){
    $this->url = Config::TSE_STOCK_PRICE_DATA;


    try {
      $str = $this->client->get($this->url)->getBody();
      $str = substr($str, strpos($str, 'IR')-16);
      $lines = explode(';', $str);
      $records = array();

      foreach ($lines as $line){
        $items = explode(',', $line);
        $i=0;
        $record = array();

        if (count($items) < 21) continue;
        foreach ($items as $item){
          $item = str_replace('ي','ی', $item);
          $item = str_replace('ك','ک', $item);
          switch ($i){
            case 0: $record['ind'] = $item;break;
            case 1: $record['code'] = $item;break;
            case 2: $record['symbol'] = $item;break;
            case 3: $record['name'] = $item;break;
//            case 4: $record['4'] = $item;break;
            case 5: $record['first'] = $item;break;
            case 6: $record['close'] = $item;break;
            case 7: $record['last'] = $item;break;
            case 8: $record['openint'] = $item;break;
            case 9: $record['vol'] = $item;break;
            case 10: $record['value'] = $item;break;
            case 11: $record['low'] = $item;break;
            case 12: $record['high'] = $item;break;
            case 13: $record['open'] = $item;break;
            case 14: $record['eps'] = $item;break;
            case 15: $record['base_volume'] = $item;break;
//            case 16: $record['16'] = $item;break;
//            case 17: $record['17'] = $item;break;
//            case 18: $record['18'] = $item;break;
//            case 19: $record['19'] = $item;break;
//            case 20: $record['20'] = $item;break;
            case 21: $record['stock_count'] = $item;break;
//            case 22: $record['22'] = $item;break;
            default: break;
          }

          $i++;

        }

        if(!array_key_exists('eps', $record)) {
          print_r($items);
        }

        (strlen($record['eps']) == 0)? $record['eps'] = 0 : $record['eps'] = $record['eps'];
        (strlen($record['base_volume']) == 0)? $record['base_volume'] = 0 : $record['base_volume'] = $record['base_volume'];
        (strlen($record['stock_count']) == 0)? $record['stock_count'] = 0 : $record['stock_count'] = $record['stock_count'];

        $records[] = $record;
      }

      return $records;

    } catch (\Exception  $e) {
      Log::error('GET request failed.error=' . $e->getMessage(). '\turl=' . $this->url);
      return [];
    }
  }






  //=================================from file===========================================

  //get stock trades history (prices) from file
  public function getStockHistoryFromFile($ind){

    try {

      if (!file_exists(public_path('/tickers_data/'.$ind. '.csv'))) return [];


      $str = file_get_contents('tickers_data/'.$ind. '.csv');

      $lines = explode(PHP_EOL, $str);

      $array = array();
      foreach ($lines as $line) {
        $array[] = str_getcsv($line);
      }

      unset($array[0]);

      $prices = array();
      foreach ($array as $line) {
        $i=0;
        $price = array();
        foreach ($line as $item) {
          switch ($i){
            case 2: $price['date'] = $item;break;
            case 3 : $price['first'] = $item;break;
            case 4 : $price['high'] = $item;break;
            case 5 : $price['low'] = $item;break;
            case 6 : $price['close'] = $item;break;
            case 7 : $price['value'] = $item;break;
            case 8 : $price['vol'] = $item;break;
            case 9 : $price['openint'] = $item;break;
            case 10 : $price['per'] = $item;break;
            case 11: $price['open'] = $item;break;
            case 12: $price['last'] = $item;break;
            default: break;
          }
          $i++;
        }
        $prices[] = $price;
      }


      return $prices;

    } catch (RequestException  $e) {
      Log::error('GET request failed.error=' . $e->getMessage(). '\turl=' . $this->url);
      return [];
    }
  }


}