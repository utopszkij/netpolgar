<?php
/* fileok */

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFilesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('files', function (Blueprint $table) {
            $table->id()->comment("egyedi ID");
            $table->string('parent_type', 45)->nullable()->comment("tulajdonos tipusa:  teams|projects|products|events|users");
            $table->unsignedBigInteger('parent')->comment("tilajdonos ID");
            $table->string('name')->nullable()->comment("rövid megnevezés");
            $table->mediumText('description')->comment("leírás");
            $table->string('type', 45)->nullable()->comment("file tipus");
            $table->string('licence', 45)->nullable()->comment("licensz");
            $table->unsignedBigInteger('created_by')->comment("feltöltő user");
            $table->timestamps()->comment("rekord létrehozás időpontja");
            
            $table->foreign('created_by', 'files_created_by_foreign')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('files');
    }
}
