<?php namespace Marty\NexGenRifle\Updates;

use Schema;
use October\Rain\Database\Schema\Blueprint;
use October\Rain\Database\Updates\Migration;

class CreateCategoriesTable extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('marty_nexgenrifle_categories')) {
            Schema::create('marty_nexgenrifle_categories', function(Blueprint $table) {
                $table->engine = 'InnoDB';
                $table->increments('id');
                $table->uuid('uuid')->unique()->nullable();
                $table->string('name');
                $table->string('slug')->unique();
                $table->text('description')->nullable();
                $table->integer('sort_order')->nullable();
                $table->boolean('is_active')->default(true);
                $table->timestamps();
            });
        }
    }

    public function down()
    {
        Schema::dropIfExists('marty_nexgenrifle_categories');
    }
}
