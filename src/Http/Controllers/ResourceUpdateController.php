<?php

namespace Cyclops1101\PageObjectManager\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Laravel\Nova\Http\Requests\UpdateResourceRequest;

abstract class ResourceUpdateController extends Controller
{
    /**
     * The queried resource's name
     *
     * @var string
     */
    protected $resourceName;

    /**
     * Update a resource.
     *
     * @param UpdateResourceRequest $request
     * @return Response
     * @throws AuthorizationException
     */
    public function handle(UpdateResourceRequest $request)
    {
        $route = call_user_func($request->getRouteResolver());
        $route->setParameter('resource', $this->resourceName);
        $request->findResourceOrFail()->authorizeToUpdate($request);

        $resource = $request->resource();

        $resource::validateForUpdate($request);

        $template = $request->findModelQuery()->firstOrFail();

        if ($this->templateHasBeenUpdatedSinceRetrieval($request, $template)) {
            return response('', 409);
        }

        [$template, $callbacks] = $resource::fillForUpdate($request, $template);

        tap($template)->save();
        collect($callbacks)->each->__invoke();

        return response()->json([
            'id' => $template->getKey(),
            'resource' => $template->getAttributes(),
            'redirect' => $resource::redirectAfterUpdate($request, $request->newResourceWith($template)),
        ]);
    }

    /**
     * Determine if the resource has been updated since it was retrieved.
     *
     * @param UpdateResourceRequest $request
     * @param  \Cyclops1101\PageObjectManager\Pages\Template  $template
     * @return void
     */
    protected function templateHasBeenUpdatedSinceRetrieval(UpdateResourceRequest $request, $template)
    {
        $date = $template->getDate('updated_at');
        return $request->input('_retrieved_at') && $date && $date->gt(
            Carbon::createFromTimestamp($request->input('_retrieved_at'))
        );
    }
}
