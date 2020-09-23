<?php


namespace App\Http\Controllers\Stocks\Download;


class Config {

  const TSE_MARKET_WATCH_INIT = "http://www.tsetmc.com/tsev2/data/MarketWatchInit.aspx?h=0&r=0";//tamami sahmhaye bazar lahze i
  const TSE_CLIENT_TYPE_ALL = "http://www.tsetmc.com/tsev2/data/ClientTypeAll.aspx";//tamami sahm ha hagigi hogugi lahze i
  const TSE_TICKER_EXPORT_DATA_ADDRESS = "http://tsetmc.com/tsev2/data/Export-txt.aspx?t=i&a=1&b=0&i=";//gozareshe gimate yek sahm
  const TSE_CLIENT_TYPE_DATA_URL = "http://www.tsetmc.com/tsev2/data/clienttype.aspx?i=";//hagigi hogugiye yek sahm
  const TSE_STOCK_ADDRESS = "http://tsetmc.com/Loader.aspx?ParTree=151311&i=";//safhe yek sahm
  const TSE_STOCK_EXPORT_PRICE_DATA = "http://members.tsetmc.com/tsev2/excel/MarketWatchPlus.aspx?d=0&format=0";//eteleate lahze i dideban (excel)
  const TSE_STOCK_PRICE_DATA = "http://www.tsetmc.com/tsev2/data/MarketWatchInit.aspx?h=0&r=0";//eteleate lahze i dideban





  const TSE_STOCK_INFO_URL = "http://www.tsetmc.com/tsev2/data/instinfofast.aspx?i={}&c=57+";//etelaate lahze i yek sahm
  const TSE_SYMBOL_SEARCH_URL = "http://www.tsetmc.com/tsev2/data/search.aspx?skey={}";//jostojuye sahm


}