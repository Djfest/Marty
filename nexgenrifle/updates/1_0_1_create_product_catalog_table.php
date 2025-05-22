<?php namespace Marty\NexGenRifle\Updates;

use Schema;
use October\Rain\Database\Schema\Blueprint;
use October\Rain\Database\Updates\Migration;

class CreateProductCatalogTable extends Migration
{
    public function up()
    {
        Schema::create('marty_nexgenrifle_product_catalog', function(Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->uuid('uuid')->unique()->nullable();
            $table->integer('product_category_id')->unsigned()->nullable(); // Made nullable
            $table->integer('supplier_id')->unsigned()->nullable(); // Renamed from vendor_id and made nullable
            $table->string('title');
            $table->decimal('price', 10, 2)->nullable();
            $table->string('image_url')->nullable();
            $table->string('product_url')->nullable();
            $table->string('affiliate_click_url')->nullable();
            $table->boolean('is_affiliate_tracked')->default(false);
            $table->json('config')->nullable();
            $table->timestamps();

            // Foreign key constraints removed as per request.
        });
    }

    public function down()
    {
        // No need to drop foreign keys as they are not created in the up() method anymore.
        Schema::dropIfExists('marty_nexgenrifle_product_catalog');
    }
}
