<?php namespace Marty\NexGenRifle\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class CreateBuildListTables extends Migration
{    public function up()
    {
        Schema::disableForeignKeyConstraints();
        Schema::create('marty_nexgenrifle_build_lists', function($table)
        {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->string('uuid')->unique()->nullable();
            $table->string('name');
            $table->text('description')->nullable();
            $table->integer('user_id')->unsigned()->nullable();
            $table->string('list_type')->nullable();
            $table->string('status')->default('planning');
            $table->timestamp('target_date')->nullable();
            $table->decimal('total_budget', 10, 2)->nullable();
            $table->decimal('current_total', 10, 2)->default(0);
            $table->json('metadata')->nullable(); // Convert from text to json
            $table->json('config')->nullable(); // Convert from text to json
            $table->boolean('is_completed')->default(false);
            $table->timestamps();
            $table->softDeletes();
            
            $table->index('user_id');
            $table->index('list_type');
            $table->index('status');
        });

        Schema::create('marty_nexgenrifle_build_list_items', function($table)
        {
            $table->engine = 'InnoDB';            $table->increments('id');
            $table->uuid('uuid')->unique()->nullable();
            $table->integer('build_list_id')->unsigned()->nullable();
            $table->integer('product_id')->unsigned()->nullable();
            $table->integer('supplier_id')->unsigned()->nullable(); // Renamed from vendor_id
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('status')->default('planned')->nullable();
            $table->decimal('price', 10, 2)->nullable();
            $table->integer('quantity')->default(1)->nullable();
            $table->integer('priority')->default(1)->nullable();
            $table->timestamp('target_date')->nullable();
            $table->string('product_url')->nullable();
            $table->string('affiliate_url')->nullable();
            $table->text('metadata')->nullable();
            $table->text('config')->nullable();
            $table->boolean('is_acquired')->default(false)->nullable();
            $table->timestamps();
            $table->softDeletes();
            
            $table->index('build_list_id');
            $table->index('product_id');            $table->index('supplier_id'); // Renamed from vendor_id
            $table->index('status');
        });
        
        Schema::enableForeignKeyConstraints();
    }    public function down()
    {
        Schema::disableForeignKeyConstraints();
        Schema::dropIfExists('marty_nexgenrifle_build_list_items');
        Schema::dropIfExists('marty_nexgenrifle_build_lists');
        Schema::enableForeignKeyConstraints();
    }
}
