<?php
/* termék kategóriák kapcsoló tábla */

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductcatsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('productcats', function (Blueprint $table) {
            $table->id()->comment("Egyedi rekord ID");
            $table->unsignedBigInteger('product_id')->comment("produkt ID");
            $table->string('category')->nullable()->comment("kategória ID")->index('productcats_category_index');
            
            $table->foreign('product_id', 'productcats_product_id_foreign')->references('id')->on('products');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('productcats');
    }
}
