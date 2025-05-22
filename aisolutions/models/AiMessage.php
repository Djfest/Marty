<?php namespace Marty\AiSolutions\Models;

use Model;
use Backend\Models\User;

class AiMessage extends Model
{
    use \October\Rain\Database\Traits\Validation;

    /**
     * @var string The database table used by the model.
     */
    public $table = 'marty_aisolutions_messages';

    /**
     * @var array Guarded fields
     */
    protected $guarded = ['*'];

    /**
     * @var array Fillable fields
     */
    protected $fillable = [
        'session_id',
        'content',
        'is_from_ai',
        'user_id',
        'external_id',
        'metadata'
    ];

    /**
     * @var array Validation rules for attributes
     */
    public $rules = [
        'session_id' => 'nullable|integer|exists:marty_aisolutions_sessions,id',
        'content' => 'required'
    ];

    /**
     * @var array Attributes to be cast to native types
     */
    protected $casts = [
        'is_from_ai' => 'boolean',
        'metadata' => 'json'
    ];

    /**
     * @var array Jsonable fields
     */
    protected $jsonable = [
        'metadata'
    ];

    /**
     * @var array Relations
     */
    public $belongsTo = [
        'session' => ['Marty\AiSolutions\Models\AiSession'],
        'user' => ['Backend\Models\User']
    ];

    /**
     * Get metadata attribute
     */
    public function getMetadataAttribute($value)
    {
        return $value ? json_decode($value, true) : [];
    }

    /**
     * Set metadata attribute
     */
    public function setMetadataAttribute($value)
    {
        $this->attributes['metadata'] = is_array($value) ? json_encode($value) : $value;
    }
}
