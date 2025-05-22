<?php
namespace Marty\AiSolutions\Updates;

use Schema;
use October\Rain\Database\Schema\Blueprint;
use October\Rain\Database\Updates\Migration;

class CreateAISessionsTable extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('marty_aisolutions_ai_sessions')) {
            Schema::create('marty_aisolutions_ai_sessions', function (Blueprint $table) {
                $table->engine = 'InnoDB';
                $table->increments('id');
                $table->string('code_session_id')->unique();
                $table->string('title')->nullable();
                $table->string('active_file')->nullable();
                $table->enum('status', ['open', 'closed', 'archived'])->default('open');
                $table->timestamps();
            });
        }
    }

    public function down()
    {
        if (Schema::hasTable('marty_aisolutions_ai_sessions')) {
            Schema::dropIfExists('marty_aisolutions_ai_sessions');
        }
    }
}
