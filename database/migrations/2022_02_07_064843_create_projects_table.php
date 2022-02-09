<?php
/* projektek */

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProjectsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('projects', function (Blueprint $table) {
            $table->id()->comment("egyedi rekord ID");
            $table->unsignedBigInteger('team_id')->comment("csoport ID");
            $table->string('name')->nullable()->comment("projekt rövid neve");
            $table->mediumText('description')->comment("projekt leírása");
            $table->string('avatar')->nullable()->comment("projekt avatar kép url");
            $table->string('status')->nullable()->comment("proposal|active|closed");
            $table->date('deadline')->nullable()->comment("határidő");
            $table->mediumText('config')->comment("JSON string");
            $table->date('activated_at')->nullable()->comment("aktiválás dátuma");
            $table->date('closed_at')->nullable()->comment("lezárás dátuma");
            $table->timestamp('created_at')->useCurrent()->comment("rekord létrehozás időpontja");
            $table->timestamp('updated_at')->nullable()->useCurrent()->comment("rekord utolsó modositás időpontja");
            $table->unsignedBigInteger('created_by')->comment("létrehozó user");
            
            $table->foreign('created_by', 'projects_created_by_foreign')->references('id')->on('users')->onDelete('restrict')->onUpdate('restrict');
            $table->foreign('team_id', 'team_id_foreign')->references('id')->on('teams');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('projects');
    }
}
