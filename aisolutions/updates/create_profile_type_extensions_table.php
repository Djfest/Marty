<?php namespace Marty\AiSolutions\Updates;

use Schema;
use October\Rain\Database\Schema\Blueprint;
use October\Rain\Database\Updates\Migration;
use DB;

class CreateProfileTypeExtensionsTable extends Migration
{
    public function up()
    {
        // Temporarily disable foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS=0');

        if (!Schema::hasTable('marty_aisolutions_profile_type_extensions')) {
            Schema::create('marty_aisolutions_profile_type_extensions', function(Blueprint $table) {
                $table->engine = 'InnoDB';
                $table->increments('id');
                $table->unsignedInteger('profile_type_id');
                $table->boolean('ai_enabled')->default(false);
                $table->text('metadata')->nullable();
                $table->text('settings')->nullable();
                $table->text('description')->nullable();
                $table->timestamps();
                
                $table->foreign('profile_type_id', 'ai_profile_type_fk')
                    ->references('id')
                    ->on('marty_djfest_profiletypes')
                    ->onDelete('cascade');
            });
        }

        // Re-enable foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }

    public function down()
    {
        // Temporarily disable foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS=0');

        if (Schema::hasTable('marty_aisolutions_profile_type_extensions')) {
            // Drop the foreign key first
            Schema::table('marty_aisolutions_profile_type_extensions', function(Blueprint $table) {
                $table->dropForeign('ai_profile_type_fk');
            });
            Schema::dropIfExists('marty_aisolutions_profile_type_extensions');
        }

        // Re-enable foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }
}
