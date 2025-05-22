<?php
namespace Marty\AiSolutions\Controllers;

use Backend\Classes\Controller;
use BackendMenu;
use Marty\AiSolutions\Services\OntologySyncService;
use Flash;
use Exception;

class Sync extends Controller
{
    public $implement = ['Backend.Behaviors.FormController'];

    public $requiredPermissions = [
        'marty.aisolutions.access_sync'
    ];

    public $formConfig = 'config_form.yaml';

    public function __construct()
    {
        parent::__construct();
        BackendMenu::setContext('Marty.AiSolutions', 'aisolutions', 'sync');
        $this->addJs('/modules/backend/assets/js/october.js');
        $this->addJs('/modules/system/assets/js/framework.js');
    }

    public function index()
    {
        $this->pageTitle = 'Model Sync';
        
        $this->asExtension('FormController')->create();
        
        $profileTypes = \Marty\Djfest\Models\ProfileType::all();
        $this->vars['profileTypes'] = $profileTypes;
    }

    public function create()
    {
        $this->pageTitle = 'Model Sync';
        return $this->asExtension('FormController')->create();
    }

    public function create_onSave()
    {
        return $this->asExtension('FormController')->create_onSave();
    }

    public function onSync()
    {
        try {
            $formData = post();
            $syncAll = array_get($formData, 'sync_all', true);
            $specificModels = array_get($formData, 'specific_models', []);

            if ($syncAll) {
                $results = OntologySyncService::syncAll();
            } else {
                if (empty($specificModels)) {
                    throw new Exception('Please select at least one model to sync');
                }
                $results = OntologySyncService::syncSpecific($specificModels);
            }

            $successCount = count(array_filter($results, function ($r) {
                return $r['status'] === 'success';
            }));

            $errorCount = count(array_filter($results, function ($r) {
                return $r['status'] === 'error';
            }));

            $this->vars['results'] = $results;
            $this->vars['successCount'] = $successCount;
            $this->vars['errorCount'] = $errorCount;

            Flash::success("Sync completed: {$successCount} succeeded, {$errorCount} failed");

            return [
                '#syncResults' => $this->makePartial('sync_results')
            ];
        } catch (Exception $e) {
            Flash::error($e->getMessage());
        }
    }

    public function onGetDatabaseDetails()
    {
        $profileTypeId = post('profile_type_id');
        $profileType = \Marty\Djfest\Models\ProfileType::find($profileTypeId);

        if ($profileType) {
            $details = [
                'name' => $profileType->name,
                'table' => 'ProfileTypes',
                'fillable' => [
                    'name',
                    'model_class',
                    'metadata',
                    'description'
                ],
                'dates' => [
                    'created_at',
                    'updated_at'
                ],
                'casts' => [
                    'metadata' => 'json'
                ],
                'ai_enabled' => $profileType->metadata['ai_enabled'] ?? false
            ];

            $html = $this->makePartial('database_details', ['details' => $details]);
            return [
                '#modal-content' => $html
            ];
        }

        Flash::error('Profile Type not found.');
        return ['#modal-content' => '<p>Error loading database details.</p>'];
    }

    public function onToggleAiEnabled()
    {
        $profileTypeId = post('profile_type_id');
        $profileType = \Marty\Djfest\Models\ProfileType::find($profileTypeId);

        if ($profileType) {
            $profileType->ai_enabled = !$profileType->ai_enabled;
            $profileType->save();

            Flash::success("AI Enabled status updated for {$profileType->name}");
            return response()->json(['success' => true, 'message' => "AI Enabled status updated for {$profileType->name}", 'ai_enabled' => $profileType->ai_enabled]);
        } else {
            Flash::error('Profile Type not found.');
            return response()->json(['success' => false, 'message' => 'Profile Type not found.']);
        }
    }
}
