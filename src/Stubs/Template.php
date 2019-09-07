<?php

namespace App\Nova\Templates;

use Illuminate\Http\Request;
use Cyclops1101\PageObjectManager\Pages\Template;

class DummyTemplate extends Template {

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
}
