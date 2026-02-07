<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddApprovedToImagesTable extends Migration
{
    public function up()
    {
        if (! Schema::hasColumn('images', 'approved')) {
            Schema::table('images', function (Blueprint $table) {
                $table->boolean('approved')->default(false)->after('notes');
            });
        }
    }

    public function down()
    {
        if (Schema::hasColumn('images', 'approved')) {
            Schema::table('images', function (Blueprint $table) {
                $table->dropColumn('approved');
            });
        }
    }
}
