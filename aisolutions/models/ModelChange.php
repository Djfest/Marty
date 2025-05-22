<?php namespace Marty\AiSolutions\Models;

use Model;
use Backend\Models\User;

class ModelChange extends Model
{
    use \October\Rain\Database\Traits\Validation;

    /**
     * @var string The database table used by the model.
     */
    public $table = 'marty_aisolutions_model_changes';

    /**
     * @var array Guarded fields
     */
    protected $guarded = ['*'];

    /**
     * @var array Fillable fields
     */
    protected $fillable = [
        'model_type',
        'model_id',
        'field',
        'old_value',
        'new_value',
        'user_id',
        'session_id',
        'message_id'
    ];

    /**
     * @var array Validation rules for attributes
     */
    public $rules = [
        'model_type' => 'required',
        'model_id' => 'required',
        'field' => 'required'
    ];

    /**
     * @var array Relations
     */
    public $belongsTo = [
        'user' => ['Backend\Models\User'],
        'session' => ['Marty\AiSolutions\Models\AiSession'],
        'message' => ['Marty\AiSolutions\Models\AiMessage']
    ];

    /**
     * Get the model
     */
    public function getModel()
    {
        $modelClass = $this->model_type;
        if (!class_exists($modelClass)) {
            return null;
        }
        
        return $modelClass::find($this->model_id);
    }
}
