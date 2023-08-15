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
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->biginteger('article_size_id')->unsigned();
            $table->biginteger('order_id')->unsigned()->nullable();
            $table->biginteger('customer_id')->unsigned();
            $table->enum('type', ['VENTA', 'DEVOLUCION', 'OTRO']);
            $table->integer('quantity');
            $table->text('memo');
            $table->timestamps();

            $table->foreign('order_id')->references('id')->on('orders')->onDelete('cascade');
            $table->foreign('customer_id')->references('id')->on('customers')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
