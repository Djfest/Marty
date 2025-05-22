<?php namespace Marty\AiSolutions\Updates;

use Schema;
use October\Rain\Database\Schema\Blueprint;
use October\Rain\Database\Updates\Migration;
use Illuminate\Support\Facades\DB; // Changed to direct Laravel namespace

class CreateAiKnowledgeBaseTable extends Migration
{
    public function up()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0');

        if (!Schema::hasTable('marty_aisolutions_knowledge_base')) {
            Schema::create('marty_aisolutions_knowledge_base', function(Blueprint $table) {
                $table->increments('id');
                $table->string('model_name');
                $table->string('type', 50); // field, relation, endpoint, etc.
                $table->string('key_name');
                $table->text('value');
                $table->text('searchable_text'); // Flattened for LIKE %search%
            $table->unsignedInteger('profile_type_id')->nullable();
            $table->timestamps();

            $table->index(['model_name', 'type']);
            $table->index('key_name');
            
            $table->foreign('profile_type_id', 'kb_profile_type_fk')
                  ->references('id')
                  ->on('marty_djfest_profiletypes')
                  ->onDelete('set null');            });
        }

        DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }

    public function down()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0');

        if (Schema::hasTable('marty_aisolutions_knowledge_base')) {
            Schema::table('marty_aisolutions_knowledge_base', function(Blueprint $table) {
                $table->dropForeign('kb_profile_type_fk');
            });
            Schema::dropIfExists('marty_aisolutions_knowledge_base');
        }

        DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }
}
