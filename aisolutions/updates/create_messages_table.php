<?php namespace Marty\AiSolutions\Updates;

use Schema;
use October\Rain\Database\Schema\Blueprint;
use October\Rain\Database\Updates\Migration;
use Illuminate\Support\Facades\DB;

class CreateMessagesTable extends Migration
{
    public function up()
    {
        // Disable foreign key checks temporarily
        DB::statement('SET FOREIGN_KEY_CHECKS=0');

        if (!Schema::hasTable('marty_aisolutions_messages')) {
            Schema::create('marty_aisolutions_messages', function (Blueprint $table) {
                $table->engine = 'InnoDB';
                $table->increments('id');
                
                // Ensure session_id is explicitly nullable
                $table->unsignedInteger('session_id')->nullable();
                
                $table->string('role')->nullable(); // user, assistant, system, etc.
                $table->text('content');
                $table->boolean('is_from_ai')->default(false);
                $table->json('metadata')->nullable();
                $table->timestamps();
                
                // Only add foreign key if sessions table exists
                if (Schema::hasTable('marty_aisolutions_sessions')) {
                    // We'll try to add the foreign key constraint later
                }
            });
        }
        
        // Now try to add the foreign key constraint separately
        if (Schema::hasTable('marty_aisolutions_messages') && Schema::hasTable('marty_aisolutions_sessions')) {
            try {
                // Check if constraint already exists
                $constraintExists = false;
                $constraints = DB::select("SHOW CREATE TABLE marty_aisolutions_messages");
                if (!empty($constraints) && isset($constraints[0])) {
                    $createTableSql = isset($constraints[0]->{'Create Table'}) ? $constraints[0]->{'Create Table'} : '';
                    $constraintExists = strpos($createTableSql, 'marty_aisolutions_messages_session_id_foreign') !== false;
                }
                
                if (!$constraintExists) {
                    Schema::table('marty_aisolutions_messages', function (Blueprint $table) {
                        // Use onDelete('set null') instead of cascade since session_id is nullable
                        $table->foreign('session_id')
                            ->references('id')
                            ->on('marty_aisolutions_sessions')
                            ->onDelete('set null');
                    });
                }
            } catch (\Exception $e) {
                // Log error but continue migration
                \Log::error('Failed to add foreign key constraint to messages table: ' . $e->getMessage());
                echo "Warning: Could not add foreign key constraint to messages table: " . $e->getMessage() . "\n";
            }
        } else {
            echo "Warning: Cannot add foreign key constraint - one or both required tables don't exist yet.\n";
        }

        // Re-enable foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }

    public function down()
    {
        // Disable foreign key checks temporarily
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        
        if (Schema::hasTable('marty_aisolutions_messages')) {
            try {
                // Drop foreign key first if it exists
                Schema::table('marty_aisolutions_messages', function (Blueprint $table) {
                    $table->dropForeign(['session_id']);
                });
            } catch (\Exception $e) {
                // Ignore if constraint doesn't exist
                echo "Note: Foreign key constraint might not have existed: " . $e->getMessage() . "\n";
            }
            
            // Then drop the table
            Schema::dropIfExists('marty_aisolutions_messages');
        }
        
        // Re-enable foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }
}
