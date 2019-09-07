<?php

namespace Cyclops1101\PageObjectManager\Http\Controllers\Block;

use Cyclops1101\PageObjectManager\Pages\Manager;
use Laravel\Nova\Http\Requests\ResourceIndexRequest;
use Cyclops1101\PageObjectManager\Http\Controllers\ResourceIndexController;

class IndexController extends ResourceIndexController
{
    /**
     * Resource label callback
     *
     * @return string
     */
    protected function resourceLabel() {
        return config('page-object-manager.labels.options');
    }

    /**
     * Callback to retrieve the resource index items
     *
     * @param  \Laravel\Nova\Http\Requests\ResourceIndexRequest $request
     * @param  \Cyclops1101\PageObjectManager\Pages\Manager $manager
     * @return \Illuminate\Support\Collection
     */
    protected function resourceIndexItems(ResourceIndexRequest $request, Manager $manager) {
        return $manager->queryIndexResources($request, 'block');
    }
}
