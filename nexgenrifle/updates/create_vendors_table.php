<?php namespace Marty\NexGenRifle\Updates;

use Schema;
use October\Rain\Database\Schema\Blueprint;
use October\Rain\Database\Updates\Migration;

/**
 * CreateVendorsTable Migration
 *
 * @link https://docs.octobercms.com/3.x/extend/database/structure.html
 */
return new class extends Migration
{
    /**
     * up builds the migration
     */
    public function up()
    {
        // Temporarily disable foreign key checks
        Schema::disableForeignKeyConstraints();
        
        // Drop the incorrectly named table if it exists
        if (Schema::hasTable('nexgenrifle_nexgenrifle_vendors')) {
            Schema::dropIfExists('nexgenrifle_nexgenrifle_vendors');
        }
        
        // Create the correctly named table
        if (!Schema::hasTable('marty_nexgenrifle_vendors')) {
            Schema::create('marty_nexgenrifle_vendors', function(Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('slug')->unique();
                $table->string('base_url')->nullable();
                $table->string('affiliate_url')->nullable();
                $table->boolean('affiliate_program')->default(false);
                $table->boolean('is_affiliate')->default(false);
                $table->boolean('is_enabled')->default(true);
                $table->string('logo_url')->nullable();
                $table->json('metadata')->nullable();
                $table->text('description')->nullable();
                $table->string('website_url')->nullable();
                $table->string('contact_name')->nullable();
                $table->string('contact_email')->nullable();
                $table->string('contact_phone')->nullable();
                $table->string('address_street')->nullable();
                $table->string('address_city')->nullable();
                $table->string('address_state')->nullable();
                $table->string('address_zip')->nullable();
                $table->string('address_country')->nullable();
                $table->boolean('is_active')->default(true);
                $table->timestamps();
            });
        }
        
        // Re-enable foreign key checks
        Schema::enableForeignKeyConstraints();
    }

    /**
     * down reverses the migration
     */
    public function down()
    {
        // Temporarily disable foreign key checks
        Schema::disableForeignKeyConstraints();
        
        if (Schema::hasTable('marty_nexgenrifle_vendors')) {
            Schema::dropIfExists('marty_nexgenrifle_vendors');
        }
        
        // Re-enable foreign key checks
        Schema::enableForeignKeyConstraints();
    }
};
