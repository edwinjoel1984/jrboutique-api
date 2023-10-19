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
        Schema::table('article_sizes', function (Blueprint $table) {
            $table->renameColumn('price', 'sale_price');
            $table->decimal('purchase_price', 12, 2)->after('size_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('article_sizes', function (Blueprint $table) {
            $table->renameColumn('sales_price', 'price');
            $table->dropColumn('purchase_price');
        });
    }
};
