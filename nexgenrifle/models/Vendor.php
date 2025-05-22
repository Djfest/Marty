<?php namespace Marty\NexGenRifle\Models;

use Model;
use System\Models\File;
use Illuminate\Support\Str;

class Vendor extends Model
{
    use \October\Rain\Database\Traits\Validation;

    /**
     * @var string Name of the attribute to use for display purposes.
     */
    public $nameFrom = 'name';

    /**
     * @var string The database table used by the model
     */
    public $table = 'marty_nexgenrifle_vendors';
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
        'logo_url',
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
     * @var array Validation rules
     */
    public $rules = [
        'name' => 'required',
        'slug' => 'required|unique:marty_nexgenrifle_vendors',
        'website_url' => 'nullable|url'
    ];

    /**
     * @var array JSON fields
     */
    protected $jsonable = [
        'metadata'
    ];
    
    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'metadata' => 'array',
    ];
    
    /**
     * @var array Relationships
     */
    public $hasMany = [
        'productCatalogs' => [ProductCatalog::class, 'key' => 'vendor_id']
    ];
    public $attachOne = [
        'logo' => File::class
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

    
    /**
     * Before save event
     */
    public function beforeSave()
    {
        // Generate slug from name if not provided
        if (empty($this->slug)) {
            $this->slug = Str::slug($this->name);
        }
    }
    
    /**
     * Scope a query to only include active vendors.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
