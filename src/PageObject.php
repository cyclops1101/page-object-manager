<?php

namespace Cyclops1101\PageObjectManager;

use Cyclops1101\PageObjectManager\Pages\BlockResource;
use Cyclops1101\PageObjectManager\Pages\PageResource;
use Illuminate\View\View;
use Laravel\Nova\Nova;
use Laravel\Nova\Tool;

class PageObject extends Tool
{
    /**
     * Perform any tasks that need to happen when the tool is booted.
     *
     * @return void
     */
    public function boot()
    {
        Nova::resources([
            PageResource::class,
            BlockResource::class,
        ]);
    }

    /**
     * Build the view that renders the navigation links for the tool.
     *
     * @return View
     */
    public function renderNavigation()
    {
        return view('page-object-manager::navigation');
    }
}
