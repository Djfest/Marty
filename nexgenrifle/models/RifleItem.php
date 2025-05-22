<?php namespace Marty\NexGenRifle\Models;

use Model;

class RifleItem extends Model
{
    use \October\Rain\Database\Traits\Validation;

    public $table = 'marty_nexgenrifle_rifle_items';

    public $rules = [
        'rifle_build_id' => 'nullable|exists:marty_nexgenrifle_rifle_builds,id',
        'product_category_id' => 'nullable|exists:marty_nexgenrifle_product_categories,id', // Assuming this column exists in rifle_items table
        'product_item_id' => 'nullable|exists:marty_nexgenrifle_product_items,id', // This was product_id in migration
        'supplier_id' => 'nullable|exists:marty_nexgenrifle_suppliers,id'
    ];

    protected $fillable = [
        'rifle_build_id',
        'product_category_id',
        'product_item_id',
        'supplier_id',
        'price',
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
        'rifle_build' => RifleBuild::class,
        'product_category' => ProductCategory::class,
        'product_item' => ProductItem::class,
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

        static::saved(function ($model) {
            // Update build's total price
            $build = $model->rifle_build;
            $build->total_price = $build->rifle_items->sum('price');
            $build->save();
        });
    }
}
