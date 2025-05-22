<?php namespace Marty\NexGenRifle\Updates;

use Schema;
use October\Rain\Database\Schema\Blueprint;
use October\Rain\Database\Updates\Migration;

/**
 * CreateSuppliersTable Migration
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
        Schema::create('marty_nexgenrifle_suppliers', function(Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique()->nullable();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('base_url')->nullable();
            $table->string('affiliate_url')->nullable();
            $table->boolean('affiliate_program')->default(false);
            $table->boolean('is_affiliate')->default(false);
            $table->boolean('is_enabled')->default(true);
            $table->string('logo_url')->nullable(); // For storing URL if logo is external, or use $attachOne for local files
            $table->json('metadata')->nullable(); // Convert from text to json
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
            $table->softDeletes();
        });
    }

    /**
     * down reverses the migration
     */
    public function down()
    {
        Schema::dropIfExists('marty_nexgenrifle_suppliers');
    }
};
