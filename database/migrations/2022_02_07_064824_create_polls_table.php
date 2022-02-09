<?php
/* vita, szavazás */

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePollsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('polls', function (Blueprint $table) {
            $table->id()->comment("rekord egyedi ID");
            $table->string('parent_type')->nullable()->comment("parent tipus teams|projects");
            $table->unsignedBigInteger('parent')->comment("parent_type.id");
            $table->string('name')->nullable()->comment("rövid megnevezés");
            $table->string('status')->nullable()->comment("proposal|deabate|voks|closed");
            $table->mediumText('description')->comment("leírás");
            $table->mediumText('config')->comment("beállítások JSON string");
            $table->unsignedBigInteger('created_by')->comment("létrehozó user");
            $table->timestamp('created_at')->useCurrent()->comment("rekord létrehozás időpontja");
            $table->timestamp('updated_at')->nullable()->useCurrent()->comment("utolsó modosítás időpontja");
            $table->date('debate_start')->nullable()->comment("vita megindulásának dátuma");
            
            $table->index(['parent_type', 'parent'], 'polls_parent_type_parent_index');
            $table->foreign('created_by', 'polls_created_by_foreign')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('polls');
    }
}
