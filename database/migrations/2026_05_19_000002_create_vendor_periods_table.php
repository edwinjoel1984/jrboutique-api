<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vendor_periods', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('vendor_id');
            $table->foreign('vendor_id')->references('id')->on('users')->onDelete('cascade');
            $table->smallInteger('year');
            $table->tinyInteger('month');
            $table->enum('status', ['OPEN', 'CLOSED'])->default('OPEN');
            $table->timestamps();
            $table->unique(['vendor_id', 'year', 'month']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vendor_periods');
    }
};
