<?php

namespace App\Nova\Templates;

use Cyclops1101\PageObjectManager\Traits\ActionTrait;
use Illuminate\Http\Request;
use Cyclops1101\PageObjectManager\Pages\Template;

class DummyTemplate extends Template
{
    use ActionTrait;

    protected $type = 'page';

    /**
     * Get the fields displayed by the resource.
     *
     * @param Request $request
     * @return array
     */
    public function fields(Request $request)
    {
        return [];
    }

    /**
     * Get the cards available for the request.
     *
     * @param Request $request
     * @return array
     */
    public function cards(Request $request)
    {
        return [];
    }




    /**
     * Method mapped for action events in Nova which we don't use, fun
     * @return string
     */
    public function getMorphClass()
    {
        return static::class;
    }
}
