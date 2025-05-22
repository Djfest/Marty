<?php namespace Marty\AiSolutions\Updates;

use Schema;
use October\Rain\Database\Schema\Blueprint;
use October\Rain\Database\Updates\Migration;

class CreateModelGraphsTable extends Migration
{
    public function up()
    {
        Schema::create('marty_aisolutions_model_graphs', function(Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->string('title');
            $table->text('description')->nullable();
            $table->json('graph_data');
            $table->integer('profile_type_id')->unsigned()->nullable();
            $table->timestamps();
            
            $table->foreign('profile_type_id')
                ->references('id')
                ->on('marty_nexgenrifle_profile_types')
                ->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::dropIfExists('marty_aisolutions_model_graphs');
    }
}
