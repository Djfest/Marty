<?php namespace Marty\NexGenRifle\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class CreateProductsTable extends Migration
{
    public function up()
    {
        Schema::create('marty_nexgenrifle_products', function($table)
        {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->string('uuid')->unique()->index();
            $table->string('name');
            $table->string('slug')->unique()->index();
            $table->text('description')->nullable();
            $table->text('short_description')->nullable();
            $table->string('brand')->nullable(); 
            $table->integer('category_id')->unsigned()->nullable()->index();
            $table->decimal('price', 10, 2)->nullable();
            $table->string('sku')->nullable();
            $table->integer('stock')->nullable();
            $table->boolean('is_active')->default(true);
            $table->boolean('is_featured')->default(false);
            $table->string('image')->nullable();
            $table->text('gallery')->nullable();
            $table->text('metadata')->nullable();
            $table->timestamps();
            
            // Foreign keys
            $table->foreign('category_id')
                  ->references('id')
                  ->on('marty_nexgenrifle_product_categories')
                  ->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::dropIfExists('marty_nexgenrifle_products');
    }
}