<?php namespace Marty\NexGenRifle\Controllers;

use Response;
use Exception;
use BackendMenu;
use Backend\Classes\Controller;
use ApplicationException;
use October\Rain\Database\ModelException;

/**
 * API Controller
 */
class Api extends Controller
{
    /**
     * @var array Behaviors implemented by this controller
     */
    public $implement = [];

    /** @var bool A flag to enable more verbose responses and error details. */
    protected $debugMode = false;

    /**
     * Constructor
     */
    public function __construct()
    {
        parent::__construct();
        
        // Set debug mode based on APP_DEBUG from .env as a default
        $this->debugMode = env('APP_DEBUG', false);
        
        // If authentication middleware confirmed the system API key was used,
        // set debugMode to true for this request to provide maximum detail.
        if (request()->attributes->get('is_system_api_key_used', false)) {
            $this->debugMode = true;
        }
    }    /**
     * Display a listing of resources
     * 
     * @param string $resource Resource type to list
     * @return Response
     */
    public function index($resource)
    {
        try {
            // Debug information that can help troubleshoot route parameter issues
            if ($this->debugMode) {
                \Log::info("API Resource Index called with resource: " . $resource);
            }
            
            $model = $this->resolveResourceModel($resource);
            
            // Apply query parameters
            $query = $model::query();
            
            // Apply sorting if provided
            if ($sort = request('sort')) {
                $direction = 'asc';
                if (starts_with($sort, '-')) {
                    $direction = 'desc';
                    $sort = substr($sort, 1);
                }
                $query->orderBy($sort, $direction);
            }
            
            // Apply filtering if provided
            if ($filter = request('filter')) {
                // Parse filter JSON if it's a string
                if (is_string($filter)) {
                    $filter = json_decode($filter, true) ?? [];
                }
                
                foreach ($filter as $field => $value) {
                    if (is_array($value)) {
                        $query->whereIn($field, $value);
                    } else {
                        $query->where($field, $value);
                    }
                }
            }
            
            // Apply relationship eager loading
            if ($with = request('with')) {
                $relations = explode(',', $with);
                $query->with($relations);
            }
            
            // Apply pagination
            $perPage = request('per_page', 15);
            $page = request('page', 1);
            
            $items = $query->paginate($perPage, ['*'], 'page', $page);
            
            // Enhance response with ProfileType metadata
            $responseData = $this->enhanceResponseWithProfileType(['data' => $items], $resource);
            return response()->json($responseData);
        }
        catch (ModelException $ex) {
            return $this->handleError($ex, 422);
        }
        catch (Exception $ex) {
            return $this->handleError($ex);
        }
    }

    /**
     * Specific method for build lists index
     */
    public function indexBuildLists()
    {
        try {
            $model = $this->resolveResourceModel('build-lists');
            $items = $model::all();
            
            // Enhance response with ProfileType metadata
            $responseData = $this->enhanceResponseWithProfileType(['data' => $items], 'build-lists');
            return response()->json($responseData);
        }
        catch (ModelException $ex) {
            return $this->handleError($ex, 'build-lists', 422);
        }
        catch (Exception $ex) {
            return $this->handleError($ex, 'build-lists');
        }
    }    /**
     * Display the specified resource
     * 
     * @param string $resource Resource type to show
     * @param int $id Resource ID
     * @return Response
     */
    public function show($resource, $id)
    {
        try {
            $modelClass = $this->resolveResourceModel($resource);
            $item = $modelClass::find($id);
            
            if (!$item) {
                // Let ModelNotFoundException be thrown by findOrFail if preferred, or handle here
                // For consistency with other updated methods, let's use findOrFail
                $item = $modelClass::findOrFail($id);
            }
            
            // Enhance response with ProfileType metadata
            $responseData = $this->enhanceResponseWithProfileType(['data' => $item], $resource);
            return response()->json($responseData);
        }
        catch (\Illuminate\Database\Eloquent\ModelNotFoundException $ex) { 
            return $this->handleError($ex, $resource, 404); 
        }
        catch (ModelException $ex) {
            return $this->handleError($ex, $resource, 422);
        }
        catch (Exception $ex) {
            return $this->handleError($ex, $resource);
        }
    }

