<?php

namespace Cyclops1101\PageObjectManager\Pages\Concerns;

use Illuminate\Support\Collection;
use Laravel\Nova\Resource;
use Cyclops1101\PageObjectManager\Pages\Template;
use Cyclops1101\PageObjectManager\Pages\PageResource;
use Cyclops1101\PageObjectManager\Pages\BlockResource;
use Laravel\Nova\Http\Requests\ResourceIndexRequest;

trait QueriesResources
{
    /**
     * Retrieves registered static resource for given request and type
     *
     * @param ResourceIndexRequest $request
     * @param  string $type
     * @return Collection
     */
    public function queryIndexResources(ResourceIndexRequest $request, $type) {
        $query = $this->newQueryWithoutScopes();
        return $query->whereType($type)->get(false)->map(function($template) use ($type) {
            return $this->getResourceForType($type, $template);
        });
    }

    /**
     * Retrieves registered page type count for request
     *
     * @param ResourceIndexRequest $request
     * @param  string $type
     * @return Collection
     */
    public function queryResourcesCount(ResourceIndexRequest $request, $type)
    {
        return $this->newQueryWithoutScopes()->whereType($type)->get(false)->count();
    }

    /**
     * Creates a new Nova Resource for given type and Template
     *
     * @param  string $type
     * @param Template $resource
     * @return Resource
     */
    protected function getResourceForType($type, Template $resource) {
        switch ($type) {
            case 'page': return new PageResource($resource);
            case 'block': return new BlockResource($resource);
        }
    }
}
