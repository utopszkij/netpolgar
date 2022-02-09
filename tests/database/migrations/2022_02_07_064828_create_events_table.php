<?php
/* események */

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEventsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('events', function (Blueprint $table) {
            $table->unsignedBigInteger('id')->comment("egyedi rekord ID");
            $table->string('parent_type', 45)->nullable()->comment("szervező tipusa: teams|projects");
            $table->unsignedBigInteger('parent')->comment("szervező ID");
            $table->string('name')->nullable()->comment("esemény rövid neve");
            $table->mediumText('description')->comment("esemény leírása");
            $table->string('avatar')->nullable()->comment("avatar kép url");
            $table->string('location')->nullable()->comment("helyszin");
            $table->date('date')->nullable()->comment("dátum");
            $table->integer('hours')->nullable()->comment("kezdés óra");
            $table->integer('minute')->nullable()->comment("kezdés perc");
            $table->string('length')->comment("esemény időtartama");
            $table->timestamp('created_at')->nullable()->useCurrent()->comment("rekord létrehozás időpontja");
            $table->timestamp('updated_at')->nullable()->useCurrent()->comment("utolsó modosítás időpontja");
            $table->unsignedBigInteger('created_by')->comment("létrehozó user");
            
            $table->primary(['id', 'length']);
            $table->foreign('created_by', 'events_created_by_foreign')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('events');
    }
}