    /**
     * Specific method for showing a build list
     */
    public function showBuildList($id)
    {
        try {
            $model = $this->resolveResourceModel('build-lists'); // Resolves to class string
            $item = $model::findOrFail($id); // findOrFail will throw ModelNotFoundException if not found
            $responseData = $this->enhanceResponseWithProfileType(['data' => $item], 'build-lists');
            return response()->json($responseData);
        }
        catch (Exception $ex) {
            return $this->handleError($ex);
        }
    } // Note: This specific method's error handling for ModelNotFoundException is already good.
      // For ModelException (validation) during a GET, it's less common but possible if complex query scopes fail.
      // We can add specific catch for ModelException if needed, or let the generic Exception catch it.
      // For consistency, let's ensure it passes 'build-lists' to handleError.
      // catch (Exception $ex) {
      //     return $this->handleError($ex, 'build-lists');
      // }

    /**
     * Store a newly created resource
     * 
     * @param string $resource Resource type to create
     * @return Response
     */
    public function store($resource)
    {
        try {
            $modelClass = $this->resolveResourceModel($resource);
            $data = request()->all();
            
            $item = new $modelClass;
            $item->fill($data);
            $item->save();
            
            $responseData = $this->enhanceResponseWithProfileType(['data' => $item], $resource);
            return response()->json($responseData, 201);
        }
        catch (ModelException $ex) {
            return $this->handleError($ex, $resource, 422);
        }
        catch (Exception $ex) {
            return $this->handleError($ex, $resource);
        }
    }

    /**
     * Specific method for storing a build list
     */
    public function storeBuildList()
    {
        try {
            $modelClass = $this->resolveResourceModel('build-lists');
            $data = post();
            
            $item = new $modelClass;
            $item->fill($data);
            $item->save();
            
            $responseData = $this->enhanceResponseWithProfileType(['data' => $item], 'build-lists');
            return response()->json($responseData, 201);
        }
        catch (ModelException $ex) {
            return $this->handleError($ex, 'build-lists', 422);
        }
        catch (Exception $ex) {
            return $this->handleError($ex, 'build-lists');
        }
    }

    /**
     * Update the specified resource
     * 
     * @param string $resource Resource type to update
     * @param int $id Resource ID
     * @return Response
     */
    public function update($resource, $id)
    {
        try {
            $modelClass = $this->resolveResourceModel($resource);
            $data = post();
            $item = $modelClass::findOrFail($id);
            $item->fill($data);
            $item->save();
            
            $responseData = $this->enhanceResponseWithProfileType(['data' => $item], $resource);
            return response()->json($responseData);
        }
        catch (\Illuminate\Database\Eloquent\ModelNotFoundException $ex) { 
            return $this->handleError($ex, $resource, 404); 
        }
        catch (ModelException $ex) {
            return $this->handleError($ex, $resource, 422);
        }
        catch (Exception $ex) {
            return $this->handleError($ex, $resource);
        }
    }

    /**
     * Specific method for updating a build list
     */
    public function updateBuildList($id)
    {
        try {
            $modelClass = $this->resolveResourceModel('build-lists');
            $data = post();
            $item = $modelClass::findOrFail($id);
            $item->fill($data);
            $item->save();
            
            $responseData = $this->enhanceResponseWithProfileType(['data' => $item], 'build-lists');
            return response()->json($responseData);
        }
        catch (\Illuminate\Database\Eloquent\ModelNotFoundException $ex) { 
            return $this->handleError($ex, 'build-lists', 404); 
        }
        catch (ModelException $ex) {
            return $this->handleError($ex, 'build-lists', 422);
        }
        catch (Exception $ex) {
            return $this->handleError($ex, 'build-lists');
        }
    }

