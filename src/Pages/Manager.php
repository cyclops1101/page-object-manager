<?php

namespace Cyclops1101\PageObjectManager\Pages;

use Cyclops1101\PageObjectManager\Exceptions\TemplateContentNotFoundException;
use Cyclops1101\PageObjectManager\Exceptions\TemplateNotFoundException;
use Illuminate\Routing\Route;
use Illuminate\Http\Resources\ConditionallyLoadsAttributes;

class Manager
{
    use ConditionallyLoadsAttributes,
        Concerns\QueriesResources,
        Concerns\ResolvesResourceCards;

    /**
     * The registered PageObjectManager Templates & Pages.
     *
     * @var Cyclops1101\PageObjectManager\Pages\TemplatesRepository
     */
    protected $repository;

    /**
     * The default current Page Template
     *
     * @var Cyclops1101\PageObjectManager\Pages\Template
     */
    protected $current;

    /**
     * Create the Main Service Singleton
     *
     * @return void
     */
    public function __construct(TemplatesRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Load the TemplateRepository with registered routes
     *
     * @return void
     */
    public function booted()
    {
        $this->repository->registerTemplates();
    }

    /**
     * Register a Template into the TemplatesRepository.
     *
     * @param string $type
     * @param string $name
     * @param string $template
     * @return Cyclops1101\PageObjectManager\Pages\Template
     */
    public function register($type, $name, $template)
    {
        return $this->repository->register($type, $name, $template);
    }

    /**
     * Load a new Page Template
     *
     * @param string $name
     * @param string $type
     * @param bool $throwOnMissing
     * @return Template
     * @throws TemplateNotFoundException
     * @throws TemplateContentNotFoundException
     */
    public function load($name, $type = 'page', $throwOnMissing = false)
    {
        $template = $this->repository->load($type, $name, $throwOnMissing);

        return $template;
    }

    /**
     * Get a loaded Template by its name
     *
     * @param string $name
     * @param string $type
     * @return null|Cyclops1101\PageObjectManager\Pages\Template
     */
    public function find($name = null, $type = 'page')
    {
        return $this->repository->getLoaded($type, $name);
    }

    /**
     * Get an option template by its name
     *
     * @param string $name
     * @param bool $throwOnMissing
     * @return mixed
     * @throws TemplateNotFoundException
     * @throws TemplateContentNotFoundException
     */
    public function block($name, $throwOnMissing = false)
    {
        return $this->find($name, 'block') ??
            $this->load($name, 'block', false, $throwOnMissing);
    }

    /**
     * Get an attribute on the current Template
     *
     * @param string $attribute
     * @return mixed
     */
    public function __get($attribute)
    {
        if (!$this->current) {
            return;
        }

        return $this->current->$attribute;
    }

    /**
     * Forward a method call to the current Template
     *
     * @param string $method
     * @param array $arguments
     * @return mixed
     */
    public function __call($method, $arguments)
    {
        if (!$this->current) {
            return;
        }

        return call_user_func_array([$this->current, $method], $arguments);
    }

    /**
     * Mimic eloquent model method and return a fake Query builder
     *
     * @return Cyclops1101\PageObjectManager\Pages\Query
     */
    public function newQueryWithoutScopes()
    {
        return new Query($this->getRepository());
    }

    /**
     * Get the underlying template repository
     * @return TemplatesRepository|Cyclops1101\PageObjectManager\Pages\TemplatesRepository
     */
    public function getRepository()
    {
        return $this->repository;
    }
}
