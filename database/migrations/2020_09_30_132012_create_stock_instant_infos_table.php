<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStockInstantInfosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('stock_instant_infos', function (Blueprint $table) {
          $table->id();
          $table->bigInteger('stock_id')->nullable();

          //gimate moamellat
          $table->double('first')->nullable();//avalin gimat
          $table->double('high')->nullable();//bishtarin gimat
          $table->double('low')->nullable();//kamtarin gimat
          $table->double('close')->nullable();//gimate payani
          $table->double('value')->nullable();//arzeshe moamelat
          $table->double('vol')->nullable();//hajme moamelat
          $table->double('openint')->nullable();//tedade moamelat
          $table->double('open')->nullable();//gimate diruz(gimate baz shodan)
          $table->double('last')->nullable();//akharin moamele
          $table->double('change_percent')->nullable();//darsade taghyirat

          //client type data
          $table->double('individual_buy_count')->nullable();//tedade kharide hagigi
          $table->double('corporate_buy_count')->nullable();//tedade kharide hogugi
          $table->double('individual_sell_count')->nullable();//tedade forushe hagigi
          $table->double('corporate_sell_count')->nullable();//tedade forushe hogugi
          $table->double('individual_buy_vol')->nullable();//hajme kharide hagigi
          $table->double('corporate_buy_vol')->nullable();//hajme kharide hogugi
          $table->double('individual_sell_vol')->nullable();//hajme forushe hagigi
          $table->double('corporate_sell_vol')->nullable();//hajme forushe hogugi
          $table->double('individual_buy_value')->nullable();//arzeshe kharide hagigi
          $table->double('corporate_buy_value')->nullable();//arzeshe kharie hogugi
          $table->double('individual_sell_value')->nullable();//arzeshe forushe hagigi
          $table->double('corporate_sell_value')->nullable();//arzeshe forushe hogugi

          //recent trades
          $table->double('sell_count1')->nullable();
          $table->double('sell_vol1')->nullable();
          $table->double('sell_price1')->nullable();
          $table->double('buy_count1')->nullable();
          $table->double('buy_vol1')->nullable();
          $table->double('buy_price1')->nullable();

          $table->double('sell_count2')->nullable();
          $table->double('sell_vol2')->nullable();
          $table->double('sell_price2')->nullable();
          $table->double('buy_count2')->nullable();
          $table->double('buy_vol2')->nullable();
          $table->double('buy_price2')->nullable();

          $table->double('sell_count3')->nullable();
          $table->double('sell_vol3')->nullable();
          $table->double('sell_price3')->nullable();
          $table->double('buy_count3')->nullable();
          $table->double('buy_vol3')->nullable();
          $table->double('buy_price3')->nullable();


          $table->bigInteger('time')->unsigned()->nullable();
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
        Schema::dropIfExists('stock_instant_infos');
    }
}