    /**
     * Remove the specified resource
     * 
     * @param string $resource Resource type to delete
     * @param int $id Resource ID
     * @return Response
     */
    public function destroy($resource, $id)
    {
        try {
            $modelClass = $this->resolveResourceModel($resource);
            $item = $modelClass::findOrFail($id);
            $item->delete();
            
            return response()->json([
                'message' => ucfirst($resource) . ' deleted successfully'
            ]);
        }
        catch (\Illuminate\Database\Eloquent\ModelNotFoundException $ex) { 
            return $this->handleError($ex, $resource, 404); 
        }
        // No ModelException expected on delete, but other exceptions possible
        catch (Exception $ex) {
            return $this->handleError($ex, $resource);
        }
    }

    /**
     * Specific method for deleting a build list
     */
    public function destroyBuildList($id)
    {
        try {
            $modelClass = $this->resolveResourceModel('build-lists');
            $item = $modelClass::findOrFail($id);
            $item->delete();
            
            return response()->json([
                'message' => 'Build list deleted successfully'
            ]);
        }
        catch (\Illuminate\Database\Eloquent\ModelNotFoundException $ex) { 
            return $this->handleError($ex, 'build-lists', 404); 
        }
        // No ModelException expected on delete
        catch (Exception $ex) {
            return $this->handleError($ex, 'build-lists');
        }
    }

    /**
     * Execute batch operations
     * 
     * @return Response
     */
    public function batch()
    {
        try {
            $operations = post('operations', []);
            $useTransaction = post('use_transaction', true);
            $results = [];
            
            if ($useTransaction) {
                \DB::beginTransaction();
            }
            
            foreach ($operations as $index => $operation) {
                $method = strtolower($operation['method']);
                $resource = $operation['resource'];
                $data = isset($operation['data']) ? $operation['data'] : null;
                $id = isset($operation['id']) ? $operation['id'] : null;
                
                // Execute the appropriate method for this operation
                switch ($method) {
                    case 'get':
                        if ($id) {
                            $results[] = $this->show($resource, $id)->getOriginalContent();
                        } else {
                            $results[] = $this->index($resource)->getOriginalContent();
                        }
                        break;
                        
                    case 'post':
                        $_POST = $data;
                        $results[] = $this->store($resource)->getOriginalContent();
                        break;
                        
                    case 'put':
                        $_POST = $data;
                        $results[] = $this->update($resource, $id)->getOriginalContent();
                        break;
                        
                    case 'delete':
                        $results[] = $this->destroy($resource, $id)->getOriginalContent();
                        break;
                }
            }
            
            if ($useTransaction) {
                \DB::commit();
            }
            
            return response()->json(['results' => $results]);
        }
        catch (Exception $ex) {
            if (isset($useTransaction) && $useTransaction) {
                \DB::rollBack();
            }
            return $this->handleError($ex, isset($resource) ? $resource : null);
        }
    }

    /**
     * Process data with flexible operations
     * 
     * @return Response
     */
    public function process()
    {
        try {
            $model = post('model');
            $action = post('action');
            $data = post('data', []);
            $options = post('options', []);
            
            if (empty($model)) {
                throw new ApplicationException('The model parameter is required');
            }
            
            if (empty($action)) {
                throw new ApplicationException('The action parameter is required');
            }
            
            // First try to resolve the model class
            try {
                $modelClass = $this->resolveResourceModel($model);
            } catch (Exception $e) {
                throw new ApplicationException("Could not resolve model: {$model}. " . $e->getMessage());
            }
            
            // Check if model has a method matching the action
            $actionMethod = 'process' . ucfirst($action);
            if (method_exists($modelClass, $actionMethod)) {
                $result = $modelClass::$actionMethod($data, $options);
                return response()->json([
                    'data' => $result,
                    'message' => "Successfully processed {$action} on {$model}"
                ]);
            }
            
            // Check if there's a controller method for this model action
            $controllerMethod = 'process' . ucfirst($model) . ucfirst($action);
            if (method_exists($this, $controllerMethod)) {
                $result = $this->$controllerMethod($data, $options);
                return response()->json([
                    'data' => $result,
                    'message' => "Successfully processed {$action} on {$model}"
                ]);
            }
            
            // Check for generic handlers in the controller
            $genericMethod = 'processGeneric' . ucfirst($action);
            if (method_exists($this, $genericMethod)) {
                $result = $this->$genericMethod($model, $data, $options);
                return response()->json([
                    'data' => $result,
                    'message' => "Successfully processed {$action} on {$model}"
                ]);
            }
            
            // If no processor found, throw error
            throw new ApplicationException("No processor found for action '{$action}' on model '{$model}'");
        }
        catch (ApplicationException $ex) {
            return $this->handleError($ex, null, 400);
        }
        catch (ModelException $ex) {
            return $this->handleError($ex, null, 422);
        }
        catch (Exception $ex) {
            return $this->handleError($ex);
        }
    }
    
