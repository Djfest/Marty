<?php namespace Marty\NexGenRifle\Models;

use Model;
use RainLab\User\Models\User as UserModel;

/**
 * UserProfile Model
 */
class UserProfile extends Model
{
    use \October\Rain\Database\Traits\Validation;

    /**
     * @var string The database table used by the model.
     */
    public $table = 'marty_nexgenrifle_user_profiles'; // Ensure this matches your table name

    /**
     * @var array Guarded fields
     */
    protected $guarded = ['*'];

    /**
     * @var array Fillable fields
     */
    protected $fillable = [
        'user_id',
        'bio',
        'expertise_level',
        'preferences',
        'rifle_interests',
        'social_links',
        'avatar_url'
    ];

    /**
     * @var array Validation rules
     */
    public $rules = [
        'user_id' => 'required|exists:users,id'
    ];

    /**
     * @var array JSONable attributes
     */
    protected $jsonable = [
        'preferences',
        'rifle_interests',
        'social_links'
    ];

    /**
     * @var array Attribute casting
     */
    protected $casts = [
        'preferences' => 'json',
        'rifle_interests' => 'json',
        'social_links' => 'json'
    ];

    /**
     * @var array Relations
     */
    public $belongsTo = [
        'user' => [UserModel::class] // Assumes 'user_id' foreign key
    ];

    /**
     * Extend the RainLab User model to include the profile relationship
     */
    public function boot()
    {
        parent::boot();

        // Extend RainLab User model to include relationship to this profile
        UserModel::extend(function($model) {
            $model->hasOne['profile'] = [
                'Marty\NexGenRifle\Models\UserProfile',
                'key' => 'user_id',
                'delete' => true
            ];
            
            // Add event handler to create profile when new user is created
            $model->bindEvent('model.afterCreate', function() use ($model) {
                $profile = new UserProfile();
                $profile->user_id = $model->id;
                $profile->save();
            });
        });
    }

    /**
     * Auto-create profile if not exists when accessing relationship
     */
    public static function getFromUser($user)
    {
        if (!$user) {
            return null;
        }

        $profile = $user->profile;

        if (!$profile) {
            $profile = new self();
            $profile->user_id = $user->id;
            $profile->save();
            
            // Refresh relation on user model
            $user->reloadRelations('profile');
            $profile = $user->profile;
        }

        return $profile;
    }
}
