<?php
namespace Marty\AiSolutions\Updates;

use Schema;
use October\Rain\Database\Schema\Blueprint;
use October\Rain\Database\Updates\Migration;

class AddUserIdToModelChanges extends Migration
{
        public function up()
        {
                if (!Schema::hasColumn('marty_aisolutions_model_changes', 'user_id')) {
                        Schema::table('marty_aisolutions_model_changes', function (Blueprint $table) {
                                $table->integer('user_id')->unsigned()->nullable()->after('file_type');
            $table->foreign('user_id', 'mc_add_user_id_fk')->references('id')->on('backend_users')->onDelete('set null');
                        });
                }
        }

        public function down()
        {
                if (Schema::hasColumn('marty_aisolutions_model_changes', 'user_id')) {
                        Schema::table('marty_aisolutions_model_changes', function (Blueprint $table) {
                                $table->dropForeign(['user_id']);
                                $table->dropColumn('user_id');
                        });
                }
        }
}
