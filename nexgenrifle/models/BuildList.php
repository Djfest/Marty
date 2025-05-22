<?php namespace Marty\NexGenRifle\Models;

use Model;

/**
 * BuildList Model
 */
class BuildList extends Model
{
    use \October\Rain\Database\Traits\Validation;
    use \October\Rain\Database\Traits\SoftDelete;

    /**
     * @var string table associated with the model
     */
    public $table = 'marty_nexgenrifle_build_lists';

    /**
     * @var array guarded attributes aren't mass assignable
     */
    protected $guarded = ['id'];

    /**
     * @var array fillable attributes are mass assignable
     */
    protected $fillable = [
        'name',
        'description',
        'list_type',
        'status',
        'target_date',
        'total_budget',
        'metadata',
        'config',
        'is_completed',
        'user_id'
    ];

    /**
     * @var array dates attributes that should be mutated to dates
     */
    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
        'target_date'
    ];

    /**
     * @var array jsonable attribute names that are json encoded and decoded from the database
     */
    protected $jsonable = ['config', 'metadata'];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'config' => 'array',
        'metadata' => 'array',
    ];

    /**
     * @var array Mapping of list type string identifiers to integer IDs.
     * These string keys are expected from the API.
     */
    public static $listTypeStringMap = [
        'rifle_build' => 1,
        'gift_list' => 2,
        'project_list' => 3,
    ];

    /**
     * @var array rules for validation
     */
    public $rules = [
        'name' => 'required|string|max:255',
        'list_type' => 'nullable|build_list_type_validation', // Now nullable
        'status' => 'nullable|string|in:planning,in_progress,completed,on_hold,archived', // Explicitly nullable
        'target_date' => 'nullable|date',
        'total_budget' => 'nullable|numeric',
        'user_id' => 'nullable|exists:users,id'
    ];

    /**
     * @var array Relations
     */
    public $hasMany = [
        'items' => 'Marty\NexGenRifle\Models\BuildListItem'
    ];

    public $belongsTo = [
        'user' => 'RainLab\User\Models\User'
    ];

    /**
     * Boot method for model events and custom validation registration.
     */
    public static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->uuid)) {
                $model->uuid = \Str::uuid()->toString();
            }
            if (empty($model->status)) {
                $model->status = 'planning'; // Default status if not provided
            }
            // If user_id is not set and a user is authenticated, assign the authenticated user's ID
            if (empty($model->user_id) && \Auth::check()) {
                $model->user_id = \Auth::getUser()->id;
            }
        });

        \Validator::extend('build_list_type_validation', function ($attribute, $value, $parameters, $validator) {
            $listTypeStringMap = self::$listTypeStringMap; // Use self:: for static property access
            $customMessages = [];

            if (is_string($value)) {
                if (!array_key_exists($value, $listTypeStringMap)) {
                    $customMessages['build_list_type_validation'] = 'The selected ' . $attribute.' is invalid. Must be one of: ' . implode(', ', array_keys($listTypeStringMap));
                    $validator->setCustomMessages($customMessages);
                    return false;
                }
            } elseif (is_numeric($value)) {
                if (!in_array((int)$value, array_values($listTypeStringMap), true)) {
                    $customMessages['build_list_type_validation'] = 'The selected ' . $attribute.' is invalid. Must be one of the configured numeric values: ' . implode(', ', array_values($listTypeStringMap));
                    $validator->setCustomMessages($customMessages);
                    return false;
                }
            } else {
                // Not a string and not numeric
                $customMessages['build_list_type_validation'] = 'The ' . $attribute.' must be a valid string identifier (e.g., "rifle_build") or its corresponding numeric ID.';
                $validator->setCustomMessages($customMessages);
                return false;
            }

            return true;
        });
    }


    /**
     * Before save event.
     * Note: UUID generation has been moved to the static::creating event in boot().
     * This method can be removed if no other beforeSave logic is needed.
     */
    // public function beforeSave() { }

    /**
     * Get list type options
     */
    public function getListTypeOptions()
    {
        return [
            1 => 'Rifle Build', // Numeric value for rifle_build
            2 => 'Gift List',   // Numeric value for gift_list
            3 => 'Project List' // Numeric value for project_list
        ];
    }

    /**
     * Get status options
     */
    public function getStatusOptions()
    {
        return [
            'planning' => 'Planning',
            'in_progress' => 'In Progress',
            'completed' => 'Completed',
            'on_hold' => 'On Hold',
            'archived' => 'Archived'
        ];
    }
}
