<?php

// termékek

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
            $table->biginteger('project_id')->unsigned()->nullable();  // tuljdonos projekt
            $table->string('name')->nullabled();  // megnevezés
            $table->mediumtext('description')->nullabled();  // leírás
            $table->string('avatar')->nullabled();  // avatar kép url
            $table->string('status')->nullabled();  // státusz
            $table->string('type')->nullabled();  // tipus
            $table->integer('quantity')->nullabled(); // mennyiség
            $table->string('unit')->nullabled(); // mennyiség egység
            $table->integer('price')->nullabled(); //egységár
            $table->date('start')->nullabled();  // tényleges indulás
            $table->date('end')->nullabled();  // tényleges vége
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent();
            $table->biginteger('created_by')->unsigned();
            // ----
            $table->foreign('project_id')->references('id')->on('projects');
            $table->foreign('created_by')->references('id')->on('users');
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
