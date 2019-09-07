<?php

namespace Cyclops1101\PageObjectManager;

use Cyclops1101\PageObjectManager\Pages\Manager;
use Cyclops1101\PageObjectManager\Pages\Template;

class PageRouteMacros
{

    /**
     * Get or set the PageObjectManager template attached to the route.
     *
     * @param  string|null $template
     * @return $this|string|null
     */
    public function template()
    {
        return function($template = null) {
            if (is_null($template)) {
                return $this->action['nova-page-template'] ?? null;
            }

            $this->action['nova-page-template'] = $template;

            return $this;
        };
    }

}
