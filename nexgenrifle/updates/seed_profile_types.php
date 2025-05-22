<?php
namespace Marty\Nexgenrifle\Updates;

use Seeder;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
// use Marty\Nexgenrifle\Traits\SmartSeederTrait;

class SeedProfileTypes extends Seeder
{
    // use SmartSeederTrait;

    protected $dynamicFieldDetection = false;
    protected $enableCSVexport = true;
    protected $enableCustomMigrationPath = true;
    protected $promptUserBeforeSeeding = false;
    protected $interactiveDebug = false;
    protected $migrationPath = 'database/migrations/2023_10_01_create_profile_types_table.php';
    protected $modelName = 'ProfileType';

 
    // New property to define boolean fields
    protected $booleanFields = ['is_active', 'is_featured'];

    protected $requiredFields = [];
    protected $jsonFields = [];
    protected $additionalFields = [];
    protected $integerFields = []; // ✅ Add this line

    protected
    $types = [
        'users' => [
            'namespace' => 'rainlab', // Plugin namespace
            'name' => 'User', // Model name (used for references, especially by ChatGPT and API resolution)
            'type' => 'user', // some profiletypes are primaryily used for users and some are used for resources and some are used for both this can help us with determining different things like what the model is used for and what the model is used for in the system 
            'access' => 'restricted', // Access level for this profile type
            'icon' => 'icon-user', // Icon class for display
            'description' => 'Users in Michigan, connect with local events, artists, and communities to stay engaged with the music scene. Join our platform today to find opportunities in Detroit, Grand Rapids, Ann Arbor, and beyond!', // Description of users in context of our platform and overall music scene will also helo contextualize the conversation analysis so we'll want to improve this description
            'conversation_analysis' => [ // Data for analyzing user conversations (NLP tasks, contextualization)
                'goals' => ['Connect with others', 'Share experiences', 'Discover new music and events', 'Engage in discussions'],//thing the profiletype might like to leverage by using our system
                'strategies' => ['Be respectful and inclusive', 'Share relevant information and insights', 'Participate actively in community forums', 'Follow and connect with interesting profiles'],//ways for chatgpt to understand how to respond to users
                'keywords' => ['community', 'music', 'events', 'connection', 'sharing', 'discussion', 'discovery', 'engagement'] // Keywords that will help with seo purposes and chatGPT to understand the context of the model as well
            ],
            'blog_article' => [ // Information for generating blog articles (contextualization) but can also be used for generating prompts for chatGPT
                'topics' => ['Building a Vibrant Music Community Online', 'Connecting with Artists and Fans Through Digital Platforms', 'Discovering New Music and Events in Michigan'],//topics that the blog article might cover and ways this profiletype might be used in the system and connections for the community
                'target_audience' => ['Users', 'Artists', 'Event organizers', 'General public'],//who the blog article is targeted at
                'writing_style' => 'Engaging, informative, community-focused'
            ],
            'api_help' => [ // API documentation and usage examples (for developers and ChatGPT)
                'documentation_links' => ['/api/v2/users'], // Consider updating this to reflect dynamic routing or preferred endpoint
                'example_calls' => ['GET /api/v2/users/{id}', 'POST /api/v2/users'], // Consider updating this to reflect dynamic routing or preferred endpoint will and should match the key in the type array and making it the most likely reference to the model
                'common_errors' => ['404 Not Found', '401 Unauthorized']
            ],
            'tone' => 'Friendly, inclusive, enthusiastic', // Desired tone for communications related to users (contextualization)
            'relationships' => [ // key Relationships with other profile types
                'Event' => 'Attending events, discovering new opportunities',
                'Artist' => 'Following artists, engaging with content'
            ],
            'prompt_instructions' => "Focus on the role of users in building a vibrant and engaged community on the platform. Use a friendly and inclusive tone. Include keywords related to community interaction, content sharing, and event discovery. Provide specific examples of how users can connect with others, share their experiences, and discover new opportunities. Emphasize the importance of respectful and inclusive communication, and encourage active participation in community forums and discussions. Use clear and concise language, incorporate specific keywords, provide context and examples, encourage creativity and engagement, and regularly review and update the instructions.",//instructions for chatGPT to generate prompts for the model
            'fillable_fields' => [ // List of fillable fields for the User model (data validation, API input)
                "id",
                "is_guest",
                "is_mail_blocked",
                "first_name",
                "last_name",
                "username",
                "email",
                "notes",
                "primary_group_id",
                "created_ip_address",
                "last_ip_address",
                "banned_reason",
                "banned_at",
                "activated_at",
                "two_factor_confirmed_at",
                "last_seen",
                "deleted_at",
                "created_at",
                "updated_at"
            ],
            'content_types' => ["User profiles", "Community discussions", "Event reviews"], // Types of content associated with users
            'model_class' => "RainLab\User\Models\User",  // Fully qualified model class name
            'api_endpoint' => "/api/v2/user", // API endpoint for users (adjust as needed, or remove if using dynamic routing exclusively) - Updated based on discussion
            'query_params' => ["sort" => "created_at desc", "with" => ["posts"]], // Default query parameters for API requests
            'response_structure' => [ // Structure of the API response
                'rainlab-user' => [ // Updated based on example API response
                    'id',
                    'is_guest',
                    'is_mail_blocked',
                    'first_name',
                    'last_name',
                    'username',
                    'email',
                    'notes',
                    'primary_group_id',
                    'created_ip_address',
                    'banned_reason',
                    'banned_at',
                    'activated_at',
                    'last_seen',
                    'deleted_at',
                    'created_at',
                    'updated_at',
                    'posts' => []
                ]
            ],
            'error_guidance' => [ // Guidance for handling errors (for API and ChatGPT)
                'USER_NOT_FOUND' => "The requested user was not found. Please check the user ID.",
                'INVALID_CREDENTIALS' => "The provided login credentials are incorrect."
            ],
            'logging_details' => [ // Information for logging user-related actions
                'key_relationships' => [
                    'posts' => 'Has-many relationship with Posts model (blog posts)',
                ],
                'data_validation' => [
                    'email' => 'Must be a valid email address',
                    'password' => 'Must meet password complexity requirements',
                ],
            ],
            'common' => ["user", "users", "person", "people", "community", "communities", "friends", "followers", "squad", "team", "individual", "individuals", "member", "members", "userprofile", "userprofiles"], // Common terms associated with users (for model resolution, contextualization)
            'related_files' => [
                "plugins/rainlab/user/updates/create_users_table.php", // Migration for the users table
                "plugins/rainlab/user/updates/version.yaml", // Version file for the RainLab User plugin
                "plugins/rainlab/user/models/User.php", // The User model itself
                "plugins/rainlab/user/models/user/fields.yaml", // Fields definition for User model (if exists)
                "plugins/rainlab/user/models/user/columns.yaml", // Columns definition for User model (if exists)
                "plugins/rainlab/user/controllers/Users.php", // The User controller
                "plugins/rainlab/user/components/UserForm.php", // Example User component (adjust as needed)
                "plugins/rainlab/user/Plugin.php", // RainLab User plugin file
                "plugins/rainlab/user/routes.php", // RainLab User routes file (if applicable)
                "plugins/rainlab/user/lang/en/lang.php", // Language file for the RainLab User plugin
                "plugins/marty/nexgenrifle/models/ProfileType.php", // The ProfileType model
                "plugins/marty/nexgenrifle/updates/create_profiletypes_table.php", // The ProfileType migration
                "plugins/marty/nexgenrifle/updates/SeedProfileTypes.php", // The ProfileType seeder
                "plugins/marty/nexgenrifle/controllers/Api.php", // The API controller (important for API interactions)
                "plugins/marty/nexgenrifle/models/profiletype/fields.yaml", // Fields definition for ProfileType
                "plugins/marty/nexgenrifle/models/profiletype/columns.yaml", // Columns definition for ProfileType
                "plugins/marty/nexgenrifle/components/ProfileType.php",  // The ProfileType component
                "plugins/marty/nexgenrifle/lang/en/lang.php", // Language file for the Marty Nexgenrifle plugin
            ], // Related files (for developers and ChatGPT) including components, model field.yaml, column.yaml, migration files, and other necessary files according to the namespace. These will help ChatGPT navigate the knowledgebase and solve errors in our environment.
            'controller_path' => 'Rainlab\User\Controllers\User', // Path to the controller (Updated to Api controller)
            'api_methods' => ['get', 'post', 'put', 'delete'], // Allowed API methods
            'api_version' => 'v2', // API version
            'status_groups' => ['account', 'event', 'submission', 'booking', 'payment', 'performance', 'content', 'ticket', 'order', 'shipping', 'user_action'], // Status groups for the profile type use the relevant groups per the type and likelihood of various statuses that would be used for the profile type in the system
            'knowledgebase' => [ // Knowledgebase information for the profile type
                'documentation' => 'https://octobercms.com/docs/backend/users', // Documentation link
                'zipfolder' => 'https://octobercms.com/docs/backend/users', // Zip folder link
            ],
        ],

        'profiletypes' => [
            'namespace' => 'nexgenrifle',
            'name' => 'ProfileType',
            'type' => 'system',
            'access' => 'restricted',
            'icon' => 'icon-sitemap',
            'description' => 'Metadata registry for all available models in the system, containing information about model classes, endpoints, fields, and access rules.',
            'conversation_analysis' => [
                'goals' => ['Register system models', 'Define API access points', 'Document model structure', 'Control access permissions'],
                'strategies' => ['Centralized model registry', 'Dynamic API endpoint mapping', 'Field documentation', 'Access control'],
                'keywords' => ['model registry', 'api endpoint', 'metadata', 'model structure', 'permissions']
            ],
            'blog_article' => [
                'topics' => ['System Architecture', 'API Design', 'Model Registry'],
                'target_audience' => ['Developers', 'System administrators', 'API integrators'],
                'writing_style' => 'Technical, documentation-focused'
            ],
            'api_help' => [
                'documentation_links' => ['/api/v2/profiletypes'],
                'example_calls' => ['GET /api/v2/profiletypes', 'GET /api/v2/profiletypes/{id}'],
                'common_errors' => ['403 Forbidden - Insufficient permissions']
            ],
            'tone' => 'Technical, system-oriented',
            'relationships' => [
                'Models' => 'Describes and catalogs all system models',
                'API' => 'Defines available API endpoints'
            ],
            'fillable_fields' => [
                "id",
                "uuid",
                "access",
                "api_endpoint",
                "api_help",
                "blog_article",
                "class",
                "common",
                "controller_path",
                "content_types",
                "conversation_analysis",
                "description",
                "error_guidance",
                "fillable_fields",
                "sensitive_fields",
                "icon",
                "is_active",
                "is_featured",
                "is_default",
                "ai_enabled",
                "logging_details",
                "model_class",
                "name",
                "namespace",
                "type",
                "prompt_instructions",
                "query_params",
                "related_files",
                "relationships",
                "response_structure",
                "slug",
                "order",
                "status",
                "status_groups",
                "tone",
                "user_id",
                "created_at",
                "updated_at",
                "deleted_at",
                "migration_path",
                "fields_path",
                "columns_path",
                "api_version",
                "knowledgebase",
                "api_methods",
                "methods",
                "sensitive_data",
                "metadata"
            ],
            'content_types' => ["Model metadata", "API configuration", "Field definitions"],
            'model_class' => "Marty\\Nexgenrifle\\Models\\ProfileType",
            'api_endpoint' => "/api/v2/profiletypes",
            'query_params' => ["sort" => "name asc"],
            'response_structure' => [
                'profiletype' => [
                    'id',
                    'uuid',
                    'access',
                    'api_endpoint',
                    'api_help',
                    'blog_article',
                    'class',
                    'common',
                    'controller_path',
                    'content_types',
                    'conversation_analysis',
                    'description',
                    'error_guidance',
                    'fillable_fields',
                    'sensitive_fields',
                    'icon',
                    'is_active',
                    'is_featured',
                    'is_default',
                    'ai_enabled',
                    'logging_details',
                    'model_class',
                    'name',
                    'namespace',
                    'type',
                    'prompt_instructions',
                    'query_params',
                    'related_files',
                    'relationships',
                    'response_structure',
                    'slug',
                    'order',
                    'status',
                    'status_groups',
                    'tone',
                    'user_id',
                    'created_at',
                    'updated_at',
                    'deleted_at',
                    'migration_path',
                    'fields_path',
                    'columns_path',
                    'api_version',
                    'knowledgebase',
                    'api_methods',
                    'methods',
                    'sensitive_data',
                    'metadata'
                ]
            ],
            'error_guidance' => [
                'PROFILETYPE_NOT_FOUND' => "The requested profile type was not found. Please check the profile type ID.",
                'INVALID_MODEL_CLASS' => "The specified model class does not exist or is not properly configured."
            ],
            'logging_details' => [
                'key_relationships' => [
                    'models' => 'Describes the structure and metadata for all system models'
                ],
                'data_validation' => [
                    'name' => 'Required, must be unique',
                    'model_class' => 'Required, must be a valid class name',
                    'api_endpoint' => 'Required, must follow URL format'
                ]
            ],
            'common' => ["model registry", "api endpoints", "model definition", "schema", "system catalog"],
            'related_files' => [
                "plugins/marty/nexgenrifle/models/ProfileType.php",
                "plugins/marty/nexgenrifle/updates/create_profile_types_table.php",
                "plugins/marty/nexgenrifle/controllers/ProfileTypes.php"
            ],
            'controller_path' => 'Marty\\Nexgenrifle\\Controllers\\ProfileTypes',
            'api_methods' => ['get'],
            'api_version' => 'v2',
            'status_groups' => ['active', 'hidden', 'deprecated'],
            'knowledgebase' => [
                'documentation' => '/docs/api/profiletypes',
                'zipfolder' => '/docs/downloads/profiletypes'
            ]
        ]




    ];

