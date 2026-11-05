<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class ChangeStatusesToAcceptBusinessCheckinAsInteger extends Migration
{
    public function up()
    {
        DB::table('statuses')
            ->whereNull('business')
            ->update(['business' => 0]);

        if (DB::getDriverName() === 'pgsql') {
            DB::statement(
                'ALTER TABLE statuses ALTER COLUMN business TYPE SMALLINT USING CASE WHEN business THEN 1 ELSE 0 END'
            );
            DB::statement('ALTER TABLE statuses ALTER COLUMN business SET DEFAULT 0');
            DB::statement('ALTER TABLE statuses ALTER COLUMN business SET NOT NULL');
        } else {
            Schema::table('statuses', function (Blueprint $table) {
                $table->unsignedSmallInteger('business')
                    ->default(0)
                    ->change();
            });
        }
    }

    public function down()
    {
        DB::table('statuses')
            ->where('business', '>', '1')
            ->update(['business' => 1]);

        if (DB::getDriverName() === 'pgsql') {
            DB::statement(
                'ALTER TABLE statuses ALTER COLUMN business TYPE BOOLEAN USING CASE WHEN business = 0 THEN FALSE ELSE TRUE END'
            );
            DB::statement('ALTER TABLE statuses ALTER COLUMN business DROP NOT NULL');
        } else {
            Schema::table('statuses', function (Blueprint $table) {
                $table->boolean('business')
                    ->change();
            });
        }
    }
}
