<?php
/* termékek */

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id()->comment("egyedi rekord ID");
            $table->string('parent_type')->nullable()->comment("gyártó, forgalmazó tipus:  teams|users")->index('team_id');
            $table->unsignedBigInteger('parent')->comment("parent_type.id");
            $table->string('name')->nullable()->comment("rövid megnevezés")->index('name');
            $table->string('unit')->nullable()->comment("mértékegység");
            $table->double('price')->nullable()->comment("egységár");
            $table->string('currency')->default('NTC COMMENT 'pénznem'');
            $table->double('vat')->nullable()->comment("ÁFA%");
            $table->string('type')->nullable()->comment("tipus");
            $table->mediumText('description')->comment("leírás");
            $table->string('avatar')->nullable()->comment("avatar kép url");
            $table->string('status')->nullable()->comment("active|disabled");
            $table->double('stock')->nullable()->comment("készlet");
            $table->timestamp('created_at')->useCurrent()->comment("rekord létrehozás időpontja");
            $table->timestamp('updated_at')->nullable()->useCurrent()->comment("utolsó modosítás időpontja");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('products');
    }
}
