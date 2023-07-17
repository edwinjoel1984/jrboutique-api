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
        Schema::create('article_sizes', function (Blueprint $table) {
            $table->id();
            $table->biginteger('article_id')->unsigned();
            $table->biginteger('size_id')->unsigned();
            $table->decimal('price', 12, 2);
            $table->integer('quantity');
            $table->timestamps();

            $table->foreign('article_id')->references('id')->on('articles');
            $table->foreign('size_id')->references('id')->on('sizes');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('article_sizes');
    }
};
