<?php namespace Marty\AiSolutions\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;
use October\Rain\Support\Facades\DB;

class AddForeignKeys extends Migration
{
    public function up()
    {
        // Make sure both tables exist before trying to add foreign keys
        if (!Schema::hasTable('marty_aisolutions_sessions') || !Schema::hasTable('marty_aisolutions_model_changes')) {
            echo "Not all required tables exist. Skipping foreign key creation.\n";
            return;
        }
        
        // Check if the foreign key already exists
        $hasConstraint = false;
        try {
            $constraints = DB::select("SHOW CREATE TABLE marty_aisolutions_model_changes");
            if (!empty($constraints) && isset($constraints[0])) {
                $createTableSql = $constraints[0]->{'Create Table'} ?? '';
                $hasConstraint = strpos($createTableSql, 'marty_aisolutions_model_changes_session_id_foreign') !== false;
            }
        } catch (\Exception $e) {
            echo "Error checking for existing constraint: " . $e->getMessage() . "\n";
        }
        
        // Add the foreign key if it doesn't exist
        if (!$hasConstraint) {
            try {
                Schema::table('marty_aisolutions_model_changes', function($table) {
                    $table->foreign('session_id')
                        ->references('id')
                        ->on('marty_aisolutions_sessions')
                        ->onDelete('set null');
                });
                echo "Foreign key constraint added successfully.\n";
            } catch (\Exception $e) {
                echo "Error adding foreign key constraint: " . $e->getMessage() . "\n";
            }
        } else {
            echo "Foreign key constraint already exists.\n";
        }
    }

    public function down()
    {
        // Only try to drop the foreign key if both tables exist
        if (Schema::hasTable('marty_aisolutions_sessions') && Schema::hasTable('marty_aisolutions_model_changes')) {
            try {
                Schema::table('marty_aisolutions_model_changes', function($table) {
                    $table->dropForeign(['session_id']);
                });
            } catch (\Exception $e) {
                echo "Error dropping foreign key: " . $e->getMessage() . "\n";
            }
        }
    }
}
