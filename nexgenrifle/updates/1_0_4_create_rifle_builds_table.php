<?php namespace Marty\NexGenRifle\Updates;

use Schema;
use October\Rain\Database\Schema\Blueprint;
use October\Rain\Database\Updates\Migration;

class CreateRifleBuildsTable extends Migration
{
    public function up()
    {
        // Create build categories table first if it doesn't exist
        if (!Schema::hasTable('marty_nexgenrifle_build_categories')) {
            Schema::create('marty_nexgenrifle_build_categories', function(Blueprint $table) {
                $table->engine = 'InnoDB';
                $table->increments('id');
                $table->string('name');
                $table->string('slug')->unique();
                $table->text('description')->nullable();
                $table->timestamps();
            });
        }
        
        // Create rifle builds table without foreign keys
        if (!Schema::hasTable('marty_nexgenrifle_rifle_builds')) {
            Schema::create('marty_nexgenrifle_rifle_builds', function(Blueprint $table) {
                $table->engine = 'InnoDB';
                $table->increments('id');
                $table->uuid('uuid')->unique()->nullable();
                $table->integer('user_id')->unsigned()->nullable();
                $table->integer('build_category_id')->unsigned();
                $table->string('title');
                $table->string('status')->default('draft');
                $table->decimal('total_cost', 10, 2)->nullable();
                $table->text('notes')->nullable();
                $table->json('metadata')->nullable();
                $table->timestamps();
            });
            
            // Add foreign key constraints separately with try-catch
            try {
                Schema::table('marty_nexgenrifle_rifle_builds', function(Blueprint $table) {
                    $table->foreign('user_id')
                          ->references('id')
                          ->on('users')
                          ->onDelete('cascade');
                });
            } catch (\Exception $e) {
                \Log::warning('Could not create user_id foreign key: ' . $e->getMessage());
            }
            
            try {
                Schema::table('marty_nexgenrifle_rifle_builds', function(Blueprint $table) {
                    $table->foreign('build_category_id')
                          ->references('id')
                          ->on('marty_nexgenrifle_build_categories')
                          ->onDelete('restrict');
                });
            } catch (\Exception $e) {
                \Log::warning('Could not create build_category_id foreign key: ' . $e->getMessage());
            }
        }
    }

    public function down()
    {
        Schema::dropIfExists('marty_nexgenrifle_rifle_builds');
        Schema::dropIfExists('marty_nexgenrifle_build_categories');
    }
}
