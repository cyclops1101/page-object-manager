<?php

namespace Cyclops1101\PageObjectManager\Http\Controllers\Page;

use Cyclops1101\PageObjectManager\Pages\Manager;
use Illuminate\Support\Collection;
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
        return config('page-object-manager.labels.pages');
    }

    /**
     * Callback to retrieve the resource index items
     *
     * @param ResourceIndexRequest $request
     * @param Manager $manager
     * @return Collection
     */
    protected function resourceIndexItems(ResourceIndexRequest $request, Manager $manager) {
        return $manager->queryIndexResources($request, 'page');
    }
}
