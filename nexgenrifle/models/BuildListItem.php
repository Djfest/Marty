<?php namespace Marty\NexGenRifle\Models;

use Model;

/**
 * BuildListItem Model
 */
class BuildListItem extends Model
{
    use \October\Rain\Database\Traits\Validation;
    use \October\Rain\Database\Traits\SoftDelete;

    /**
     * @var string table associated with the model
     */
    public $table = 'marty_nexgenrifle_build_list_items';

    /**
     * @var array guarded attributes aren't mass assignable
     */
    protected $guarded = ['id'];

    /**
     * @var array fillable attributes are mass assignable
     */
    protected $fillable = [
        'build_list_id',
        'product_id',
        'supplier_id',
        'title',
        'description',
        'status',
        'price',
        'quantity',
        'priority',
        'target_date',
        'product_url',
        'affiliate_url',
        'metadata',
        'config',
        'is_acquired'
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
    protected $jsonable = ['metadata', 'config'];

    /**
     * @var array rules for validation
     */
    public $rules = [
        'build_list_id' => 'nullable|exists:marty_nexgenrifle_build_lists,id',
        'product_id' => 'nullable|exists:marty_nexgenrifle_products,id', // Assuming products table name
        'supplier_id' => 'nullable|exists:marty_nexgenrifle_suppliers,id',
        'title' => 'required|string|max:255',
        'status' => 'nullable|string', // Now nullable
        'price' => 'nullable|numeric',
        'quantity' => 'nullable|integer|min:1', // Now nullable
        'priority' => 'nullable|integer|min:1' // Now nullable
    ];

    /**
     * @var array Relations
     */
    public $belongsTo = [
        'buildList' => 'Marty\NexGenRifle\Models\BuildList',
        'product' => 'Marty\NexGenRifle\Models\Product',
        'supplier' => ['Marty\NexGenRifle\Models\Supplier', 'key' => 'supplier_id']
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
     * After save event hook
     */
    public function afterSave()
    {
        // Update parent build list totals
        if ($this->buildList) {
            $this->updateBuildListTotals();
        }
    }

    /**
     * After delete event hook
     */
    public function afterDelete()
    {
        // Update parent build list totals
        if ($this->buildList) {
            $this->updateBuildListTotals();
        }
    }

    /**
     * Get status options
     */
    public function getStatusOptions()
    {
        return [
            'planned' => 'Planned',
            'researching' => 'Researching',
            'selected' => 'Selected',
            'ordered' => 'Ordered',
            'acquired' => 'Acquired',
            'removed' => 'Removed'
        ];
    }

    /**
     * Update the parent build list totals
     */
    protected function updateBuildListTotals()
    {
        $buildList = $this->buildList;
        
        // Calculate current total
        $items = $buildList->items()
            ->where('status', '<>', 'removed')
            ->get();
            
        $total = 0;
        $acquiredCount = 0;
        
        foreach ($items as $item) {
            $itemTotal = ($item->price ?: 0) * $item->quantity;
            $total += $itemTotal;
            
            if ($item->is_acquired) {
                $acquiredCount++;
            }
        }
        
        // Update build list totals
        $buildList->current_total = $total;
        
        // Update completion status
        $totalItems = $items->count();
        $isCompleted = ($totalItems > 0) && ($acquiredCount >= $totalItems);
        $buildList->is_completed = $isCompleted;
        
        $buildList->save();
    }
}
