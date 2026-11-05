<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AddForeignKeysToSocialLoginProfiles extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('social_login_profiles', function (Blueprint $table) {
            $table->bigInteger('id')->unsigned()->autoIncrement()->change();
            $table->bigInteger('user_id')->unsigned()->change();

            $table->foreign('user_id')
                ->references('id')
                ->on('users')
                ->onDelete('cascade')
                ->onUpdate('cascade');
        });

        if (DB::getDriverName() === 'pgsql') {
            DB::statement('ALTER TABLE social_login_profiles ALTER COLUMN twitter_id TYPE BIGINT USING twitter_id::BIGINT');
        } else {
            Schema::table('social_login_profiles', function (Blueprint $table) {
                $table->bigInteger('twitter_id')->unsigned()->change();
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
        Schema::table('social_login_profiles', function (Blueprint $table) {
            $table->dropForeign('social_login_profiles_user_id_foreign');
        });
        if (DB::getDriverName() === 'pgsql') {
            DB::statement('ALTER TABLE social_login_profiles ALTER COLUMN twitter_id TYPE VARCHAR(255) USING twitter_id::VARCHAR');
        }
        Schema::table('social_login_profiles', function (Blueprint $table) {
            $table->integer('id')->unsigned()->autoIncrement()->change();
            $table->integer('user_id')->unsigned()->change();
            if (DB::getDriverName() !== 'pgsql') {
                $table->string('twitter_id', 255)->change();
            }
        });
    }
}
