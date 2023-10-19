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
        Schema::create('groupsizes_sizes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBiginteger('group_size_id')->unsigned();
            $table->unsignedBiginteger('size_id')->unsigned();
            $table->integer('order')->unsigned()->nullable();
            $table->foreign('group_size_id')->references('id')
                ->on('group_sizes')->onDelete('cascade');
            $table->foreign('size_id')->references('id')
                ->on('sizes')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('groupsizes_sizes');
    }
};
