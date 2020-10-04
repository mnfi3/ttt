<?php

namespace App\Console\Commands;

use App\Http\Controllers\Stocks\Update\TseDailyUpdater;
use App\Http\Controllers\Stocks\Update\TseLocalHandler;
use App\Http\Controllers\Util\Util;
use Illuminate\Console\Command;

class TseUpdateStocksAtMorning extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tse:update-stocks-at-morning';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'update all stocks info every mornings';

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

        $handler->handleLastDayActiveStocks();
        $handler->handleLastDayInstantInfo();

        $updater->updateStocksInfoBeforeMarket();
        $handler->handleArabicCharacters();
        $handler->handlePriority();
        $handler->handleSellOption();
        $handler->handleBuyOption();

      }

        return 0;
    }
}
