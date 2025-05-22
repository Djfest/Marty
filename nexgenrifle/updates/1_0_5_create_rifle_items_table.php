<?php namespace Marty\NexGenRifle\Updates;

use Schema;
use October\Rain\Database\Schema\Blueprint;
use October\Rain\Database\Updates\Migration;

class CreateRifleItemsTable extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('marty_nexgenrifle_rifle_items')) {
            Schema::create('marty_nexgenrifle_rifle_items', function(Blueprint $table) {
                $table->engine = 'InnoDB';
                $table->increments('id');
                $table->uuid('uuid')->unique()->nullable();
                $table->integer('rifle_build_id')->unsigned()->nullable(); // Made nullable
                $table->integer('product_category_id')->unsigned()->nullable(); // Added product_category_id
                $table->integer('product_id')->unsigned()->nullable(); // Made nullable, maps to product_item_id in model
                $table->integer('supplier_id')->unsigned()->nullable(); // Added supplier_id
                $table->integer('quantity')->default(1);
                $table->decimal('price_override', 10, 2)->nullable();
                $table->text('notes')->nullable();
                $table->json('metadata')->nullable();
                $table->timestamps();
            });
            
            // Foreign key constraints removed as per request.
            // If you need to add them back later, ensure the referenced tables and columns exist.
        }
    }

    public function down()
    {
        Schema::dropIfExists('marty_nexgenrifle_rifle_items');
    }
}
