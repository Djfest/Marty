<?php namespace Marty\NexGenRifle\Updates;

use Schema;
use October\Rain\Database\Schema\Blueprint;
use October\Rain\Database\Updates\Migration;

class CreateBaseTables extends Migration
{
    public function up()
    {
        Schema::create('marty_nexgenrifle_build_categories', function(Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->uuid('uuid')->unique()->nullable();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->json('config')->nullable();
            $table->timestamps();
        });

        Schema::create('marty_nexgenrifle_product_categories', function(Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->uuid('uuid')->unique()->nullable();
            $table->string('group_key');
            $table->string('name');
            $table->string('slug')->unique();
            $table->integer('order')->default(0);
            $table->json('config')->nullable();
            $table->timestamps();
        });

        Schema::create('marty_nexgenrifle_vendors', function(Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->uuid('uuid')->unique()->nullable();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('base_url')->nullable();
            $table->string('affiliate_url')->nullable();
            $table->string('affiliate_program')->nullable();
            $table->boolean('is_affiliate')->default(false);
            $table->boolean('is_enabled')->default(true);
            $table->string('logo_url')->nullable();
            $table->json('metadata')->nullable(); // Convert from text to json
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('marty_nexgenrifle_build_categories');
        Schema::dropIfExists('marty_nexgenrifle_product_categories');
        Schema::dropIfExists('marty_nexgenrifle_vendors');
    }
}
