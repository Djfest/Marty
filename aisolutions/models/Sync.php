<?php namespace Marty\AiSolutions\Models;

use Model;

class Sync extends Model
{
    use \October\Rain\Database\Traits\Validation;

    public $table = 'marty_aisolutions_syncs';

    protected $fillable = [
        'namespace',
        'model_name',
        'sync_status',
        'last_synced_at',
        'metadata'
    ];

    protected $jsonable = ['metadata'];

    public $timestamps = true;

    public $rules = [
        'namespace' => 'required',
        'model_name' => 'required'
    ];
}