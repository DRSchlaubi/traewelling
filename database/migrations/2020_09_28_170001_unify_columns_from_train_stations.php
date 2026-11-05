<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class UnifyColumnsFromTrainStations extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (DB::getDriverName() === 'pgsql') {
            DB::statement('ALTER TABLE train_stations ALTER COLUMN ibnr TYPE BIGINT USING ibnr::BIGINT');
        } else {
            Schema::table('train_stations', function (Blueprint $table) {
                $table->bigInteger('ibnr')->unsigned()->change();
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (DB::getDriverName() === 'pgsql') {
            DB::statement('ALTER TABLE train_stations ALTER COLUMN ibnr TYPE VARCHAR(255) USING ibnr::VARCHAR');
        } else {
            Schema::table('train_stations', function (Blueprint $table) {
                $table->string('ibnr', 255)->change();
            });
        }
    }
}
