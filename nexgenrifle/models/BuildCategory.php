<?php namespace Marty\NexGenRifle\Models;

use Model;

class BuildCategory extends Model
{
    use \October\Rain\Database\Traits\Validation;

    /**
     * @var string The database table used by the model.
     */
    public $table = 'marty_nexgenrifle_build_categories';

    /**
     * @var array Validation rules
     */
    public $rules = [
        'name' => 'required',
        'slug' => 'required|unique:marty_nexgenrifle_build_categories'
    ];

    /**
     * @var array Fillable fields
     */
    protected $fillable = [
        'name',
        'slug',
        'description'
    ];

    /**
     * @var array Relations
     */
    public $hasMany = [
        'rifle_builds' => [RifleBuild::class, 'key' => 'build_category_id']
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
}
