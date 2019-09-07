<?php

namespace Cyclops1101\PageObjectManager\Http\Controllers;

use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Cyclops1101\PageObjectManager\Pages\Manager;
use Illuminate\Support\Collection;
use Laravel\Nova\Http\Requests\NovaRequest;

class CardController extends Controller
{

    /**
     * List the cards for the given resource.
     *
     * @param Manager $manager
     * @param NovaRequest $request
     * @return Collection
     */
    public function index(Manager $manager, NovaRequest $request)
    {
        return $manager->availableCards($request);
    }

}
