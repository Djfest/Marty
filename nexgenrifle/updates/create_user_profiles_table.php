<?php namespace Marty\NexGenRifle\Updates;

use Schema;
use October\Rain\Database\Schema\Blueprint;
use October\Rain\Database\Updates\Migration;

/**
 * CreateUserProfilesTable Migration
 *
 * @link https://docs.octobercms.com/3.x/extend/database/structure.html
 */
class CreateUserProfilesTable extends Migration
{
    /**
     * up builds the migration
     */
    public function up()
    {
        Schema::create('marty_nexgenrifle_user_profiles', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->integer('user_id')->unsigned()->index();
            $table->string('expertise_level')->nullable();
            $table->text('bio')->nullable();
            $table->text('preferences')->nullable();
            $table->text('rifle_interests')->nullable();
            $table->text('social_links')->nullable();
            $table->string('avatar_url')->nullable();
            $table->timestamps();
            
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * down reverses the migration
     */
    public function down()
    {
        Schema::dropIfExists('marty_nexgenrifle_user_profiles');
    }
}
