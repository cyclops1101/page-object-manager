<?php

namespace Cyclops1101\PageObjectManager\Pages\Concerns;

use Illuminate\Support\Collection;
use Laravel\Nova\Fields\FieldCollection;
use Laravel\Nova\Http\Requests\NovaRequest;
use Cyclops1101\PageObjectManager\Http\Controllers\Page\IndexController as PageResourceIndexController;
use Cyclops1101\PageObjectManager\Http\Controllers\Block\IndexController as BlockResourceIndexController;

trait ResolvesResourceFields
{
    /**
     * Get the fields that are available for the given request.
     *
     * @param NovaRequest $request
     * @return Collection
     */
    public function availableFields(NovaRequest $request)
    {
        if($this->isDisplayingIndexFields($request)) {
            return new FieldCollection($this->getIndexTableFields($request));
        }

        return new FieldCollection(array_values($this->filter($this->fields($request))));
    }

    /**
     * Check if incoming request displays an index page
     *
     * @param NovaRequest $request
     * @return bool
     */
    protected function isDisplayingIndexFields(NovaRequest $request)
    {
        $indexActions = [
            PageResourceIndexController::class . '@handle',
            BlockResourceIndexController::class . '@handle'
        ];

        return in_array(
            $request->route()->getAction()['controller'],
            $indexActions
        );
    }
}
