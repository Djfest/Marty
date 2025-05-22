<?php namespace Marty\NexGenRifle\Models;

use Model;

class SocialConnection extends Model
{
    use \October\Rain\Database\Traits\Validation;

    /**
     * @var string The database table used by the model.
     */
    public $table = 'marty_nexgenrifle_social_connections';

    /**
     * @var array Fillable fields
     */
    protected $fillable = [
        'user_id', 
        'provider', 
        'provider_id',
        'token',
        'refresh_token',
        'expires_at',
        'nickname',
        'name',
        'email',
        'avatar',
        'data'
    ];

    /**
     * @var array The attributes that should be cast to native types.
     */
    protected $casts = [
        'data' => 'json',
        'expires_at' => 'datetime'
    ];

    /**
     * @var array Jsonable fields
     */
    protected $jsonable = ['data'];
    
    /**
     * @var array Validation rules
     */
    public $rules = [
        'provider' => 'required',
        'provider_id' => 'required'
    ];
    
    /**
     * @var array Relations
     */
    public $belongsTo = [
        'user' => ['RainLab\User\Models\User', 'key' => 'user_id']
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
     * Find by provider and ID
     * @param string $provider
     * @param string $providerId
     * @return SocialConnection|null
     */
    public static function findByProviderAndId($provider, $providerId)
    {
        return self::where('provider', $provider)
            ->where('provider_id', $providerId)
            ->first();
    }
    
    /**
     * Check if token is expired
     * @return bool
     */
    public function isExpired()
    {
        if (!$this->expires_at) {
            return false;
        }
        
        return $this->expires_at->isPast();
    }
}
