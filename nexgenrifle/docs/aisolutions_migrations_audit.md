# AiSolutions Plugin Migrations Audit

## Summary of Migrations in `plugins\marty\aisolutions\updates`

Here are the migrations that need to be reviewed to ensure foreign key fields are properly nullable:

### 1. create_sessions_table.php
```php
// Changes needed:
// - Ensure user_id field is nullable

Schema::create('marty_aisolutions_sessions', function($table) {
    // ...existing code...
    $table->integer('user_id')->unsigned()->nullable(); // Make sure it's nullable
    // ...existing code...
});
```

### 2. create_model_changes_table.php
```php
// Changes needed:
// - Ensure session_id is nullable
// - Use set null for foreign key constraint

Schema::create('marty_aisolutions_model_changes', function($table) {
    // ...existing code...
    $table->integer('session_id')->unsigned()->nullable(); // Make sure it's nullable
    // ...existing code...
});

// Foreign key constraint should use onDelete('set null')
$table->foreign('session_id')
    ->references('id')
    ->on('marty_aisolutions_sessions')
    ->onDelete('set null');
```

### 3. create_messages_table.php
```php
// Changes needed:
// - Ensure session_id is nullable
// - Use set null for foreign key constraint

Schema::create('marty_aisolutions_messages', function($table) {
    // ...existing code...
    $table->unsignedInteger('session_id')->nullable(); // Make sure it's nullable
    // ...existing code...
});

// Foreign key constraint should use onDelete('set null')
$table->foreign('session_id')
    ->references('id')
    ->on('marty_aisolutions_sessions')
    ->onDelete('set null');
```

### 4. create_model_graphs_table.php
```php
// Changes needed:
// - Ensure parent_id and model_id are nullable

Schema::create('marty_aisolutions_model_graphs', function($table) {
    // ...existing code...
    $table->unsignedInteger('parent_id')->nullable(); // Make sure it's nullable
    $table->unsignedInteger('model_id')->nullable(); // Make sure it's nullable
    // ...existing code...
});
```

### 5. create_profiletype_extensions_table.php
```php
// Changes needed:
// - Ensure profile_type_id is nullable
// - Use set null for foreign key constraint

Schema::create('marty_aisolutions_profiletype_extensions', function($table) {
    // ...existing code...
    $table->unsignedInteger('profile_type_id')->nullable(); // Make sure it's nullable
    // ...existing code...
});

// Foreign key constraint should use onDelete('set null')
$table->foreign('profile_type_id')
    ->references('id')
    ->on('marty_nexgenrifle_profile_types')
    ->onDelete('set null');
```

### 6. create_ai_knowledge_base_table.php
```php
// Changes needed:
// - Ensure profile_type_id is nullable
// - Use set null for foreign key constraint

Schema::create('marty_aisolutions_knowledge_base', function($table) {
    // ...existing code...
    $table->unsignedInteger('profile_type_id')->nullable(); // Make sure it's nullable
    // ...existing code...
});

// Foreign key constraint should use onDelete('set null')
$table->foreign('profile_type_id')
    ->references('id')
    ->on('marty_nexgenrifle_profile_types')
    ->onDelete('set null');
```

### 7. create_ai_sessions_table.php
```php
// Changes needed:
// No changes needed if this table doesn't contain foreign keys
```

### 8. create_ai_messages_table.php
```php
// Changes needed:
// - Ensure aisession_id is nullable
// - Use set null for foreign key constraint

Schema::create('marty_aisolutions_ai_messages', function($table) {
    // ...existing code...
    $table->unsignedInteger('aisession_id')->nullable(); // Make sure it's nullable
    // ...existing code...
});

// Foreign key constraint should use onDelete('set null')
$table->foreign('aisession_id')
    ->references('id')
    ->on('marty_aisolutions_ai_sessions')
    ->onDelete('set null');
```

### 9. create_ai_solutions_table.php
```php
// Changes needed:
// Ensure any foreign key fields are nullable (if present)
Schema::create('marty_aisolutions_ai_solutions', function($table) {
    // ...existing code...
    // Check for foreign key fields and make them nullable
    // ...existing code...
});
```

## Generic Update Script

Here's a PHP script that could be created to fix any existing tables by modifying foreign key fields to be nullable:

```php
// filepath: c:\Users\gifte\Documents\NEXGENRIFLE\plugins\marty\aisolutions\updates\update_foreign_keys_to_nullable.php
<?php namespace Marty\AiSolutions\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;
use Illuminate\Support\Facades\DB;

class UpdateForeignKeysToNullable extends Migration
{
    public function up()
    {
        // Disable foreign key checks temporarily
        DB::statement('SET FOREIGN_KEY_CHECKS=0');

        // Update foreign keys in model_changes table
        if (Schema::hasTable('marty_aisolutions_model_changes') && 
            Schema::hasColumn('marty_aisolutions_model_changes', 'session_id')) {
            
            // Drop foreign key if it exists
            $this->dropForeignKeyIfExists('marty_aisolutions_model_changes', 'session_id');
            
            // Modify column to be nullable
            Schema::table('marty_aisolutions_model_changes', function($table) {
                $table->unsignedInteger('session_id')->nullable()->change();
            });
            
            // Add foreign key with set null behavior
            if (Schema::hasTable('marty_aisolutions_sessions')) {
                Schema::table('marty_aisolutions_model_changes', function($table) {
                    $table->foreign('session_id')
                        ->references('id')
                        ->on('marty_aisolutions_sessions')
                        ->onDelete('set null');
                });
            }
        }

        // Update similar pattern for other tables with foreign keys
        // ...

        // Re-enable foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }

    public function down()
    {
        // No rollback needed as making columns nullable is a safe operation
    }
    
    protected function dropForeignKeyIfExists($table, $column)
    {
        try {
            $foreignKeys = DB::select(
                "SELECT CONSTRAINT_NAME FROM information_schema.KEY_COLUMN_USAGE
                WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = '{$table}'
                AND COLUMN_NAME = '{$column}' AND REFERENCED_TABLE_NAME IS NOT NULL"
            );
            
            if (!empty($foreignKeys)) {
                foreach ($foreignKeys as $foreignKey) {
                    $constraintName = $foreignKey->CONSTRAINT_NAME;
                    Schema::table($table, function($table) use ($constraintName) {
                        $table->dropForeign($constraintName);
                    });
                }
            }
        } catch (\Exception $e) {
            // Log error but continue
            \Log::warning("Could not drop foreign key on {$table}.{$column}: " . $e->getMessage());
        }
    }
}
```

This documentation provides a comprehensive review of all migrations in the AiSolutions plugin and highlights the necessary changes to ensure proper handling of foreign keys as nullable fields.