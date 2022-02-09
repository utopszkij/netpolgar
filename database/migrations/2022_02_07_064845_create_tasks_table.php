<?php
/* projekt feladatok */

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTasksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tasks', function (Blueprint $table) {
            $table->id()->comment("egyedi rekord ID");
            $table->unsignedBigInteger('project_id')->comment("projekt ID");
            $table->mediumText('name')->comment("rövid leírás");
            $table->string('status')->nullable()->comment("waiting|canwork|working|cancontroll|incontrol|closed");
            $table->integer('position')->nullable()->comment("prioritás (kics a sürgősebb)");
            $table->date('deadline')->nullable()->comment("határidő");
            $table->string('type')->nullable()->comment("bug|task|info|question");
            $table->unsignedBigInteger('assign')->nullable()->comment("felelős user");
            $table->timestamp('created_at')->useCurrent()->comment("rekord létrehozás időpontja");
            $table->timestamp('updated_at')->nullable()->useCurrent()->comment("utolsó modositás időpontja");
            
            $table->foreign('project_id', 'project_id_foreign')->references('id')->on('projects')->onDelete('restrict')->onUpdate('restrict');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tasks');
    }
}
