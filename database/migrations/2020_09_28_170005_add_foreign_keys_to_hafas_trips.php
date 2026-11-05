<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AddForeignKeysToHafasTrips extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('hafas_trips', function (Blueprint $table) {
            $table->unique('trip_id');
        });

        if (DB::getDriverName() === 'pgsql') {
            DB::statement('ALTER TABLE hafas_trips ALTER COLUMN origin TYPE BIGINT USING origin::BIGINT');
            DB::statement('ALTER TABLE hafas_trips ALTER COLUMN destination TYPE BIGINT USING destination::BIGINT');
        } else {
            Schema::table('hafas_trips', function (Blueprint $table) {
                $table->bigInteger('origin')->unsigned()->change();
                $table->bigInteger('destination')->unsigned()->change();
            });
        }

        Schema::table('hafas_trips', function (Blueprint $table) {
            $table->foreign('origin')
                ->references('ibnr') // TODO: Should use 'id' instead...
                ->on('train_stations')
                ->onDelete('restrict')
                ->onUpdate('cascade');
            $table->foreign('destination')
                ->references('ibnr') // TODO: Should use 'id' instead...
                ->on('train_stations')
                ->onDelete('restrict')
                ->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('hafas_trips', function (Blueprint $table) {
            $table->dropUnique('hafas_trips_trip_id_unique');
            $table->dropForeign('hafas_trips_origin_foreign');
            $table->dropForeign('hafas_trips_destination_foreign');
        });

        if (DB::getDriverName() === 'pgsql') {
            DB::statement('ALTER TABLE hafas_trips ALTER COLUMN origin TYPE VARCHAR(255) USING origin::VARCHAR');
            DB::statement('ALTER TABLE hafas_trips ALTER COLUMN destination TYPE VARCHAR(255) USING destination::VARCHAR');
        } else {
            Schema::table('hafas_trips', function (Blueprint $table) {
                $table->string('origin', 255)->change();
                $table->string('destination', 255)->change();
            });
        }
    }
}
