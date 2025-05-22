<?php namespace Marty\NexGenRifle\Updates;

use Schema;
use October\Rain\Database\Schema\Blueprint;
use October\Rain\Database\Updates\Migration;

class CreateProfileTables extends Migration
{
    public function up()
    {
        // Create user profiles table without foreign key first
        if (!Schema::hasTable('marty_nexgenrifle_user_profiles')) {
            Schema::create('marty_nexgenrifle_user_profiles', function(Blueprint $table) {
                $table->engine = 'InnoDB';
                $table->increments('id');
                $table->uuid('uuid')->unique()->nullable();
                // Use integer()->unsigned() to match the users table id column
                $table->integer('user_id')->unsigned();
                $table->string('display_name');
                $table->json('preferences')->nullable();
                $table->timestamps();
            });
            
            // Add foreign key constraint separately
            try {
                Schema::table('marty_nexgenrifle_user_profiles', function(Blueprint $table) {
                    $table->foreign('user_id')
                          ->references('id')
                          ->on('users')
                          ->onDelete('cascade');
                });
            }
            catch (\Exception $e) {
                // Log the error but continue with migration
                \Log::warning('Could not create foreign key: ' . $e->getMessage());
            }
        }
    }

    public function down()
    {
        // Drop tables in reverse order
        Schema::dropIfExists('marty_nexgenrifle_user_profiles');
    }
}
