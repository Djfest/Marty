<?php namespace Marty\NexGenRifle\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class CreateProductCategoriesTable extends Migration
{
    public function up()
    {
        Schema::create('marty_nexgenrifle_product_categories', function($table)
        {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->string('uuid')->unique()->index();
            $table->string('name');
            $table->string('slug')->unique()->index();
            $table->text('description')->nullable();
            $table->integer('parent_id')->nullable()->index();
            $table->integer('nest_left')->nullable();
            $table->integer('nest_right')->nullable();
            $table->integer('nest_depth')->nullable();
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->string('image')->nullable();
            $table->string('meta_title')->nullable();
            $table->string('meta_description')->nullable();
            $table->string('meta_keywords')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('marty_nexgenrifle_product_categories');
    }
}
