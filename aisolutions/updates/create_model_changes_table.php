<?php namespace Marty\AiSolutions\Updates;

use Schema;
use October\Rain\Database\Schema\Blueprint;
use October\Rain\Database\Updates\Migration;
use Illuminate\Support\Facades\DB;

class CreateModelChangesTable extends Migration
{
    public function up()
    {
        // First, check if the table already exists
        if (!Schema::hasTable('marty_aisolutions_model_changes')) {
            // Create the table if it doesn't exist
            Schema::create('marty_aisolutions_model_changes', function($table)
            {
                $table->engine = 'InnoDB';
                $table->increments('id');
                
                // Explicit nullable for session_id
                $table->unsignedInteger('session_id')->nullable();
                
                $table->string('model_type');
                $table->unsignedInteger('model_id')->nullable();
                $table->string('field_name')->nullable();
                $table->text('old_value')->nullable();
                $table->text('new_value')->nullable();
                $table->string('change_type')->nullable(); // create, update, delete
                $table->text('metadata')->nullable();
                $table->boolean('is_applied')->default(false);
                $table->timestamp('applied_at')->nullable();
                $table->timestamps();
            });
        }
        
        // As a separate step, try to add the foreign key constraint if it doesn't exist
        // First, make sure the sessions table exists
        if (!Schema::hasTable('marty_aisolutions_sessions')) {
            // Instead of throwing an exception, just log a warning and continue
            \Log::warning("Warning: Cannot add foreign key because marty_aisolutions_sessions table does not exist. Foreign key will need to be added manually later.");
            echo "Warning: marty_aisolutions_sessions table not found. Foreign key constraint was not added.\n";
            return; // Exit the migration, but consider it successful
        }
        
        // Check if the foreign key already exists
        $hasConstraint = false;
        $constraints = DB::select("SHOW CREATE TABLE marty_aisolutions_model_changes");
        if (!empty($constraints) && isset($constraints[0])) {
            $createTableSql = $constraints[0]->{'Create Table'} ?? '';
            $hasConstraint = strpos($createTableSql, 'marty_aisolutions_model_changes_session_id_foreign') !== false;
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
            } catch (\Exception $e) {
                // Log the error but don't fail the migration
                \Log::error('Failed to add foreign key constraint: ' . $e->getMessage());
                echo "Warning: Could not add foreign key constraint. See log for details.\n";
            }
        }
    }

    public function down()
    {
        // Try to drop the foreign key first if it exists
        try {
            Schema::table('marty_aisolutions_model_changes', function($table) {
                $table->dropForeign(['session_id']);
            });
        } catch (\Exception $e) {
            // Ignore if the foreign key doesn't exist
        }

        Schema::dropIfExists('marty_aisolutions_model_changes');
    }
}
