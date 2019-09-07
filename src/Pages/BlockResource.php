<?php

namespace Cyclops1101\PageObjectManager\Pages;

use Laravel\Nova\Fields\Heading;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Fields\DateTime;

class BlockResource extends StaticResource
{
    /**
     * Get the URI key for the resource.
     *
     * @return string
     */
    public static function uriKey()
    {
        return 'nova-block';
    }

    /**
     * Get the displayable label of the resource.
     *
     * @return string
     */
    public static function label()
    {
        return config('page-object-manager.labels.options');
    }

    /**
     * Get the displayable singular label of the resource.
     *
     * @return string
     */
    public static function singularLabel()
    {
        return config('page-object-manager.labels.option');
    }

    /**
     * Get the base fields displayed at the top of the resource's form.
     *
     * @return array
     */
    protected function getFormIntroductionFields()
    {
        return [
            Heading::make($this->getFormattedName())
        ];
    }

    /**
     * Get the base attributes Nova Panel
     *
     * @return array
     */
    protected function getIndexTableFields()
    {
        return [
            Text::make('Name', function () {
                return $this->getFormattedName();
            })->sortable(),

            DateTime::make('Last updated on', function () {
                $updated_at = $this->getDate('updated_at');
                return $updated_at ? $updated_at->toDateTimeString() : null;
            })->format(config('page-object-manager.date_format'))->sortable(),

            //TODO: add last updated by
//            Text::make('Last Update By', function () {
//                return $this->user ? $this->user->name : null;
//            })
        ];
    }

    /**
     * Format template class name for display
     *
     * @return string
     */
    public function getFormattedName()
    {
        return ucfirst(preg_replace('/(?<!\ )[A-Z]/', ' $0', $this->getName()));
    }

}
