<?php namespace Marty\NexGenRifle\Models;

use Model;

class ProductCatalog extends Model
{
    use \October\Rain\Database\Traits\Validation;

    public $table = 'marty_nexgenrifle_product_catalog';

    public $rules = [
        'title' => 'required', // Remains required
        'product_category_id' => 'nullable|exists:marty_nexgenrifle_product_categories,id', // Already nullable
        'supplier_id' => 'nullable|exists:marty_nexgenrifle_suppliers,id' // Already nullable
    ];

    protected $fillable = [
        'title',
        'product_category_id',
        'supplier_id',
        'price',
        'image_url',
        'product_url',
        'affiliate_click_url',
        'is_affiliate_tracked',
        'config'
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'is_affiliate_tracked' => 'boolean',
        'config' => 'json'
    ];

    public $belongsTo = [
        'product_category' => ProductCategory::class,
        'supplier' => ['Marty\NexGenRifle\Models\Supplier', 'key' => 'supplier_id']
    ];

    public $hasMany = [
        'product_items' => ProductItem::class
    ];

    // Add timestamps property to ensure created_at/updated_at fields work correctly
    public $timestamps = true;

    // Add jsonable property for the config field
    protected $jsonable = ['config'];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->uuid)) {
                $model->uuid = \Illuminate\Support\Str::uuid()->toString();
            }
        });
    }

    // Add scope for active products
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    // Add method to get formatted price
    public function getFormattedPriceAttribute()
    {
        return '$' . number_format($this->price, 2);
    }
}
