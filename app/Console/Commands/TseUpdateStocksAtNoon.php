<?php

namespace App\Console\Commands;

use App\Http\Controllers\Stocks\Update\TseDailyUpdater;
use App\Http\Controllers\Stocks\Update\TseLocalHandler;
use App\Http\Controllers\Util\Util;
use Illuminate\Console\Command;

class TseUpdateStocksAtNoon extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tse:update-stocks-at-noon';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'update stocks info after market';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {

      if (Util::getDayOfWeek() != 4 && Util::getDayOfWeek() != 5){
        $updater = new TseDailyUpdater();
        $handler = new TseLocalHandler();

        $updater->updateStocksInfoAfterMarket();
        $updater->updateStocksPricesAfterMarket();
        $updater->updateStocksClientTypesAfterMarket();
      }
        return 0;
    }
}
