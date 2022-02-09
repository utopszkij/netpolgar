<?php
/* tetszik/nem tetszik jelzések */

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLikesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('likes', function (Blueprint $table) {
            $table->id();
            $table->string('parent_type')->nullable()->comment("teams|members|projects|polls|options|products");
            $table->unsignedBigInteger('parent')->nullable()->comment("parent_type.id");
            $table->unsignedBigInteger('user_id')->comment("véleményező user");
            $table->string('like_type')->nullable()->comment("like|dislike");
            $table->timestamp('created_at')->useCurrent()->comment("rekord létrehozás időpontjaa");
            $table->timestamp('updated_at')->comment("utolsó módosítás időpontja");
            
            $table->index(['parent_type', 'parent'], 'likes_parent_type_parent_index');
            $table->foreign('user_id', 'user_id_foreign')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('likes');
    }
}
