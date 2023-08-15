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
        Schema::create('payment_commitments', function (Blueprint $table) {
            $table->id();
            $table->date('date');
            $table->integer('amount');
            $table->bigInteger('commitment_id')->unsigned();
            $table->bigInteger('payment_id')->unsigned();
            $table->timestamps();

            $table->foreign('commitment_id')->references('id')->on('commitments')->onDelete('cascade');
            $table->foreign('payment_id')->references('id')->on('payments')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payment_commitments');
    }
};
