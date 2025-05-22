<?php namespace Marty\AiSolutions\Models;

use Model;
use Backend\Models\User;

class AiSession extends Model
{
    use \October\Rain\Database\Traits\Validation;

    /**
     * @var string The database table used by the model.
     */
    public $table = 'marty_aisolutions_sessions';

    /**
     * @var array Guarded fields
     */
    protected $guarded = ['*'];

    /**
     * @var array Fillable fields
     */
    protected $fillable = [
        'title', 
        'description', 
        'profile_type_id',
        'entity_id',
        'user_id',
        'external_id',
        'status'
    ];

    /**
     * @var array Validation rules for attributes
     */
    public $rules = [
        'title' => 'required',
        'status' => 'required|in:active,paused,completed,archived'
    ];

    /**
     * @var array Relations
     */
    public $belongsTo = [
        'profile_type' => ['Marty\Nexgenrifle\Models\ProfileType'],
        'user' => ['Backend\Models\User']
    ];

    public $hasMany = [
        'messages' => ['Marty\AiSolutions\Models\AiMessage']
    ];

    /**
     * @var array Status options for dropdown
     */
    public static $statusOptions = [
        'active' => 'Active',
        'paused' => 'Paused',
        'completed' => 'Completed',
        'archived' => 'Archived'
    ];
    
    /**
     * Before save event
     */
    public function beforeSave()
    {
        if (!$this->status) {
            $this->status = 'active';
        }
    }
}
