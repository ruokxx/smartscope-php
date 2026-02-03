<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('objects', function (Blueprint $table) {
            $table->id();
            $table->string('name');             // e.g. M31
            $table->string('catalog')->nullable(); // Messier / NGC text
            $table->string('ra')->nullable();
            $table->string('dec')->nullable();
            $table->string('type')->nullable();
            $table->text('description')->nullable();
            $table->timestamps();
            $table->unique(['name','catalog']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('objects');
    }
};
