<?php namespace Marty\AiSolutions\Updates;

use Schema;
use October\Rain\Database\Schema\Blueprint;
use October\Rain\Database\Updates\Migration;
use Illuminate\Support\Facades\DB; // Changed from "use DB;" to proper namespace

class CreateKnowledgeBaseTable extends Migration
{
    public function up()
    {
        Schema::create('marty_aisolutions_knowledge_base', function(Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->string('title');
            $table->text('content')->nullable();
            $table->string('keywords')->nullable();
            $table->integer('profile_type_id')->unsigned()->nullable();
            $table->string('external_id')->nullable();
            $table->integer('user_id')->unsigned()->nullable();
            $table->timestamps();
            
            $table->foreign('profile_type_id')
                ->references('id')
                ->on('marty_nexgenrifle_profile_types')
                ->onDelete('set null');
                
            $table->foreign('user_id')
                ->references('id')
                ->on('backend_users')
                ->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::dropIfExists('marty_aisolutions_knowledge_base');
    }
}
