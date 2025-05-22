<?php namespace Marty\NexGenRifle\Models;

use Model;
use RainLab\User\Models\User as UserModel;
use Marty\NexGenRifle\Traits\ReflectionTrait;

class Profile extends Model
{
    use \October\Rain\Database\Traits\Validation;
    use \October\Rain\Database\Traits\SoftDelete;
    use ReflectionTrait;

    /**
     * @var string The database table used by the model.
     */
    public $table = 'marty_nexgenrifle_profiles';

    /**
     * @var array Guarded fields
     */
    protected $guarded = ['*'];

    /**
     * @var array Fillable fields
     */
    protected $fillable = [
        'name',
        'slug',
        'bio',
        'contact_email',
        'contact_phone',
        'website',
        'user_id',
        'metadata',
        'location',
        'social_links',
        'is_active'
    ];

    /**
     * @var array Attributes that should be cast to native types
     */
    protected $casts = [
        'is_active' => 'boolean',
        'metadata' => 'json',
        'social_links' => 'json'
    ];
    
    /**
     * @var array JSON fields
     */
    protected $jsonable = [
        'metadata',
        'social_links'
    ];

    /**
     * @var array Validation rules
     */
    public $rules = [
        'name' => 'required',
        'slug' => 'required|unique:marty_nexgenrifle_profiles',
        'contact_email' => 'nullable|email'
    ];

    /**
     * @var array Relations
     */
    public $belongsTo = [
        'user' => [UserModel::class]
    ];
    
    public $hasMany = [
        'rifleBuilds' => [RifleBuild::class, 'key' => 'user_id', 'otherKey' => 'user_id']
    ];
    
    public $attachOne = [
        'avatar' => [\System\Models\File::class]
    ];

    /**
     * @var array Dates
     */
    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at'
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
     * Before save event
     */
    public function beforeSave()
    {
        if (empty($this->slug)) {
            $this->slug = str_slug($this->name);
        }
    }
    
    /**
     * Scope for active profiles
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
    
    /**
     * Find a profile by user ID
     */
    public static function findByUserId($userId)
    {
        return static::where('user_id', $userId)->first();
    }

    /**
     * Export model schema with reflection data
     * 
     * @return array
     */
    public function exportSchema()
    {
        return [
            'model' => class_basename($this),
            'table' => $this->table,
            'fields' => $this->getPropertyMetadata(ReflectionProperty::IS_PUBLIC),
            'methods' => $this->getMethodMetadata(ReflectionMethod::IS_PUBLIC),
            'relationships' => $this->getRelationships(),
            'rules' => $this->rules ?? [],
            'schema' => $this->getSchemaInfo()
        ];
    }
}
