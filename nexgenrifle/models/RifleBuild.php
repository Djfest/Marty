<?php namespace Marty\NexGenRifle\Models;

use Model;
use BackendAuth;
use Marty\NexGenRifle\Traits\ReflectionTrait;
use Carbon\Carbon;

class RifleBuild extends Model
{
    use \October\Rain\Database\Traits\Validation;
    use ReflectionTrait;

    /**
     * @var string The database table used by the model.
     */
    public $table = 'marty_nexgenrifle_rifle_builds';

    /**
     * @var array Validation rules
     */
    public $rules = [
        'title' => 'required',
        'user_id' => 'required',
        'build_category_id' => 'required'
    ];

    /**
     * @var array Fillable fields
     */
    protected $fillable = [
        'title',
        'status',
        'user_id',
        'build_category_id',
        'total_cost',
        'notes',
        'metadata',
        'purchase_state',
        'stock_state',
        'config'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'config' => 'array',
        'metadata' => 'array',
        'specifications' => 'array',
    ];

    /**
     * @var array Fields that should be included in API responses
     */
    protected $appends = [
        'formatted_created_at',
        'formatted_updated_at',
        'status_label',
        'total_items'
    ];

    /**
     * @var array Relationships
     */
    public $belongsTo = [
        'user' => [\Backend\Models\User::class],
        'build_category' => [BuildCategory::class, 'key' => 'build_category_id']
    ];

    public $hasMany = [
        'rifle_items' => [RifleItem::class]
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->uuid)) {
                $model->uuid = \Str::uuid()->toString();
            }
            if (empty($model->status)) {
                $model->status = 'draft';
            }
            if (empty($model->purchase_state)) {
                $model->purchase_state = 'none';
            }
            if (empty($model->stock_state)) {
                $model->stock_state = 'unknown';
            }
            if (empty($model->config)) {
                $model->config = [
                    'version' => '1.0',
                    'preferences' => [],
                    'custom_fields' => []
                ];
            }
        });

        static::saved(function ($model) {
            $model->updateTotalCost();
        });
    }

    /**
     * Get status label
     * @return string
     */
    public function getStatusLabelAttribute()
    {
        $statuses = [
            'draft' => 'Draft',
            'in_progress' => 'In Progress',
            'completed' => 'Completed',
            'archived' => 'Archived'
        ];

        return $statuses[$this->status] ?? $this->status;
    }

    /**
     * Get total number of items in build
     * @return int
     */
    public function getTotalItemsAttribute()
    {
        return $this->rifle_items()->count();
    }

    /**
     * Update the total cost based on items
     */
    public function updateTotalCost()
    {
        $total = $this->rifle_items()
            ->join('marty_nexgenrifle_product_catalog', 'marty_nexgenrifle_product_catalog.id', '=', 'marty_nexgenrifle_rifle_items.product_id')
            ->sum('marty_nexgenrifle_product_catalog.price');
        
        $this->total_cost = $total;
        $this->save();
    }

    /**
     * Get API documentation for this model
     * @return array
     */
    public function getApiDocumentation()
    {
        return [
            'model' => 'RifleBuild',
            'description' => 'Custom rifle build configuration',
            'required_fields' => ['title', 'user_id', 'build_category_id'],
            'relationships' => [
                'user' => 'Backend user who owns this build',
                'build_category' => 'Build category/type',
                'rifle_items' => 'Products included in this build'
            ],
            'statuses' => [
                'draft' => 'Initial build state',
                'in_progress' => 'Build is being worked on',
                'completed' => 'Build is complete',
                'archived' => 'Build has been archived'
            ]
        ];
    }

    /**
     * Get the formatted created date
     */
    public function getFormattedCreatedAtAttribute()
    {
        return $this->created_at ? $this->created_at->format('Y-m-d H:i:s') : null;
    }

    /**
     * Get the formatted updated date
     */
    public function getFormattedUpdatedAtAttribute()
    {
        return $this->updated_at ? $this->updated_at->format('Y-m-d H:i:s') : null;
    }

    /**
     * Get reflection-based model structure
     * 
     * @return array
     */
    public function getModelStructure()
    {
        return [
            'base' => [
                'model' => class_basename($this),
                'table' => $this->table,
                'connection' => $this->getConnectionName()
            ],
            'attributes' => [
                'fillable' => $this->getFillable(),
                'guarded' => $this->getGuarded(),
                'casts' => $this->getCasts(),
                'dates' => $this->getDates(),
                'jsonable' => $this->jsonable ?? []
            ],
            'validations' => $this->rules ?? [],
            'relationships' => $this->getRelationships(),
            'methods' => $this->getMethodMetadata(ReflectionMethod::IS_PUBLIC),
        ];
    }
}
