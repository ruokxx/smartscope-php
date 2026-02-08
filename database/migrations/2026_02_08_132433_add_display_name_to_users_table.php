<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDisplayNameToUsersTable extends Migration
{
    public function up()
    {
        if (! Schema::hasColumn('users', 'display_name')) {
            Schema::table('users', function (Blueprint $table) {
                // Add column without using ->after() to avoid dependency on other columns
                $table->string('display_name')->nullable();
            });
        }
    }

    public function down()
    {
        if (Schema::hasColumn('users', 'display_name')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('display_name');
            });
        }
    }
}
