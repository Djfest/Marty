<?php namespace Marty\NexGenRifle\Controllers;

use Backend\Classes\Controller;
use BackendMenu;
use RainLab\User\Models\User as UserModel;

class UserProfiles extends Controller
{
    /**
     * @var array Behaviors implemented by this controller
     */
    public $implement = [
        \Backend\Behaviors\FormController::class,
        \Backend\Behaviors\ListController::class,
        \Backend\Behaviors\RelationController::class
    ];

    /**
     * @var string Configuration files for behaviors
     */
    public $formConfig = 'config_form.yaml';
    public $listConfig = 'config_list.yaml';
    public $relationConfig = 'config_relation.yaml';

    /**
     * @var array Required permissions for this controller
     */
    public $requiredPermissions = [
        'marty.nexgenrifle.manage_userprofiles' 
    ];

    public function __construct()
    {
        parent::__construct();
        BackendMenu::setContext('Marty.NexGenRifle', 'nexgenrifle', 'userprofiles');
        
        // Make RainLab User plugin available to views
        $this->vars['userPlugin'] = \System\Classes\PluginManager::instance()->findByIdentifier('RainLab.User');
    }
    
    /**
     * Extend the query used for the list controller
     */
    public function listExtendQuery($query)
    {
        // Join with RainLab User table to get user details
        $query->with('user');
    }
    
    /**
     * Link to RainLab User account
     */
    public function onLinkToUser()
    {
        $profileId = post('profile_id');
        $userId = post('user_id');
        
        // Validate inputs
        if (!$profileId || !$userId) {
            return [
                'result' => 'error',
                'message' => 'Profile ID and User ID are required.'
            ];
        }
        
        // Find the models
        $profile = \Marty\NexGenRifle\Models\UserProfile::findOrFail($profileId);
        $user = UserModel::findOrFail($userId);
        
        // Link the profile to the user
        $profile->user_id = $user->id;
        $profile->save();
        
        return [
            'result' => 'success',
            'message' => 'User profile linked successfully.'
        ];
    }
    
    /**
     * Find RainLab users for linking
     */
    public function onSearchUsers()
    {
        $searchTerm = post('term');
        
        if (empty($searchTerm)) {
            return [];
        }
        
        $users = UserModel::where('email', 'like', "%{$searchTerm}%")
            ->orWhere('username', 'like', "%{$searchTerm}%")
            ->orWhere('name', 'like', "%{$searchTerm}%")
            ->get(['id', 'email', 'username', 'name'])
            ->map(function ($user) {
                return [
                    'id' => $user->id,
                    'text' => "{$user->name} ({$user->email})"
                ];
            });
            
        return ['results' => $users];
    }
    
    /**
     * Initialize a new socialite connection
     */
    public function onInitSocialiteConnect()
    {
        $provider = post('provider');
        
        if (!$provider) {
            return [
                'result' => 'error',
                'message' => 'Provider is required.'
            ];
        }
        
        // Store provider in session for callback
        \Session::put('socialite_provider', $provider);
        \Session::put('socialite_admin_mode', true);
        
        // Get redirect URL
        $url = \Backend::url("marty/nexgenrifle/userprofiles/socialiteCallback/{$provider}");
        
        return [
            'redirect' => $url
        ];
    }
    
    /**
     * Handle socialite callback
     */
    public function socialiteCallback($provider)
    {
        // This method will be implemented when Socialite is integrated
        // For now it's just a placeholder
        
        \Flash::success("Socialite callback for provider: {$provider}");
        return redirect()->to(\Backend::url('marty/nexgenrifle/userprofiles'));
    }
}
