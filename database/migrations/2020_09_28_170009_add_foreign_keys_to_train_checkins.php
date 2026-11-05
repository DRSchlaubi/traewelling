<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AddForeignKeysToTrainCheckins extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('train_checkins', function (Blueprint $table) {
            $table->bigInteger('status_id')->unsigned()->change();

            $table->foreign('status_id')
                ->references('id')
                ->on('statuses')
                ->onDelete('cascade')
                ->onUpdate('cascade');
        });

        if (DB::getDriverName() === 'pgsql') {
            DB::statement('ALTER TABLE train_checkins ALTER COLUMN origin TYPE BIGINT USING origin::BIGINT');
            DB::statement('ALTER TABLE train_checkins ALTER COLUMN destination TYPE BIGINT USING destination::BIGINT');
        } else {
            Schema::table('train_checkins', function (Blueprint $table) {
                $table->bigInteger('origin')->unsigned()->change();
                $table->bigInteger('destination')->unsigned()->change();
            });
        }

        Schema::table('train_checkins', function (Blueprint $table) {
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
            $table->foreign('trip_id')
                ->references('trip_id') // TODO: Should use 'id' instead...
                ->on('hafas_trips')
                ->onDelete('restrict')
                ->onUpdate('restrict');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('train_checkins', function (Blueprint $table) {
            $table->dropForeign('train_checkins_origin_foreign');
            $table->dropForeign('train_checkins_destination_foreign');
            $table->dropForeign('train_checkins_trip_id_foreign');
            $table->dropForeign('train_checkins_status_id_foreign');
        });
        if (DB::getDriverName() === 'pgsql') {
            DB::statement('ALTER TABLE train_checkins ALTER COLUMN origin TYPE VARCHAR(255) USING origin::VARCHAR');
            DB::statement('ALTER TABLE train_checkins ALTER COLUMN destination TYPE VARCHAR(255) USING destination::VARCHAR');
            Schema::table('train_checkins', function (Blueprint $table) {
                $table->integer('status_id')->change();
            });
        } else {
            Schema::table('train_checkins', function (Blueprint $table) {
                $table->integer('status_id')->change();
                $table->string('origin', 255)->change();
                $table->string('destination', 255)->change();
            });
        }
    }
}