    protected function detectModelFields($modelInstance)
    {
        $table = $modelInstance->getTable();
        $columns = Schema::getColumnListing($table);

        $jsonFields = [];
        $requiredFields = [];
        $additionalFields = [];
        $integerFields = []; // ✅ New array for integer fields
        $booleanFields = []; // ✅ Already exists

        foreach ($columns as $column) {
            if (in_array($column, ['id', 'created_at', 'updated_at', 'deleted_at', 'uuid'])) {
                continue;
            }
            try {
                $type = Schema::getColumnType($table, $column);

                if ($type === 'json') {
                    $jsonFields[] = $column;
                } elseif ($type === 'boolean' || str_starts_with($column, 'is_')) {
                    $booleanFields[] = $column;
                } elseif (in_array($type, ['integer', 'bigint', 'smallint', 'mediumint', 'tinyint'])) {
                    $integerFields[] = $column; // ✅ Correctly detect integer fields
                }

                if (Schema::getConnection()->getDoctrineColumn($table, $column)->getNotnull()) {
                    $requiredFields[] = $column;
                } else {
                    $additionalFields[] = $column;
                }
            } catch (\Exception $e) {
                Log::error("Error detecting field '$column' in '$table': " . $e->getMessage());
            }
        }

        return [
            'required' => $requiredFields,
            'json' => $jsonFields,
            'additional' => $additionalFields,
            'integer' => $integerFields, // ✅ Ensure this key is always returned
            'boolean' => $booleanFields, // ✅ Already existing
        ];
    }


