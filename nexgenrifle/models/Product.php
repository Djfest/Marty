<?php namespace Marty\NexGenRifle\Models;

use Model;

/**
 * Product Model
 */
class Product extends Model
{
    use \October\Rain\Database\Traits\Validation;

    /**
     * @var string The database table used by the model.
     */
    public $table = 'marty_nexgenrifle_products';

    /**
     * @var array Validation rules for attributes
     */
    public $rules = [
        'name' => 'required',
        'slug' => 'required|unique:marty_nexgenrifle_products,slug'
    ];

    /**
     * @var array Fillable fields
     */
    protected $fillable = [
        'name',
        'slug',
        'description',
        'short_description',
        'brand',
        'category_id',
        'price',
        'sku',
        'stock',
        'is_active',
        'is_featured',
        'image',
        'gallery',
        'metadata'
    ];

    /**
     * @var array Attributes to be cast to native types
     */
    protected $casts = [
        'price' => 'float',
        'stock' => 'integer',
        'is_active' => 'boolean',
        'is_featured' => 'boolean',
        'metadata' => 'array'
    ];

    /**
     * @var array Jsonable fields
     */
    protected $jsonable = ['gallery', 'metadata'];

    /**
     * @var array Relations
     */
    public $belongsTo = [
        'category' => ['Marty\NexGenRifle\Models\ProductCategory']
    ];

    /**
     * Scope a query to only include active products.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope a query to only include featured products.
     */
    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }
    
    /**
     * Generate a unique slug before saving
     */
    public function beforeSave()
    {
        $this->slug = isset($this->slug) ? $this->slug : \Str::slug($this->name);
        
        // Ensure UUID exists
        if (empty($this->uuid)) {
            $this->uuid = \Illuminate\Support\Str::uuid()->toString();
        }
    }
    
    /**
     * Format the price for display
     */
    public function getFormattedPriceAttribute()
    {
        return '$' . number_format($this->price, 2);
    }
}