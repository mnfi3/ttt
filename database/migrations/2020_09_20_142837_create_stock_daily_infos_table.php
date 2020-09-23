<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStockDailyInfosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('stock_daily_infos', function (Blueprint $table) {
            $table->id();
            $table->float('stock_id');

            //gimate moamellat
            $table->float('first')->nullable();//avalin gimat
            $table->float('high')->nullable();//bishtarin gimat
            $table->float('low')->nullable();//kamtarin gimat
            $table->float('close')->nullable();//gimate payani
            $table->float('value')->nullable();//arzeshe moamelat
            $table->float('vol')->nullable();//hajme moamelat
            $table->float('openint')->nullable();//tedade moamelat
            $table->string('per')->nullable();//dore gozaresh(ruzane)
            $table->float('open')->nullable();//gimate diruz(gimate baz shodan)
            $table->float('last')->nullable();//akharin moamele
            $table->float('change_percent')->nullable();//darsade taghyirat

            //client type data
            $table->float('individual_buy_count')->nullable();//tedade kharide hagigi
            $table->float('corporate_buy_count')->nullable();//tedade kharide hogugi
            $table->float('individual_sell_count')->nullable();//tedade forushe hagigi
            $table->float('corporate_sell_count')->nullable();//tedade forushe hogugi
            $table->float('individual_buy_vol')->nullable();//hajme kharide hagigi
            $table->float('corporate_buy_vol')->nullable();//hajme kharide hogugi
            $table->float('individual_sell_vol')->nullable();//hajme forushe hagigi
            $table->float('corporate_sell_vol')->nullable();//hajme forushe hogugi
            $table->float('individual_buy_value')->nullable();//arzeshe kharide hagigi
            $table->float('corporate_buy_value')->nullable();//arzeshe kharie hogugi
            $table->float('individual_sell_value')->nullable();//arzeshe forushe hagigi
            $table->float('corporate_sell_value')->nullable();//arzeshe forushe hogugi

            //stock other data
            $table->float('stock_count')->nullable();//tedade sahm
            $table->float('base_volume')->nullable();//haje mabna
            $table->float('floating_stocks')->nullable();//sahame shenavar %
            $table->float('month_mean_volume')->nullable();//miyangine hajme mah
            $table->float('eps')->nullable();//eps
            $table->float('pe')->nullable();//p/e
            $table->float('group_pe')->nullable();//p/e guruh
            $table->string('status')->nullable();//vaziat

            $table->integer('date')->unsigned()->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->charset = 'utf8';
            $table->collation = 'utf8_general_ci';
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('stock_daily_infos');
    }
}
