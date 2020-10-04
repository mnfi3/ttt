<?php

namespace App\Console\Commands;

use App\Http\Controllers\Stocks\Update\TseInstantUpdater;
use App\Http\Controllers\Util\Util;
use Illuminate\Console\Command;

class TseUpdateStocksEveryMinutes extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tse:update-stocks-every-minutes';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'update instant data';

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

      date_default_timezone_set('Asia/Tehran');
      $hour = date('H');
      if (Util::getDayOfWeek() != 4 && Util::getDayOfWeek() != 5 && $hour > 9 && $hour < 13){
        $updater = new TseInstantUpdater();

        $updater->updateInstantAllStocksPricesAndClientTypes();
      }
        return 0;
    }
}
