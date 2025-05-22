<?php namespace Marty\NexGenRifle\Models;

use Model;

class ProductCategory extends Model
{
    use \October\Rain\Database\Traits\Validation;
    use \October\Rain\Database\Traits\NestedTree;

    /**
     * @var string The database table used by the model.
     */
    public $table = 'marty_nexgenrifle_product_categories';

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
        'description',
        'parent_id',
        'is_active',
        'sort_order',
        'image',
        'meta_title',
        'meta_description',
        'meta_keywords'
    ];

    /**
     * @var array Validation rules for attributes
     */
    public $rules = [
        'name' => 'required',
        'slug' => 'required|unique:marty_nexgenrifle_product_categories,slug'
    ];

    /**
     * @var array Relations
     */
    public $hasMany = [
        'products' => ['Marty\NexGenRifle\Models\Product', 'key' => 'category_id'],
        'children' => ['Marty\NexGenRifle\Models\ProductCategory', 'key' => 'parent_id']
    ];

    public $belongsTo = [
        'parent' => ['Marty\NexGenRifle\Models\ProductCategory', 'key' => 'parent_id']
    ];

    /**
     * @var array Attributes to be cast to native types
     */
    protected $casts = [
        'is_active' => 'boolean'
    ];

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
     * Scope a query to only include active categories.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
