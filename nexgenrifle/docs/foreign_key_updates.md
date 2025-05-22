# Foreign Key Updates

Here's a list of model and migration files that need to be updated to ensure foreign keys are nullable:

## Model Files

### AiMessage Model
```php
// filepath: c:\Users\gifte\Documents\NEXGENRIFLE\plugins\marty\aisolutions\models\AiMessage.php

// ...existing code...
public $rules = [
    // Change from 'required|integer|exists:...'
    'session_id' => 'nullable|integer|exists:marty_aisolutions_sessions,id',
    'content' => 'required'
];
// ...existing code...
```

### ModelChange Model
```php
// filepath: c:\Users\gifte\Documents\NEXGENRIFLE\plugins\marty\aisolutions\models\ModelChange.php

// ...existing code...
public $rules = [
    // Change from 'required|integer|exists:...'
    'session_id' => 'nullable|integer|exists:marty_aisolutions_sessions,id',
    // Other fields remain the same
];
// ...existing code...
```

### AiKnowledgeBase Model
```php
// filepath: c:\Users\gifte\Documents\NEXGENRIFLE\plugins\marty\aisolutions\models\AiKnowledgeBase.php

// ...existing code...
public $rules = [
    // Update any foreign key validation rules to be nullable
    'profile_type_id' => 'nullable|integer|exists:marty_nexgenrifle_profile_types,id',
    // Other fields remain the same
];
// ...existing code...
```

### ModelGraph Model
```php
// filepath: c:\Users\gifte\Documents\NEXGENRIFLE\plugins\marty\aisolutions\models\ModelGraph.php

// ...existing code...
public $rules = [
    // Make any foreign key validation nullable
    'parent_id' => 'nullable|integer',
    'model_id' => 'nullable|integer',
    // Other fields remain the same
];
// ...existing code...
```

### ProfileTypeExtension Model
```php
// filepath: c:\Users\gifte\Documents\NEXGENRIFLE\plugins\marty\aisolutions\models\ProfileTypeExtension.php

// ...existing code...
public $rules = [
    // Change from 'required|integer|exists:...'
    'profile_type_id' => 'nullable|integer|exists:marty_nexgenrifle_profile_types,id',
    // Other fields remain the same
];
// ...existing code...
```

## Migration Files

### Create AI Messages Table
```php
// filepath: c:\Users\gifte\Documents\NEXGENRIFLE\plugins\marty\aisolutions\updates\create_messages_table.php

// ...existing code...
Schema::create('marty_aisolutions_messages', function (Blueprint $table) {
    // ...existing code...
    $table->unsignedInteger('session_id')->nullable();
    // ...existing code...
    
    // If foreign key exists, ensure it uses onDelete('set null')
    $table->foreign('session_id')
        ->references('id')
        ->on('marty_aisolutions_sessions')
        ->onDelete('set null');
});
// ...existing code...
```

### Create Model Changes Table
```php
// filepath: c:\Users\gifte\Documents\NEXGENRIFLE\plugins\marty\aisolutions\updates\create_model_changes_table.php

// ...existing code...
Schema::create('marty_aisolutions_model_changes', function($table) {
    // ...existing code...
    $table->unsignedInteger('session_id')->nullable();
    // ...existing code...
    
    // Update foreign key constraint to use set null
    $table->foreign('session_id')
        ->references('id')
        ->on('marty_aisolutions_sessions')
        ->onDelete('set null');
});
// ...existing code...
```

### Create Model Graphs Table
```php
// filepath: c:\Users\gifte\Documents\NEXGENRIFLE\plugins\marty\aisolutions\updates\create_model_graphs_table.php

// ...existing code...
Schema::create('marty_aisolutions_model_graphs', function (Blueprint $table) {
    // ...existing code...
    $table->unsignedInteger('parent_id')->nullable();
    $table->unsignedInteger('model_id')->nullable();
    // ...existing code...
    
    // Ensure any foreign keys use set null
});
// ...existing code...
```

### Create ProfileType Extensions Table
```php
// filepath: c:\Users\gifte\Documents\NEXGENRIFLE\plugins\marty\aisolutions\updates\create_profiletype_extensions_table.php

// ...existing code...
Schema::create('marty_aisolutions_profiletype_extensions', function (Blueprint $table) {
    // ...existing code...
    $table->unsignedInteger('profile_type_id')->nullable();
    // ...existing code...
    
    // Update foreign key to use set null
    $table->foreign('profile_type_id')
        ->references('id')
        ->on('marty_nexgenrifle_profile_types')
        ->onDelete('set null');
});
// ...existing code...
```

### Create Knowledge Base Table
```php
// filepath: c:\Users\gifte\Documents\NEXGENRIFLE\plugins\marty\aisolutions\updates\create_knowledge_base_table.php

// ...existing code...
Schema::create('marty_aisolutions_knowledge_base', function(Blueprint $table) {
    // ...existing code...
    $table->unsignedInteger('profile_type_id')->nullable();
    // ...existing code...
    
    // Update foreign key to use set null
    $table->foreign('profile_type_id')
        ->references('id')
        ->on('marty_nexgenrifle_profile_types')
        ->onDelete('set null');
});
// ...existing code...
```

## General Best Practices for Foreign Keys

1. Always declare foreign key columns as nullable in migrations
2. Use onDelete('set null') for nullable foreign keys
3. Only use onDelete('cascade') when you want child records to be deleted with parent
4. Move validation of required relationships to the model validation rules when needed
5. Consider adding fallbacks for cases where related records don't exist

By implementing these changes, we'll reduce database migration issues and make our data model more flexible.