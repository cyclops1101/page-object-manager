<?php

namespace Cyclops1101\PageObjectManager;

use Illuminate\Support\Facades\Facade;
use Cyclops1101\PageObjectManager\Pages\Manager;

class PageFacade extends Facade {

    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return Manager::class;
    }

}
