<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSomeFieldsToStockInstantInfosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('stock_instant_infos', function (Blueprint $table) {
            $table->double('eps')->nullable()->after('buy_price3');
            $table->double('pe')->nullable()->after('eps');
            $table->double('base_volume')->nullable()->after('pe');
            $table->double('stock_count')->nullable()->after('base_volume');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('stock_instant_infos', function (Blueprint $table) {
            //
        });
    }
}
