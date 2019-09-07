<?php

namespace Cyclops1101\PageObjectManager\Http\Controllers\Page;

use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Cyclops1101\PageObjectManager\Pages\Manager;
use Laravel\Nova\Http\Requests\ResourceIndexRequest;

class CountController extends Controller
{
    /**
     * Get the resource count for a given query.
     *
     * @param ResourceIndexRequest $request
     * @param Manager $manager
     * @return Response
     */
    public function show(ResourceIndexRequest $request, Manager $manager)
    {
        return response()->json([
            'count' => $manager->queryResourcesCount($request, 'page')
        ]);
    }
}
