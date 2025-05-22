<?php namespace Marty\NexGenRifle\Updates;

use Schema;
use October\Rain\Database\Schema\Blueprint;
use October\Rain\Database\Updates\Migration;

class CreateProductItemsTable extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('marty_nexgenrifle_product_items')) {
            Schema::create('marty_nexgenrifle_product_items', function(Blueprint $table) {
                $table->engine = 'InnoDB';
                $table->increments('id');
                $table->uuid('uuid')->unique()->nullable();
                $table->integer('product_catalog_id')->unsigned();
                $table->string('name');
                $table->decimal('price', 10, 2)->nullable();
                $table->string('image_url')->nullable();
                $table->json('config')->nullable();
                $table->timestamps();
            });
            
            try {
                Schema::table('marty_nexgenrifle_product_items', function(Blueprint $table) {
                    $table->foreign('product_catalog_id')
                          ->references('id')
                          ->on('marty_nexgenrifle_product_catalog')
                          ->onDelete('restrict');
                });
            } catch (\Exception $e) {
                \Log::warning('Could not create product_catalog_id foreign key: ' . $e->getMessage());
            }
        }
    }

    public function down()
    {
        Schema::dropIfExists('marty_nexgenrifle_product_items');
    }
}
