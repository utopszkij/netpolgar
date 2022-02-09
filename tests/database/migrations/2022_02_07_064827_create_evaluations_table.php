<?php
/* Termék értékelések */

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEvaluationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('evaluations', function (Blueprint $table) {
            $table->id()->comment("egyedi rekord ID");
            $table->unsignedBigInteger('product_id')->comment("termék");
            $table->unsignedBigInteger('user_id')->comment("felhasználó");
            $table->integer('value')->nullable()->comment(""érdemjegy 1 - 5");
            $table->timestamp('created_at')->useCurrent()->comment("létrehozás időpontja");
            $table->timestamp('updated_at')->nullable()->useCurrent()->comment("utolsó modosítás időpontja");
            
            $table->foreign('product_id', 'evaluations_product_id_foreign1')->references('id')->on('products');
            $table->foreign('user_id', 'evaluations_user_id_foreign')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('evaluations');
    }
}
