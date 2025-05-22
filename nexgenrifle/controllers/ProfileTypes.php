<?php namespace Marty\NexGenRifle\Controllers;

use Backend\Classes\Controller;
use BackendMenu;
use Flash;
use Marty\NexGenRifle\Models\ProfileType;
use Exception;

class ProfileTypes extends Controller
{
    public $implement = [
        \Backend\Behaviors\ListController::class,
        \Backend\Behaviors\FormController::class,
        \Backend\Behaviors\RelationController::class
    ];
    
    public $listConfig = 'config_list.yaml';
    public $formConfig = 'config_form.yaml';
    public $relationConfig = 'config_relation.yaml';
    
    public $requiredPermissions = [
        'marty.nexgenrifle.manage_profiletypes' 
    ];

    public function __construct()
    {
        parent::__construct();
        BackendMenu::setContext('Marty.NexGenRifle', 'nexgenrifle', 'profiletypes');
    }
    
    public function index()
    {
        $this->asExtension('ListController')->index();
    }
    
    /**
     * Override to handle special json fields
     */
    public function formBeforeSave($model)
    {
        // Ensure correct handling of JSON fields
        foreach ($model->jsonable as $field) {
            if (is_string($model->$field)) {
                try {
                    $model->$field = json_decode($model->$field, true);
                    if (json_last_error() !== JSON_ERROR_NONE) {
                        throw new Exception("Invalid JSON in field {$field}");
                    }
                } catch (Exception $e) {
                    Flash::error("Error processing JSON field {$field}: " . $e->getMessage());
                }
            }
        }
    }
    
    /**
     * Test model resolution for a profile type
     */
    public function onTestModelResolution()
    {
        $profileId = post('profile_id');
        $profile = ProfileType::find($profileId);
        
        if (!$profile) {
            return [
                'result' => 'error',
                'message' => 'Profile not found'
            ];
        }
        
        $model = $profile->resolveModel();
        
        if (!$model) {
            return [
                'result' => 'error',
                'message' => 'Could not resolve model class: ' . $profile->model_class
            ];
        }
        
        return [
            'result' => 'success',
            'message' => 'Successfully resolved model: ' . get_class($model),
            'fields' => $profile->getFieldInformation()
        ];
    }
    
    /**
     * Generate AI prompt from profile
     */
    public function onGenerateAiPrompt()
    {
        $profileId = post('profile_id');
        $context = post('context', '');
        $profile = ProfileType::find($profileId);
        
        if (!$profile) {
            return [
                'result' => 'error',
                'message' => 'Profile not found'
            ];
        }
        
        $prompt = $profile->generateAiPrompt($context);
        
        return [
            'result' => 'success',
            'prompt' => $prompt
        ];
    }
    
    /**
     * Scan for models across all plugins
     */
    // public function onScanForModels()
    // {
    //     $pluginManager = \System\Classes\PluginManager::instance();
    //     $plugins = $pluginManager->getPlugins();
    //     $models = [];
        
    //     foreach ($plugins as $plugin) {
    //         $pluginPath = $plugin->getPluginPath();
    //         $modelsPath = $pluginPath . '/models';
            
    //         if (!file_exists($modelsPath)) {
    //             continue;
    //         }
            
    //         $files = glob($modelsPath . '/*.php');
    //         foreach ($files as $file) {
    //             $className = basename($file, '.php');
    //             $namespace = get_class($plugin);
    //             $namespace = substr($namespace, 0, strrpos($namespace, '\\')) . '\\Models\\' . $className;
                
    //             if (class_exists($namespace)) {
    //                 $models[] = [
    //                     'class' => $namespace,
    //                     'name' => $className,
    //                     'file' => $file,
    //                     'plugin' => $plugin->getPluginIdentifier()
    //                 ];
    //             }
    //         }
    //     }
        
    //     return [
    //         'result' => 'success',
    //         'models' => $models
    //     ];
    // }
}
