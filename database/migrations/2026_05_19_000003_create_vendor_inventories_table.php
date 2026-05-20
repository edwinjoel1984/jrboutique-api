<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vendor_inventories', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('vendor_id');
            $table->foreign('vendor_id')->references('id')->on('users')->onDelete('cascade');
            $table->unsignedBigInteger('period_id');
            $table->foreign('period_id')->references('id')->on('vendor_periods')->onDelete('cascade');
            $table->unsignedBigInteger('article_size_id');
            $table->foreign('article_size_id')->references('id')->on('article_sizes')->onDelete('cascade');
            $table->integer('quantity_assigned');
            $table->integer('quantity_returned')->default(0);
            $table->enum('status', ['ACTIVE', 'FULLY_RETURNED'])->default('ACTIVE');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vendor_inventories');
    }
};
