<?php namespace Marty\NexGenRifle\Updates;

use Schema;
use October\Rain\Database\Schema\Blueprint;
use October\Rain\Database\Updates\Migration;

/**
 * Migration Helper Template
 * 
 * Copy this template when creating new migrations that need to create tables
 * to avoid "table already exists" errors.
 */
class MigrationHelperTemplate extends Migration
{
    public function up()
    {
        // Template for safely creating tables
        if (!Schema::hasTable('table_name')) {
            Schema::create('table_name', function(Blueprint $table) {
                $table->engine = 'InnoDB';
                $table->increments('id');
                // Add your columns here
                $table->timestamps();
            });
        }

        // For adding columns to existing tables
        if (Schema::hasTable('existing_table') && !Schema::hasColumn('existing_table', 'new_column')) {
            Schema::table('existing_table', function(Blueprint $table) {
                $table->string('new_column')->nullable();
            });
        }
    }

    public function down()
    {
        // For columns added to existing tables
        if (Schema::hasTable('existing_table') && Schema::hasColumn('existing_table', 'new_column')) {
            Schema::table('existing_table', function(Blueprint $table) {
                $table->dropColumn('new_column');
            });
        }
        
        // For tables
        Schema::dropIfExists('table_name');
    }
}
