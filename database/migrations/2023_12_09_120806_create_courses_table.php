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
        Schema::create('courses', function (Blueprint $table) {
            $table->id();
            // who is posting
            $table->string('user_token', 50);
            // course name
            $table->string('name', 255);
            // course thumbnail
            $table->string('thumbnail', 255);
            // course video
            $table->string('video', 255)->nullable();
            // course description
            $table->text('description', 255)->nullable();
            // course type id
            $table->smallInteger('type_id');
            // course price
            $table->float('price');
            // course lessons count
            $table->integer('lessons_count')->default(0);
            // course video length
            $table->smallInteger('video_length')->nullable();
            // course followers
            $table->integer('followers')->default(0);
            // course score
            $table->float('score')->default(4);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('courses');
    }
};
