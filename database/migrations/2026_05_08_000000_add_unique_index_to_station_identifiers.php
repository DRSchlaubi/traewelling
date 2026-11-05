<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration
{
    public function up(): void
    {
        if (DB::getDriverName() !== 'sqlite') {
            // Remove duplicates, keeping the row with the lowest id per (type, identifier) pair.
            DB::statement('
                DELETE FROM station_identifiers
                WHERE EXISTS (
                    SELECT 1
                    FROM station_identifiers si2
                    WHERE station_identifiers.type = si2.type
                    AND station_identifiers.identifier = si2.identifier
                    AND station_identifiers.id > si2.id
                )
            ');
        }

        Schema::table('station_identifiers', function (Blueprint $table) {
            $table->unique(['type', 'identifier'], 'station_identifiers_type_identifier_unique');
        });
    }

    public function down(): void
    {
        Schema::table('station_identifiers', function (Blueprint $table) {
            $table->dropUnique('station_identifiers_type_identifier_unique');
        });
    }
};