    /**
     * Process generic analyze action
     * 
     * @param string $model The model name
     * @param array $data Additional data
     * @param array $options Processing options
     * @return array Analysis results
     */
    protected function processGenericAnalyze($model, $data = [], $options = [])
    {
        $modelClass = $this->resolveResourceModel($model);
        $query = $modelClass::query();
        
        // Apply filters if provided
        if (!empty($data['filters'])) {
            foreach ($data['filters'] as $field => $value) {
                if (is_array($value)) {
                    $query->whereIn($field, $value);
                } else {
                    $query->where($field, $value);
                }
            }
        }
        
        // Get count
        $totalCount = $query->count();
        
        // Get available fields
        $sampleItem = new $modelClass;
        $fillableFields = $sampleItem->getFillable();
        
        // Get ProfileType metadata
        $profileType = $this->getProfileTypeForResource($model);
        $profileTypeInfo = null;
        if ($profileType) {
            $profileTypeInfo = [
                'name' => $profileType->name,
                'slug' => $profileType->slug,
                'description' => $profileType->description
            ];
        }
        
        // Build response
        return [
            'model' => $model,
            'model_class' => $modelClass,
            'total_count' => $totalCount,
            'fillable_fields' => $fillableFields,
            'profile_type' => $profileTypeInfo,
            'analyzed_at' => now()->toDateTimeString()
        ];
    }
    