    public function run()
    {
        $errors = [];
        $validatedTypes = [];
        $DEBUG_MODE = "errors";

        if ($this->promptUserBeforeSeeding) {
            echo "\n\e[1;34mNexgenrifle.app:\e[0m You are about to seed data into the model: \e[1;33m{$this->modelName}\e[0m. Proceed? (y/n): ";
            $confirmation = trim(fgets(STDIN));
            if (strtolower($confirmation) !== 'y') {
                Log::warning("Seeding aborted by user for model: {$this->modelName}.");
                echo "\e[1;31mSeeding aborted. No changes made.\e[0m\n";
                return;
            }
        }

        $jsonKeys = json_encode(array_keys($this->types));
        if ($this->promptUserBeforeSeeding) {
            echo "\n\e[1;34mNexgenrifle.app:\e[0m You are about to seed the following Models:  \e[1;33m{$jsonKeys}\e[0m. Proceed? (y/n): ";
            $confirmation = trim(fgets(STDIN));
            if (strtolower($confirmation) !== 'y') {
                Log::warning("🫷🛑Seeding aborted by user for model: {$this->modelName}.");
                echo "\e[1;31mSeeding aborted. No changes made.\e[0m\n";
                return;
            }
        }

        // Get namespace from the type array OR default to Marty\Nexgenrifle\Models
        $namespace = !empty($this->types['namespace']) ? $this->types['namespace'] : "Marty\\Nexgenrifle";
        echo (Log::info("Using namespace: $namespace For the following model: {$this->modelName}"));
        // Construct the full model class
        $modelClass = "{$namespace}\\Models\\{$this->modelName}";

        if (!class_exists($modelClass)) {
            Log::warning("Model class '$modelClass' not found. Checking if a custom model class is provided.");

            // Check if a fully qualified model_class is set inside the type array
            if (!empty($this->types['model_class']) && class_exists($this->types['model_class'])) {
                $modelClass = $this->types['model_class'];
            } else {
                Log::error("No valid model class found for '{$this->modelName}'. Seeder aborted.");
                return;
            }
        }

        $modelInstance = new $modelClass;
        $fieldDetection = $this->detectModelFields($modelInstance);
        $this->requiredFields = $fieldDetection['required'];
        $this->jsonFields = $fieldDetection['json'];
        $this->additionalFields = $fieldDetection['additional'];
        $this->integerFields = array_unique(array_merge($this->integerFields, $fieldDetection['integer']));
        $this->booleanFields = array_unique(array_merge($this->booleanFields, $fieldDetection['boolean']));

        $recordsReady = 0;
        foreach ($this->types as $key => $type) {
            $statusMessages = [];

            if ($this->promptUserBeforeSeeding) {
                // echo "\n\e[1;34mNexgenrifle.app:\e[0m You are about to seed the following Models:  \e[1;33m{$jsonKeys}\e[0m. Proceed? (y/n): ";
                $confirmation = 'y'; // trim(fgets(STDIN));
                if (strtolower($confirmation) !== 'y') {
                    Log::warning("🫷🛑Seeding aborted by user for model: {$this->modelName}.");
                    echo "\e[1;31mSeeding aborted. No changes made.\e[0m\n";
                    return;
                } else {
                    //echo the name of the model and it's required fields and contine to the next model automatically
                    echo "\n\e[1;34mNexgenrifle.app:\e[0m You are about to seed the following Model:  \e[1;33m{$key}\e[0m. Proceeding to the next model automatically: ";

                }
            }

            foreach ($this->requiredFields as $field) {
                if (!array_key_exists($field, $type)) {
                    $type[$field] = $this->getDefaultValueForField($field);
                }
            }
            $type['ai_enabled'] = $type['ai_enabled'] ?? false;

            $recordsReady++;
            $validatedTypes[$key] = $type;
        }

        if ($recordsReady === 0) {
            Log::warning("No valid records were found. Seeder aborted. Type Array could be empty.");
            echo "\e[1;33m⚠️ No valid records found. Seeder aborted  Type Array could be empty..\e[0m\n";
            return;
        }


        if ($this->promptUserBeforeSeeding) {
            echo "\n\e[1;34mNexgenrifle.app:\e[0m want to inspect this data?  \e[1;33m{$jsonKeys}\e[0m. Proceed? (y/n): ";
            $confirmation = 'y'; // trim(fgets(STDIN));
            if (strtolower($confirmation) !== 'y') {
                // Log::warning("🫷🛑Seeding aborted by user for model: {$this->modelName}.");
                echo "\e[1;31mModel inspection Skipped.\e[0m\n";
            } else {
                foreach ($validatedTypes as $type) {
                    // echo "\n\e[1;34mNexgenrifle.app:\e[0m Inspecting the following Model:  \e[1;33m{$type}\e[0m. Proceeding to the next model automatically: ";

                }


            }
        }



        try {
            DB::transaction(function () use ($validatedTypes, $modelClass) {
                $recordsSaved = 0;
                // echo the validatedTypes
                foreach ($validatedTypes as $type) {
                    Log::info("Attempting to insert/update: {$type['name']}");

                    $record = $modelClass::where('name', $type['name'])->first();
                    if ($record) {
                        Log::info("Updating existing record: {$type['name']}");
                        echo "\e[1;33m🔄 Updating existing record: {$type['name']}\e[0m\n";
                    } else {
                        Log::info("Creating 🆕 record for: {$type['name']} (UUID will be auto-generated by model)");
                        $record = new $modelClass;
                    }

                    // Dynamically handle JSON and boolean fields
                    foreach ($type as $field => &$value) { // Pass $value by reference
                        if (in_array($field, $this->jsonFields) && is_string($value)) {
                            $value = json_decode($value, true) ?? []; // Decode JSON or default to empty array
                        } elseif (in_array($field, $this->booleanFields)) {
                            $value = (bool) $value; // Cast to boolean
                        }
                        // Add more conditions here for other data types if needed
                    }
                    $this->normalizeFieldValues($type);

                    $record->fill($type);
                    $record->type = $type['type']; // Ensure the type field is set
                    try {
                        $record->save();
                        Log::info("✅ Successfully saved model '{$type['name']}' in {$this->modelName}.");
                        echo "\e[1;32m✅ Successfully saved record: {$type['name']}\e[0m\n";
                        $recordsSaved++;
                    } catch (\Exception $e) {
                        Log::error("Error saving record '{$type['name']}': " . $e->getMessage());
                        echo "\e[1;31m❌ Error saving record '{$type['name']}']: " . $e->getMessage() . "\e[0m\n";
                    }
                }

                if ($recordsSaved > 0) {
                    Log::info("Seeder completed successfully for model: {$this->modelName}.");
                    echo "\e[1;32m🎉 Nexgenrifle.app: Seeder completed successfully for model: {$this->modelName}.\e[0m\n";
                } else {
                    Log::warning("No records were inserted or updated for model: {$this->modelName}.");
                    echo "\e[1;33m⚠️ Nexgenrifle.app: No records were inserted or updated for model: {$this->modelName}.\e[0m\n";
                }
            });
        } catch (\Exception $e) {
            Log::error("Seeder Error: Error seeding {$this->modelName}: " . $e->getMessage());
            echo "\e[1;31m❌ Seeder Error: Error seeding {$this->modelName}: " . $e->getMessage() . "\e[0m\n";
            throw $e;
        }
    }

    private function getDefaultValueForField($field)
    {
        if (in_array($field, $this->jsonFields)) {
            return [];
        } elseif (in_array($field, $this->booleanFields)) {
            return 0; // Boolean default
        } elseif ($field === 'order') {
            return 0; // Default integer value for order
        } elseif (in_array($field, $this->requiredFields)) {
            return ""; // Fallback for required string fields
        }
        return null;
    }

    protected function normalizeFieldValues(array &$data)
    {
        foreach ($data as $key => &$value) {
            // Ensure integer fields are correctly cast to integers.
            if (in_array($key, $this->integerFields)) {
                // If a value is an empty string, (int)"" becomes 0.
                // If null is intended for nullable integer fields, it should be explicitly set as null
                // in the $types array data.
                if ($value !== null) { // Avoid casting an explicit null to 0
                    $value = (int) $value;
                }
            }
            // Note: JSON string to array and boolean casting are largely handled
            // by the loop that runs just before this normalizeFieldValues method is called.
            // Additional or stricter type checks could be added here if needed.
        }
    }
}
