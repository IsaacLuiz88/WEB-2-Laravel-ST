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
        Schema::create('books', function (Blueprint $table) {
            $table->integer('id')->primary()->autoIncrement();
            $table->string('title', 255);
            $table->unsignedBigInteger('author_id')->cascadeOnDelete();
            $table->unsignedBigInteger('category_id')->cascadeOnDelete();
            $table->unsignedBigInteger('publisher_id')->cascadeOnDelete();
            $table->integer('published_year')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('books');
    }
};
