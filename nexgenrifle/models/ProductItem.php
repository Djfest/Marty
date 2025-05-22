<?php namespace Marty\NexGenRifle\Models;

use Model;

class ProductItem extends Model
{
    use \October\Rain\Database\Traits\Validation;
    
    public $table = 'marty_nexgenrifle_product_items';
    
    protected $fillable = [
        'title',
        'product_catalog_id',
        'build_list_id',
        'status',
        'price',
        'quantity',
        'priority',
        'notes',
        'metadata'
    ];
    
    protected $casts = [
        'price' => 'decimal:2',
        'quantity' => 'integer',
        'priority' => 'integer',
        'metadata' => 'json'
    ];
    
    protected $jsonable = ['metadata'];
    
    public $rules = [
        'title' => 'required',
        'product_catalog_id' => 'nullable|exists:marty_nexgenrifle_product_catalog,id',
        'build_list_id' => 'nullable|exists:marty_nexgenrifle_build_lists,id'
    ];

    // Inverse relationship to ProductCatalog
    public $belongsTo = [
        'product_catalog' => [ProductCatalog::class, 'key' => 'product_catalog_id'],
        'build_list' => ['Marty\NexGenRifle\Models\BuildList', 'key' => 'build_list_id']
    ];
    
    // Add timestamps property to ensure created_at/updated_at fields work correctly
    public $timestamps = true;
    
    // Add boot method for creating UUID
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->uuid)) {
                $model->uuid = \Illuminate\Support\Str::uuid()->toString();
            }
        });
    }
}
