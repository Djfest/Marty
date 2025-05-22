<?php namespace Marty\AiSolutions\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;
use October\Rain\Support\Facades\DB;

class CreateSessionsTable extends Migration
{
    public function up()
    {
        Schema::create('marty_aisolutions_sessions', function($table)
        {
            $table->engine = 'InnoDB';
            // Make sure this is defined as unsigned integer
            $table->increments('id'); // This creates an unsigned integer
            $table->string('name')->nullable();
            $table->string('identifier')->nullable();
            $table->integer('user_id')->unsigned()->nullable();
            $table->text('metadata')->nullable();
            $table->text('context')->nullable();
            $table->string('status')->default('active');
            $table->timestamps();
            
            // Add indexes if needed
            $table->index('user_id');
            $table->index('identifier');
        });
    }

    public function down()
    {
        Schema::dropIfExists('marty_aisolutions_sessions');
    }
}
