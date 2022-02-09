<?php

// csoportok

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
            $table->id();
            $table->string('parent_type');
            $table->biginteger('parent')->unsigned()->nullable();
            $table->string('name')->nullable();  // megnevezés
            $table->string('unit')->nullable(); // mértékegység
            $table->double('price')->nullable(); // egységár
            $table->string('currency')->nullable();
            $table->double('vat')->nullable(); // ÁFA %
            $table->string('type')->nullable(); // 'anygai'|'szellemi'|'szolgáltatás'
            $table->mediumtext('description')->nullable(); // leírás
            $table->string('avatar')->nullable();  // avatar kép url
            $table->string('status')->nullable(); // 'active'|'inactive'
            $table->double('stock')->nullable(); // készlet
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent()->nullable();
            $table->index(['parent_type','parent']);
            $table->index(['name']);
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
