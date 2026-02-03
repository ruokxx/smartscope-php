<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('images', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('object_id')->nullable()->constrained('objects')->nullOnDelete();
            $table->foreignId('scope_id')->nullable()->constrained('scopes')->nullOnDelete();
            $table->string('filename');
            $table->string('path'); // storage path
            $table->timestamp('upload_time')->useCurrent();
            $table->integer('exposure_total_seconds')->nullable();
            $table->integer('sub_exposure_seconds')->nullable();
            $table->integer('number_of_subs')->nullable();
            $table->string('iso_or_gain')->nullable();
            $table->string('filter')->nullable();
            $table->string('processing_software')->nullable();
            $table->text('processing_steps')->nullable();
            $table->text('notes')->nullable();
            $table->boolean('approved')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('images');
    }
};
