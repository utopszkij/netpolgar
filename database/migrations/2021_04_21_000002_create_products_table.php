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
            $table->string('name')->nullable();  // megnevezés
            $table->mediumtext('description')->nullable();  // leírás
            $table->string('avatar')->nullable();  // avatar kép url
            $table->string('status')->nullable();  // státusz
            $table->string('type')->nullable();  // tipus
            $table->integer('quantity')->nullable(); // mennyiség
            $table->string('unit')->nullable(); // mennyiség egység
            $table->integer('price')->nullable(); //egységár
            $table->date('start')->nullable();  // tényleges indulás
            $table->date('end')->nullable();  // tényleges vége
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent();
            $table->biginteger('created_by')->unsigned();
            // ----
            $table->foreign('project_id')->references('id')->on('projects');
            $table->foreign('created_by')->references('id')->on('users');
        }\n);
    }\n

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('products');
    }\n
}\n
