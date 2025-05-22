<?php namespace Marty\NexGenRifle\Updates;

use Schema;
use October\Rain\Database\Schema\Blueprint;
use October\Rain\Database\Updates\Migration;

class CreateProfiletypesTable extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('marty_nexgenrifle_profile_types')) {
            Schema::create('marty_nexgenrifle_profile_types', function(Blueprint $table) {
                $table->engine = 'InnoDB';
                $table->increments('id');
                $table->uuid('uuid')->unique()->nullable(); 
                $table->string('access')->nullable();
                $table->string('api_endpoint')->nullable();
                $table->json('api_help')->nullable();
                $table->json('blog_article')->nullable();
                $table->string('class')->nullable();
                $table->json('common')->nullable();
                $table->string('controller_path')->nullable();
                $table->json('content_types')->nullable();
                $table->json('conversation_analysis')->nullable();
                $table->text('description')->nullable();
                $table->json('error_guidance')->nullable();
                $table->json('fillable_fields')->nullable();
                $table->json('required_fields')->nullable();
                $table->json('sensitive_fields')->nullable();
                $table->string('icon')->nullable();
                $table->boolean('is_active')->default(true);
                $table->boolean('is_featured')->default(false);
                $table->boolean('is_default')->default(false);
                $table->boolean('ai_enabled')->default(false);
                $table->json('logging_details')->nullable();
                $table->string('model_class')->nullable();
                $table->string('name');
                $table->string('namespace')->nullable();
                $table->string('type')->nullable();
                $table->text('prompt_instructions')->nullable();
                $table->json('query_params')->nullable();
                $table->json('related_files')->nullable();
                $table->json('relationships')->nullable();
                $table->json('response_structure')->nullable();
                $table->string('slug')->nullable();
                $table->integer('order')->default(0);
                $table->string('status')->nullable();
                $table->json('status_groups')->nullable();
                $table->string('tone')->nullable();
                $table->integer('user_id')->unsigned()->nullable();
                $table->string('migration_path')->nullable();
                $table->string('fields_path')->nullable();
                $table->string('columns_path')->nullable();
                $table->string('api_version')->nullable();
                $table->json('knowledgebase')->nullable();
                $table->json('api_methods')->nullable();
                $table->json('methods')->nullable();
                $table->json('sensitive_data')->nullable();
                $table->json('metadata')->nullable();
                $table->timestamps();
                $table->softDeletes();
            });
        }
    }

    public function down()
    {
        Schema::dropIfExists('marty_nexgenrifle_profile_types');
    }
}
