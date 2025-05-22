<?php namespace Marty\AiSolutions\Components;

use Cms\Classes\ComponentBase;

class AiConsole extends ComponentBase
{
    public function componentDetails()
    {
        return [
            'name'        => 'AI Console',
            'description' => 'Provides an interface for AI interactions and code sessions'
        ];
    }

    public function defineProperties()
    {
        return [];
    }
}
