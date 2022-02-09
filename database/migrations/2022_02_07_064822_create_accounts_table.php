<?php
/* NTC folyószámla pénz mozgások */

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAccountsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('accounts', function (Blueprint $table) {
            $table->id()->comment("egyedi rekord ID")->unique();
            $table->string('from_type', 45)->nullable()->comment("köldő tipus:  teams|users");
            $table->unsignedBigInteger('from')->comment("from_type.id");
            $table->string('target_type', 45)->nullable()->comment("címzett tipus: teams|users");
            $table->unsignedBigInteger('target')->comment("target_type.id");
            $table->string('status', 45)->nullable()->comment("üres|allocated");
            $table->float('value')->nullable()->comment("összeg");
            $table->string('comment')->nullable()->comment("publikus közlemény");
            $table->string('info', 45)->nullable()->comment("program által használt közlemény");
            $table->timestamp('created_at')->nullable()->useCurrent()->comment("rekord létrehozás időpontja");
            $table->timestamp('updated_at')->nullable()->useCurrent()->comment("rekord modosítás időpontja");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('accounts');
    }
}
