<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('vendor_inventories', function (Blueprint $table) {
            $table->string('custom_name', 255)->nullable()->after('article_size_id');
            $table->decimal('custom_price', 15, 2)->nullable()->after('custom_name');
        });
    }

    public function down(): void
    {
        Schema::table('vendor_inventories', function (Blueprint $table) {
            $table->dropColumn(['custom_name', 'custom_price']);
        });
    }
};