    /**
     * Process BuildList analyze action (example model-specific processor)
     * 
     * @param array $data Additional data
     * @param array $options Processing options
     * @return array Analysis results
     */
    protected function processBuildListAnalyze($data = [], $options = [])
    {
        // Get base analysis
        $baseAnalysis = $this->processGenericAnalyze('build-lists', $data, $options);
        
        // Add BuildList specific info
        $modelClass = $this->resolveResourceModel('build-lists');
        
        // Count items in lists
        $totalItems = \DB::table('marty_nexgenrifle_build_list_items')->count();
        $listsWithItems = \DB::table('marty_nexgenrifle_build_list_items')
            ->select('build_list_id')
            ->groupBy('build_list_id')
            ->get()
            ->count();
        
        // Add to response
        $baseAnalysis['total_items'] = $totalItems;
        $baseAnalysis['lists_with_items'] = $listsWithItems;
        $baseAnalysis['average_items_per_list'] = $baseAnalysis['total_count'] > 0 
            ? round($totalItems / $baseAnalysis['total_count'], 2) 
            : 0;
            
        return $baseAnalysis;
    }
    /**
     * Resolve the model class from the resource type
     * 
     * @param string $resource Resource type
     * @return string Model class name
     * @throws ApplicationException
     */
    protected function resolveResourceModel($resource)
    {
        // Try to resolve via ProfileType first (preferred approach)
        try {
            // First try direct matching on common fields
            $profileType = \Marty\NexGenRifle\Models\ProfileType::where('slug', $resource)
                ->orWhere('type', $resource)
                ->first();
            
            // If not found by direct match, try common terms in the JSON field
            if (!$profileType) {
                $profileType = \Marty\NexGenRifle\Models\ProfileType::whereJsonContains('common', $resource)->first();
            }
                
            if ($profileType && !empty($profileType->model_class)) {
                if ($this->debugMode) {
                    \Log::info("Resolved resource '{$resource}' to model '{$profileType->model_class}' using ProfileType '{$profileType->name}'");
                }
                return $profileType->model_class;
            }
        } catch (Exception $e) {
            if ($this->debugMode) {
                \Log::warning('Error resolving model from ProfileType: ' . $e->getMessage());
            }
            // Continue to fallback methods
        }
        
        // If not found via ProfileType, check the hardcoded resource map as fallback
        $resourceMap = [
            'build-lists' => 'Marty\NexGenRifle\Models\BuildList',
            'rifle-builds' => 'Marty\NexGenRifle\Models\RifleBuild',
            'products' => 'Marty\NexGenRifle\Models\Product',
            'product-catalogs' => 'Marty\NexGenRifle\Models\ProductCatalog',
            'product-categories' => 'Marty\NexGenRifle\Models\ProductCategory',
            'categories' => 'Marty\NexGenRifle\Models\ProductCategory',
            'product-items' => 'Marty\NexGenRifle\Models\ProductItem',
            'suppliers' => 'Marty\NexGenRifle\Models\Supplier',
            'user-profiles' => 'Marty\NexGenRifle\Models\UserProfile',
            'profile-types' => 'Marty\NexGenRifle\Models\ProfileType',
        ];
        
        if (isset($resourceMap[$resource])) {
            if ($this->debugMode) {
                \Log::info("Resolved resource '{$resource}' to model '{$resourceMap[$resource]}' using hardcoded map");
            }
            return $resourceMap[$resource];
        }

        // If 'resources' is literally passed, reject it as an invalid resource type
        if ($resource === 'resources') {
            throw new ApplicationException('Invalid resource type: resources is a route prefix, not a model type');
        }
        
        // Try to dynamically resolve the model class
        $attemptedConventions = [];
        
        // Try common namespace conventions
        $conventions = [
            'Marty\\NexGenRifle\\Models\\' . $this->singularize($resource),
            'Marty\\AiSolutions\\Models\\' . $this->singularize($resource),
            'RainLab\\User\\Models\\' . $this->singularize($resource),
        ];
        
        foreach ($conventions as $convention) {
            $attemptedConventions[] = $convention;
            if (class_exists($convention)) {
                if ($this->debugMode) {
                    \Log::info("Resolved resource '{$resource}' to model '{$convention}' using convention");
                }
                return $convention;
            }
        }
        
        throw new ApplicationException('Invalid resource type or model not found: ' . $resource . '. Attempted conventions: ' . implode(', ', $attemptedConventions));
    }
    
    /**
     * Helper method to convert a plural resource name to singular
     * 
     * @param string $word Plural word
     * @return string Singular word
     */
    private function singularize($word)
    {
        $lastChar = substr($word, -1);
        
        // Basic singularization rules
        if (substr($word, -3) === 'ies') {
            return substr($word, 0, -3) . 'y';
        } else if (substr($word, -2) === 'es') {
            return substr($word, 0, -2);
        } else if ($lastChar === 's' && substr($word, -2) !== 'ss') {
            return substr($word, 0, -1);
        }
        
        // Attempt to convert kebab-case to PascalCase
        if (strpos($word, '-') !== false) {
            $parts = explode('-', $word);
            $singularParts = array_map(function($part) {
                return ucfirst($this->singularize($part));
            }, $parts);
            return implode('', $singularParts);
        }
        
        // If no rules match, use as is
        return ucfirst($word);
    }
    
