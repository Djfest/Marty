<?php namespace Marty\NexGenRifle\Models;

use Model;
use October\Rain\Database\Traits\Validation;
use October\Rain\Database\Traits\SoftDelete;
use Marty\NexGenRifle\Traits\ReflectionTrait;
use System\Classes\PluginManager;
use ReflectionClass;
use ReflectionMethod;
use Exception;

class ProfileType extends Model
{
    use Validation;
    use SoftDelete;
    use ReflectionTrait;

    /**
     * @var string The database table used by the model.
     */
    public $table = 'marty_nexgenrifle_profile_types';

    /**
     * @var array Guarded fields
     */
    protected $guarded = ['id'];    /**
     * @var array Fillable fields
     */
    protected $fillable = [
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
        'required_fields',
        'response_structure',
        'slug',
        'order',
        'status',
        'status_groups',
        'tone',
        'user_id',
        'djfest_profile_id',
        'migration_path',
        'fields_path',
        'columns_path',
        'api_version',
        'knowledgebase',
        'api_methods',
        'methods',
        'sensitive_data',
        'metadata'
    ];    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'api_help' => 'array',
        'blog_article' => 'array',
        'common' => 'array',
        'content_types' => 'array',
        'conversation_analysis' => 'array',
        'error_guidance' => 'array',
        'fillable_fields' => 'array',
        'sensitive_fields' => 'array',
        'logging_details' => 'array',
        'query_params' => 'array',
        'related_files' => 'array',
        'relationships' => 'array',
        'response_structure' => 'array',
        'status_groups' => 'array',
        'knowledgebase' => 'array',
        'api_methods' => 'array',
        'methods' => 'array',
        'sensitive_data' => 'array',
        'metadata' => 'array'
    ];

    /**
     * @var array Validation rules for attributes
     */
    public $rules = [
        'name' => 'required',
        'uuid' => 'uuid'
    ];    /**
     * @var array Attributes to be cast to JSON
     */
    protected $jsonable = [
        'api_help',
        'blog_article',
        'common',
        'content_types',
        'conversation_analysis',
        'error_guidance',
        'fillable_fields',
        'required_fields',
        'sensitive_fields',
        'logging_details',
        'query_params',
        'related_files',
        'relationships',
        'response_structure',
        'status_groups',
        'knowledgebase',
        'api_methods',
        'methods',
        'sensitive_data',
        'metadata'
    ];

    /**
     * @var array Dates to be cast to Carbon instances
     */
    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at'
    ];

    /**
     * @var array Relations
     */
    public $belongsTo = [
        'user' => ['Backend\Models\User']
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->uuid)) {
                $model->uuid = \Illuminate\Support\Str::uuid()->toString();
            }
        });
    }


    /**
     * Cache for resolved model instances
     */
    protected static $modelCache = [];

    /**
     * Cache for model reflection data
     */
    protected static $modelReflectionCache = [];

    /**
     * Before save event
     */
    public function beforeSave()
    {
        if (empty($this->slug) && !empty($this->name)) {
            $this->slug = str_slug($this->name);
        }
    }

    /**
     * Scope a query to only include active profile types.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope a query to only include featured profile types.
     */
    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    /**
     * Get all AI-enabled profile types
     * 
     * @return \October\Rain\Database\Collection
     */
    public static function getAiEnabled()
    {
        return self::where('ai_enabled', true)->where('is_active', true)->get();
    }

    /**
     * Find profile type by name, common term, or class
     * 
     * @param string $term
     * @return ProfileType|null
     */
    public static function findByTerm($term)
    {
        $profile = self::where('name', $term)->first();
        if ($profile) return $profile;

        $profile = self::where('model_class', $term)->first();
        if ($profile) return $profile;

        return self::whereRaw("JSON_CONTAINS(common, '\"$term\"', '$')")->first();
    }

    /**
     * Resolve a model class from this profile type
     * 
     * @return \October\Rain\Database\Model|null
     */
    public function resolveModel()
    {
        if (!$this->model_class || !class_exists($this->model_class)) {
            return null;
        }

        $cacheKey = $this->model_class;

        if (!isset(static::$modelCache[$cacheKey])) {
            try {
                static::$modelCache[$cacheKey] = new $this->model_class();
            } catch (Exception $e) {
                \Log::error("Failed to instantiate model {$this->model_class}: " . $e->getMessage());
                return null;
            }
        }

        return static::$modelCache[$cacheKey];
    }

    /**
     * Get conversation analysis data
     * 
     * @return array
     */
    public function getConversationAnalysis()
    {
        return $this->conversation_analysis ?: [
            'goals' => [],
            'strategies' => [],
            'keywords' => []
        ];
    }

    /**
     * Get API documentation and help
     * 
     * @return array
     */
    public function getApiDocumentation()
    {
        return [
            'endpoint' => $this->api_endpoint,
            'version' => $this->api_version,
            'methods' => $this->api_methods ?: [],
            'help' => $this->api_help ?: [],
            'examples' => $this->getApiExamples()
        ];
    }

    /**
     * Get API examples from the API help field
     * 
     * @return array
     */
    protected function getApiExamples()
    {
        if (!$this->api_help || !isset($this->api_help['example_calls'])) {
            return [];
        }

        return $this->api_help['example_calls'];
    }

    /**
     * Get field information for the model using reflection
     * 
     * @return array
     */
    public function getFieldInformation()
    {
        $model = $this->resolveModel();
        if (!$model) {
            return $this->fillable_fields ?: [];
        }

        $fields = [];

        // Use reflection to get all properties
        $reflection = new ReflectionClass($model);
        $properties = $reflection->getProperties();

        foreach ($properties as $property) {
            $name = $property->getName();
            // Skip private properties
            if ($property->isPrivate()) {
                continue;
            }

            $fields[$name] = [
                'name' => $name,
                'visibility' => $property->isPublic() ? 'public' : 'protected',
                'docComment' => $property->getDocComment()
            ];
        }

        // Add fillable fields from the model
        if (property_exists($model, 'fillable')) {
            foreach ($model->fillable as $field) {
                if (!isset($fields[$field])) {
                    $fields[$field] = [
                        'name' => $field,
                        'visibility' => 'fillable',
                        'docComment' => null
                    ];
                } else {
                    $fields[$field]['fillable'] = true;
                }
            }
        }

        // Add JSON fields
        if (property_exists($model, 'jsonable')) {
            foreach ($model->jsonable as $field) {
                if (isset($fields[$field])) {
                    $fields[$field]['type'] = 'json';
                }
            }
        }

        // Add casted fields
        if (method_exists($model, 'getCasts')) {
            $casts = $model->getCasts();
            foreach ($casts as $field => $type) {
                if (isset($fields[$field])) {
                    $fields[$field]['cast'] = $type;
                }
            }
        }

        return $fields;
    }

    /**
     * Get model methods using reflection
     * 
     * @param bool $onlyPublic
     * @return array
     */
    public function getModelMethods($onlyPublic = true)
    {
        $model = $this->resolveModel();
        if (!$model) {
            return [];
        }

        $cacheKey = $this->model_class . '|' . ($onlyPublic ? 'public' : 'all');
        if (isset(static::$modelReflectionCache[$cacheKey])) {
            return static::$modelReflectionCache[$cacheKey];
        }

        $reflection = new ReflectionClass($model);
        $filter = $onlyPublic ? ReflectionMethod::IS_PUBLIC : null;
        $methods = $reflection->getMethods($filter);

        $result = [];
        foreach ($methods as $method) {
            // Skip inherited methods from base Model class
            if ($method->class === 'October\Rain\Database\Model' || 
                $method->class === 'Illuminate\Database\Eloquent\Model') {
                continue;
            }

            $parameters = [];
            foreach ($method->getParameters() as $param) {
                $parameters[] = [
                    'name' => $param->getName(),
                    'type' => $param->hasType() ? (string)$param->getType() : null,
                    'isOptional' => $param->isOptional(),
                    'defaultValue' => $param->isDefaultValueAvailable() ? $param->getDefaultValue() : null
                ];
            }

            $result[$method->getName()] = [
                'name' => $method->getName(),
                'parameters' => $parameters,
                'returnType' => $method->hasReturnType() ? (string)$method->getReturnType() : null,
                'docComment' => $method->getDocComment(),
                'class' => $method->class
            ];
        }

        static::$modelReflectionCache[$cacheKey] = $result;
        return $result;
    }

    /**
     * Get model relationships using reflection
     * 
     * @return array
     */
    public function getModelRelationships()
    {
        $model = $this->resolveModel();
        if (!$model) {
            return [];
        }

        $relationshipTypes = [
            'hasOne', 'hasMany', 'belongsTo', 'belongsToMany',
            'morphTo', 'morphOne', 'morphMany', 'morphToMany',
            'attachOne', 'attachMany'
        ];

        $relationships = [];
        $reflection = new ReflectionClass($model);

        foreach ($relationshipTypes as $type) {
            $property = null;
            try {
                $property = $reflection->getProperty($type);
            } catch (\ReflectionException $e) {
                continue;
            }

            // Make the property accessible
            $property->setAccessible(true);

            // Get the property value
            $value = $property->getValue($model);

            if ($value) {
                $relationships[$type] = $value;
            }
        }

        return $relationships;
    }

    
    /**
     * Generate model documentation using reflection
     * 
     * @return array
     */
    public function generateModelDocumentation()
    {
        $model = $this->resolveModel();
        if (!$model) {
            return [
                'error' => 'Model could not be resolved'
            ];
        }

        $reflection = new ReflectionClass($model);

        return [
            'class' => [
                'name' => $reflection->getName(),
                'shortName' => $reflection->getShortName(),
                'namespace' => $reflection->getNamespaceName(),
                'docComment' => $reflection->getDocComment(),
                'fileName' => $reflection->getFileName(),
            ],
            'fields' => $this->getFieldInformation(),
            'methods' => $this->getModelMethods(),
            'relationships' => $this->getModelRelationships(),
            'schema' => $model instanceof ReflectionTrait ? $model->getSchemaInfo() : null
        ];
    }

    /**
     * Get related files information
     * 
     * @return array
     */
    public function getRelatedFiles()
    {
        return $this->related_files ?: [];
    }

    /**
     * Generate a prompt for AI based on this profile type
     * 
     * @param string $context Additional context for the prompt
     * @return string
     */
    public function generateAiPrompt($context = '')
    {
        $prompt = $this->prompt_instructions ?: '';

        $prompt .= "\n\nYou are working with the {$this->name} model.";

        if ($this->description) {
            $prompt .= "\nDescription: {$this->description}";
        }

        if ($this->tone) {
            $prompt .= "\nUse a {$this->tone} tone when discussing this model.";
        }

        $analysis = $this->getConversationAnalysis();
        if (!empty($analysis['keywords'])) {
            $prompt .= "\nRelevant keywords: " . implode(', ', $analysis['keywords']);
        }

        if ($context) {
            $prompt .= "\n\nAdditional context: {$context}";
        }

        return $prompt;
    }

    /**
     * Get knowledge base information
     * 
     * @return array
     */
    public function getKnowledgeBase()
    {
        return $this->knowledgebase ?: [];
    }

    /**
     * Check if this profile type represents a plugin model
     * 
     * @return bool
     */
    public function isPluginModel()
    {
        if (!$this->model_class) {
            return false;
        }

        $pluginManager = PluginManager::instance();
        $plugins = $pluginManager->getPlugins();

        foreach ($plugins as $plugin) {
            $namespace = get_class($plugin);
            $namespace = substr($namespace, 0, strrpos($namespace, '\\'));

            if (strpos($this->model_class, $namespace) === 0) {
                return true;
            }
        }

        return false;
    }

    /**
     * Get the plugin identifier for this model
     * 
     * @return string|null
     */
    public function getPluginIdentifier()
    {
        if (!$this->model_class) {
            return null;
        }

        $parts = explode('\\', $this->model_class);
        if (count($parts) >= 3) {
            return strtolower($parts[0] . '.' . $parts[1]);
        }

        return null;
    }

    /**
     * Find all profile types related to a specific plugin
     * 
     * @param string $pluginCode
     * @return \October\Rain\Database\Collection
     */
    public static function findByPlugin($pluginCode)
    {
        $pluginCode = strtolower($pluginCode);
        $parts = explode('.', $pluginCode);

        if (count($parts) !== 2) {
            return collect([]);
        }

        $vendor = $parts[0];
        $name = $parts[1];

        $pattern = "{$vendor}\\\\{$name}\\\\%";

        return self::where('model_class', 'LIKE', $pattern)->get();
    }

    /**
     * Get error guidance for specific error code
     * 
     * @param string $errorCode
     * @return string|null
     */
    public function getErrorGuidance($errorCode)
    {
        if (!$this->error_guidance || !isset($this->error_guidance[$errorCode])) {
            return null;
        }

        return $this->error_guidance[$errorCode];
    }

    /**
     * Get all known statuses for this model
     * 
     * @return array
     */
    public function getModelStatuses()
    {
        $groups = $this->status_groups ?: [];
        return $groups;
    }

    /**
     * Get the model controller information
     * 
     * @return array
     */
    public function getControllerInfo()
    {
        return [
            'path' => $this->controller_path,
            'related_files' => $this->getRelatedControllerFiles()
        ];
    }

    /**
     * Get related controller files
     * 
     * @return array
     */
    protected function getRelatedControllerFiles()
    {
        if (!$this->related_files) {
            return [];
        }

        $controllerFiles = [];
        foreach ($this->related_files as $file) {
            if (strpos($file, 'controllers') !== false) {
                $controllerFiles[] = $file;
            }
        }

        return $controllerFiles;
    }



    /**
     * Get structured metadata about this model with reflection data
     * 
     * @return array
     */
    public function getStructuredMetadata()
    {
        $basicMetadata = [
            'basic' => [
                'id' => $this->id,
                'name' => $this->name,
                'namespace' => $this->namespace,
                'type' => $this->type,
                'description' => $this->description
            ],
            'model' => [
                'class' => $this->model_class,
                'fields' => $this->getFieldInformation(),
                'sensitive_fields' => $this->sensitive_fields ?: []
            ],
            'api' => $this->getApiDocumentation(),
            'ai' => [
                'enabled' => (bool)$this->ai_enabled,
                'conversation' => $this->getConversationAnalysis(),
                'tone' => $this->tone
            ],
            'knowledge_base' => $this->getKnowledgeBase(),
            'relationships' => $this->relationships ?: []
        ];

        // Add reflection data if model exists
        $model = $this->resolveModel();
        if ($model) {
            $basicMetadata['reflection'] = [
                'methods' => $this->getModelMethods(),
                'relationships' => $this->getModelRelationships()
            ];

            if ($model instanceof ReflectionTrait) {
                $basicMetadata['reflection']['schema'] = $model->getSchemaInfo();
            }
        }

        return $basicMetadata;
    }

    /**
     * Get required fields for this model
     * 
     * @return array
     */
    public function getRequiredFields()
    {
        // First check if we have explicitly defined required fields
        if (!empty($this->required_fields)) {
            return $this->required_fields;
        }
        
        // Otherwise try to determine from the model's validation rules
        $model = $this->resolveModel();
        if (!$model || !property_exists($model, 'rules')) {
            return [];
        }
        
        $requiredFields = [];
        foreach ($model->rules as $field => $rules) {
            if (is_string($rules) && strpos($rules, 'required') !== false) {
                $requiredFields[] = $field;
            }
            elseif (is_array($rules) && in_array('required', $rules)) {
                $requiredFields[] = $field;
            }
        }
        
        return $requiredFields;
    }
}
