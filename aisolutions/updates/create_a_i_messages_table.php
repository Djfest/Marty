<?php
namespace Marty\AiSolutions\Updates;

use Schema;
use October\Rain\Database\Schema\Blueprint;
use October\Rain\Database\Updates\Migration;
use October\Rain\Support\Facades\DB;

class CreateAIMessagesTable extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('marty_aisolutions_ai_messages')) {
            Schema::create('marty_aisolutions_ai_messages', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->unsignedInteger('aisession_id');
            $table->foreign('aisession_id')->references('id')->on('marty_aisolutions_ai_sessions')->cascadeOnDelete();
            $table->string('context_key')->nullable();
            $table->string('file_path')->nullable();
            $table->string('source')->nullable();
            $table->string('author')->nullable();
            $table->text('message');
            $table->string('type')->nullable();
            $table->string('context')->nullable();
            $table->json('code_references')->nullable();
            $table->json('metadata')->nullable();
            $table->enum('status', ['pending', 'in_progress', 'completed', 'failed'])->default('pending');
            $table->timestamps();
            });
        }
    }

    public function down()
    {
        if (Schema::hasTable('marty_aisolutions_ai_messages')) {
            Schema::dropIfExists('marty_aisolutions_ai_messages');
        }
    }
}
