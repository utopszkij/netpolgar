<?php
/* csoport, projekt   - tagság, tisztségek, esemény résztvevők,file letöltők */

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMembersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('members', function (Blueprint $table) {
            $table->id()->comment("Egyedi rekord ID");
            $table->string('parent_type')->comment("teams|projects|events|files");
            $table->unsignedBigInteger('parent')->nullable()->comment("teams.id|projects.id|events.id");
            $table->unsignedBigInteger('user_id')->nullable()->comment("user.id");
            $table->string('rank')->nullable()->comment("member|admin|moderator|...a teamben definiált tisztségek...");
            $table->string('status')->nullable()->comment("proposal|active|closed|excluded");
            $table->date('activated_at')->nullable()->comment("érvénybelépés dátuma");
            $table->date('closed_at')->nullable()->comment("megszünés dátuma");
            $table->timestamp('created_at')->useCurrent()->comment("rekord létrehozás időpontja");
            $table->timestamp('updated_at')->nullable()->useCurrent()->comment("utolsó modosítás időpontja");
            $table->unsignedBigInteger('created_by')->comment("rekordot létrehozó user");
            
            $table->index(['parent_type', 'parent'], 'parent_type_parent_index');
            $table->foreign('created_by', 'members_created_by_foreign')->references('id')->on('users');
            $table->foreign('user_id', 'members_user_id_foreign')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('members');
    }
}
