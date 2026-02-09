<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration 
{
    public function up()
    {
        Schema::table('posts', function (Blueprint $table) {
            $table->string('image_path')->nullable()->after('content');
        });

        Schema::table('group_user', function (Blueprint $table) {
            $table->timestamp('accepted_at')->nullable()->after('user_id');
        });
    }

    public function down()
    {
        Schema::table('group_user', function (Blueprint $table) {
            $table->dropColumn('accepted_at');
        });

        Schema::table('posts', function (Blueprint $table) {
            $table->dropColumn('image_path');
        });
    }
};
