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
        Schema::table('order_details', function (Blueprint $table) {
            //
            $table->unsignedBigInteger('article_size_id')->nullable()->change();
            $table->unsignedBigInteger('offer_id')->nullable()->after('article_size_id');

            $table->foreign('offer_id')
                ->references('id')         // El campo de la tabla `offers` al que hace referencia
                ->on('offers')             // La tabla a la que hace referencia (en este caso, 'offers')
                ->onDelete('set null')     // Define qué hacer cuando se elimina un registro de `offers`
                ->nullable();
        });
    }



    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('order_details', function (Blueprint $table) {

            $table->dropForeign(['offer_id']);

            $table->dropColumn('offer_id');
            $table->unsignedBigInteger('article_size_id')->nullable(false)->change();
        });
    }
};