    /**
     * Get ProfileType information for a resource
     * 
     * @param string $resource Resource type
     * @return \Marty\NexGenRifle\Models\ProfileType|null
     */
    protected function getProfileTypeForResource($resource)
    {
        try {
            $profileType = \Marty\NexGenRifle\Models\ProfileType::where('slug', $resource)->first();
            if ($profileType) return $profileType;

            $profileType = \Marty\NexGenRifle\Models\ProfileType::where('name', $resource)->first();
            if ($profileType) return $profileType;
            
            $profileType = \Marty\NexGenRifle\Models\ProfileType::whereJsonContains('common', $resource)->first();
            if ($profileType) return $profileType;

            $profileType = \Marty\NexGenRifle\Models\ProfileType::where('type', $resource)->first();
            if ($profileType) return $profileType;

            // If resource might be a model class name (e.g. from an error)
            $profileType = \Marty\NexGenRifle\Models\ProfileType::where('model_class', $resource)->first();
            if ($profileType) return $profileType;

            // Fallback: if $resource is a studly-cased model name (e.g. RifleBuild), try to find its ProfileType
            // Or if it was a slug like 'rifle-builds'
            $studlySingularResource = \Illuminate\Support\Str::studly(\Illuminate\Support\Str::singular($resource));
            $potentialModelClass = 'Marty\\NexGenRifle\\Models\\' . $studlySingularResource;
            if (class_exists($potentialModelClass)) {
                $profileType = \Marty\NexGenRifle\Models\ProfileType::where('model_class', $potentialModelClass)->first();
                if ($profileType) return $profileType;
            }
            return null;
        } catch (Exception $e) {
            \Log::warning("Error getting ProfileType for resource '{$resource}': " . $e->getMessage());
            return null;
        }
    }
    
    /**
     * Enhance response data with ProfileType metadata
     * 
     * @param mixed $data Response data
     * @param string $resource Resource type
     * @return mixed Enhanced data
     */
    protected function enhanceResponseWithProfileType($data, $resource)
    {
        $profileType = $this->getProfileTypeForResource($resource);
        $metadata = [];

        if ($profileType) {
            $metadata['profile_type_info'] = [
                'name' => $profileType->name,
                'model_class' => $profileType->model_class,
                'description' => $profileType->description,
            ];

            if (!empty($profileType->api_help)) {
                $metadata['profile_type_info']['api_help'] = $profileType->api_help;
            }

            if ($this->debugMode) {
                $metadata['profile_type_info']['debug_details'] = [
                    'profile_type_id' => $profileType->id,
                    'slug' => $profileType->slug,
                    'type' => $profileType->type,
                    'fillable_fields_from_profile' => $profileType->fillable_fields,
                    'relationships_from_profile' => $profileType->relationships,
                    'expected_response_structure_from_profile' => $profileType->response_structure,
                    'api_methods_from_profile' => $profileType->api_methods,
                    'common_terms_from_profile' => $profileType->common,
                ];
            }
        } elseif ($this->debugMode) {
            $metadata['profile_type_info'] = [
                'warning' => "ProfileType not found for resource '{$resource}'. API response might lack detailed metadata.",
                'resolved_model_class_attempt' => null
            ];
            try {
                // Attempt to resolve model even without ProfileType for debugging
                $modelClass = $this->resolveResourceModel($resource);
                $metadata['profile_type_info']['resolved_model_class_attempt'] = $modelClass;
            } catch (Exception $e) {
                $metadata['profile_type_info']['resolved_model_class_attempt'] = "Failed to resolve: " . $e->getMessage();
            }
        }

        // If data is already an array with 'data' key, add metadata alongside
        if (is_array($data) && isset($data['data'])) {
            if (!empty($metadata)) {
                $data['_meta'] = $metadata;
            }
            return $data;
        }

        // Otherwise, wrap the data with metadata
        $responseData = [
            'data' => $data,
        ];
        if (!empty($metadata)) {
            $responseData['_meta'] = $metadata;
        }
        return $responseData;
    }

