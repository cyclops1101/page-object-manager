<?php

namespace Cyclops1101\PageObjectManager\Http\Controllers\Block;

use Illuminate\Routing\Controller;
use Cyclops1101\PageObjectManager\Pages\Manager;
use Laravel\Nova\Http\Requests\ResourceIndexRequest;

class CountController extends Controller
{
    /**
     * Get the resource count for a given query.
     *
     * @param  \Laravel\Nova\Http\Requests\ResourceIndexRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function show(ResourceIndexRequest $request, Manager $manager)
    {
        return response()->json([
            'count' => $manager->queryResourcesCount($request, 'block')
        ]);
    }
}
