<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSomeFielsToStockDailyInfosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('stock_daily_infos', function (Blueprint $table) {
          //api client type data
          $table->float('api_individual_buy_count')->nullable()->after('corporate_sell_value');//tedade kharide hagigi
          $table->float('api_corporate_buy_count')->nullable()->after('api_individual_buy_count');//tedade kharide hogugi
          $table->float('api_individual_sell_count')->nullable()->after('api_corporate_buy_count');//tedade forushe hagigi
          $table->float('api_corporate_sell_count')->nullable()->after('api_individual_sell_count');//tedade forushe hogugi
          $table->float('api_individual_buy_vol')->nullable()->after('api_corporate_sell_count');//hajme kharide hagigi
          $table->float('api_corporate_buy_vol')->nullable()->after('api_individual_buy_vol');;//hajme kharide hogugi
          $table->float('api_individual_sell_vol')->nullable()->after('api_corporate_buy_vol');;//hajme forushe hagigi
          $table->float('api_corporate_sell_vol')->nullable()->after('api_individual_sell_vol');;//hajme forushe hogugi
          $table->float('api_individual_buy_value')->nullable()->after('api_corporate_sell_vol');;//arzeshe kharide hagigi
          $table->float('api_corporate_buy_value')->nullable()->after('api_individual_buy_value');;//arzeshe kharie hogugi
          $table->float('api_individual_sell_value')->nullable()->after('api_corporate_buy_value');;//arzeshe forushe hagigi
          $table->float('api_corporate_sell_value')->nullable()->after('api_individual_sell_value');;//arzeshe forushe hogugi
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('stock_daily_infos', function (Blueprint $table) {
            //
        });
    }
}
