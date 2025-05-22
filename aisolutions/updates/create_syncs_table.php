<?php namespace Marty\AiSolutions\Updates;

use Schema;
use October\Rain\Database\Schema\Blueprint;
use October\Rain\Database\Updates\Migration;

class CreateSyncsTable extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('marty_aisolutions_syncs')) {
            Schema::create('marty_aisolutions_syncs', function(Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->string('namespace');
            $table->string('model_name');
            $table->string('file_path')->unique();
            $table->string('file_hash')->nullable();
            $table->string('file_type')->default('model');
            $table->integer('profile_type_id')->nullable();
            $table->string('sync_status')->default('pending');
            $table->timestamp('last_synced_at')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();
            });
        }
    }

    public function down()
    {
        if (Schema::hasTable('marty_aisolutions_syncs')) {
            Schema::dropIfExists('marty_aisolutions_syncs');
        }
    }
}