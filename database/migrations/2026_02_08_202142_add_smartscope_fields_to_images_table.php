<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration 
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('images', function (Blueprint $table) {
            $table->integer('gain')->nullable()->after('iso_or_gain');
            $table->integer('bortle')->nullable()->after('filter');
            $table->string('seeing')->nullable()->after('bortle');
            $table->date('session_date')->nullable()->after('upload_time');
            $table->float('sub_exposure_time')->nullable()->after('sub_exposure_seconds'); // For precise/fractional seconds
        });
    }

    public function down(): void
    {
        Schema::table('images', function (Blueprint $table) {
            $table->dropColumn(['gain', 'bortle', 'seeing', 'session_date', 'sub_exposure_time']);
        });
    }
};
