<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddEventColumnToActivityLogTable extends Migration
{
    public function up(): void
    {
        $tableName = config('activitylog.database_table_name') ?: 'activity_log';

        Schema::connection(config('activitylog.database_connection'))->table($tableName, function (Blueprint $table) {
            $table->string('event')->nullable()->after('subject_type');
        });
    }

    public function down(): void
    {
        $tableName = config('activitylog.database_table_name') ?: 'activity_log';

        Schema::connection(config('activitylog.database_connection'))->table($tableName, function (Blueprint $table) {
            $table->dropColumn('event');
        });
    }
}