    /**
     * Handle errors with appropriate response
     * 
     * @param Exception $ex The exception
     * @param string|null $resource The resource being processed, if known
     * @param int $defaultStatusCode Default HTTP status code for this error type
     * @return Response
     */
    public function handleError($ex, $resource = null, $defaultStatusCode = 500)
    {
        // Log the error
        \Log::error($ex);
        
        $statusCode = $defaultStatusCode;
        $errorResponse = [
            'error' => 'An error occurred',
            'message' => $ex->getMessage()
        ];

        if ($ex instanceof ModelException) {
            $statusCode = 422;
            $errorResponse['error'] = 'Validation error';
            $errorResponse['messages'] = []; // Initialize

            $errorFields = $ex->getFields();
            foreach ($errorFields as $field => $messages) {
                $errorResponse['messages'][$field] = ['errors' => $messages];
            }

            if ($this->debugMode && $resource) {
                $profileType = $this->getProfileTypeForResource($resource);
                if ($profileType) {
                    if (!isset($errorResponse['debug_info'])) $errorResponse['debug_info'] = [];
                    $errorResponse['debug_info']['profile_type_hint'] = [
                        'name' => $profileType->name,
                        'model_class' => $profileType->model_class,
                        'slug' => $profileType->slug,
                        'profile_fillable_fields' => $profileType->fillable_fields,
                        'profile_response_structure' => $profileType->response_structure,
                        'profile_api_methods' => $profileType->api_methods,
                    ];

                    foreach ($errorFields as $field => $messages) {
                        if (isset($errorResponse['messages'][$field])) {
                            $fieldDetail = null;
                            // Assuming fillable_fields in ProfileType might be structured like:
                            // "fieldName": {"type": "string", "description": "...", "rules": "required|max:255"}
                            // Or it might be in a separate 'field_definitions' attribute.
                            // For now, we'll keep it simple.
                            if (is_array($profileType->fillable_fields) && isset($profileType->fillable_fields[$field]) && is_array($profileType->fillable_fields[$field])) {
                                $fieldDetail = $profileType->fillable_fields[$field];
                            }

                            $errorResponse['messages'][$field]['profile_type_guidance'] = $fieldDetail ?: "Refer to ProfileType '{$profileType->name}' (slug: '{$profileType->slug}') for schema details for field '{$field}'.";
                        }
                    }
                } else {
                     if (!isset($errorResponse['debug_info'])) $errorResponse['debug_info'] = [];
                     $errorResponse['debug_info']['profile_type_hint'] = "ProfileType not found for resource '{$resource}'. Cannot provide detailed field guidance.";
                }
            }
        }
        elseif ($ex instanceof \Illuminate\Database\Eloquent\ModelNotFoundException) {
            $statusCode = 404;
            $errorResponse['error'] = 'Resource not found';
            // Extract model name if possible
            if (method_exists($ex, 'getModel')) {
                $errorResponse['message'] = class_basename($ex->getModel()) . ' not found.';
            }
        }
        elseif ($ex instanceof ApplicationException) {
            $statusCode = 400; // Or a more specific code if ApplicationException implies it
            $errorResponse['error'] = 'Application error';
        }

        if ($this->debugMode) {
            if (!isset($errorResponse['debug_info'])) $errorResponse['debug_info'] = []; // Initialize if not set by ModelException block
            // Merge general exception details, avoiding overwrite if profile_type_hint exists
            $errorResponse['debug_info'] = array_merge([
                'exception_type' => get_class($ex),
                'file' => $ex->getFile(),
                'line' => $ex->getLine(),
                'trace_summary' => array_slice(explode("\n", $ex->getTraceAsString()), 0, 10)
            ], $errorResponse['debug_info']);
        }

        return response()->json($errorResponse, $statusCode);
    }

    /**
     * Get profile type information for a resource
     * 
     * @param string $resource Resource name/slug
     * @return Response
     */
    public function getProfileType($resource)
    {
        try {
            $profileType = $this->getProfileTypeForResource($resource);
            
            if (!$profileType) {
                return response()->json([
                    'error' => 'Profile type not found for resource: ' . $resource
                ], 404);
            }
            
            return response()->json([
                'data' => $profileType,
                'message' => 'Profile type information retrieved successfully'
            ]);
        }
        catch (Exception $ex) {
            return $this->handleError($ex, $resource);
        }
    }
    
    /**
     * Get all available profile types
     * 
     * @return Response
     */
    public function getProfileTypes()
    {
        try {
            $profileTypes = \Marty\NexGenRifle\Models\ProfileType::active()->get();
            
            return response()->json([
                'data' => $profileTypes,
                'message' => 'Profile types retrieved successfully'
            ]);
        }
        catch (Exception $ex) {
            return $this->handleError($ex, 'profile-types'); // Resource context for profile-types itself
        }
    }
}
