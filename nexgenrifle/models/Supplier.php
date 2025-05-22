<?php namespace Marty\NexGenRifle\Models;

use Model;
use System\Models\File;
use Illuminate\Support\Str;

/**
 * Supplier Model
 *
 * @link https://docs.octobercms.com/3.x/extend/system/models.html
 */
class Supplier extends Model
{
    use \October\Rain\Database\Traits\Validation;
    use \October\Rain\Database\Traits\SoftDelete; // Optional: if you want soft deletes

    /**
     * @var string Name of the attribute to use for display purposes.
     */
    public $nameFrom = 'name';

    /**
     * @var string table name
     */
    public $table = 'marty_nexgenrifle_suppliers';

    /**
     * @var array dates attributes that should be mutated to dates
     */
    protected $dates = ['deleted_at']; // Optional: if using SoftDelete

    /**
     * @var array rules for validation
     */
    public $rules = [
        'name' => 'required|string|max:255',
        'slug' => 'required|string|max:255|unique:marty_nexgenrifle_suppliers,slug',
        'website_url' => 'nullable|url',
        'contact_email' => 'nullable|email',
    ];

    /**
     * @var array Fillable fields
     */
    protected $fillable = [
        'name',
        'slug',
        'base_url',
        'affiliate_url',
        'affiliate_program',
        'is_affiliate',
        'is_enabled',
        'logo_url', // If storing URL directly
        'metadata',
        'description',
        'website_url',
        'contact_name',
        'contact_email',
        'contact_phone',
        'address_street',
        'address_city',
        'address_state',
        'address_zip',
        'address_country',
        'is_active'
    ];

    /**
     * @var array JSON fields
     */
    protected $jsonable = [
        'metadata'
    ];
    
    /**
     * @var array Boolean fields / casts
     */
    protected $casts = [
        'metadata' => 'array',
    ];
    
    /**
     * @var array Attach one relations
     */
    public $attachOne = [
        'logo' => File::class // For local file uploads
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->uuid)) {
                $model->uuid = Str::uuid()->toString();
            }
        });
    }

    // If you have other relations like hasMany, belongsTo, etc., define them here
    // Example:
    // public $hasMany = [
    //     'products' => [Product::class, 'key' => 'supplier_id']
    // ];

    /**
     * Before save event to generate slug.
     */
    public function beforeValidate()
    {
        // Generate slug from name if not provided or if name changed
        if (empty($this->slug) || $this->isDirty('name')) {
            $this->slug = Str::slug($this->name);
        }
    }

    /**
     * Scope a query to only include active suppliers.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope a query to only include enabled suppliers.
     */
    public function scopeEnabled($query)
    {
        return $query->where('is_enabled', true);
    }
}
