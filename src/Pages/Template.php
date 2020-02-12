<?php

namespace Cyclops1101\PageObjectManager\Pages;

use App;
use Closure;
use ArrayAccess;
use Carbon\Carbon;
use BadMethodCallException;
use Cyclops1101\PageObjectManager\Sources\SourceInterface;
use Cyclops1101\PageObjectManager\Exceptions\ValueNotFoundException;
use Cyclops1101\PageObjectManager\Exceptions\TemplateContentNotFoundException;
use Illuminate\Database\Eloquent\Concerns\HasRelationships;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Concerns\HasAttributes;

abstract class Template implements ArrayAccess
{
    use HasAttributes,
        Concerns\HasJsonAttributes;

    /**
     * The page name
     *
     * @var string
     */
    protected $name;

    /**
     * The template key, used to identify it
     *
     * @var string
     */
    protected $key;

    /**
     * The page type
     *
     * @var string
     */
    protected $type;

    /**
     * The page's title
     *
     * @var string
     */
    protected $title;

    /**
     * The page's timestamps
     *
     * @var array
     */
    protected $dates = [];

    /**
     * The page's source
     *
     * @var mixed
     */
    protected $source;

    /**
     * Should missing values be reported
     *
     * @var bool
     */
    protected $throwOnMissing;

    /**
     * The page's raw data
     *
     * @var array
     */
    protected $raw;

    /**
     * Create A Template Instance.
     *
     * @param string $key
     * @param string $type
     * @param string $name
     * @param bool $throwOnMissing
     * @throws TemplateContentNotFoundException
     */
    public function __construct($key = null, $type = null, $name = null, $throwOnMissing = false)
    {
        $this->key = $key;
        $this->type = $type;
        $this->name = $name;
        $this->throwOnMissing = $throwOnMissing;
        $this->load($this->throwOnMissing);
    }

    /**
     * Get the template's source class name
     *
     * @return SourceInterface
     */
    public function getSource(): SourceInterface
    {
        if (is_string($this->source) || is_null($this->source)) {
            $source = $this->source ?? config('page-object-manager.default_source');
            $this->source = new $source;
            $this->source->setConfig(config('page-object-manager.sources.' . $this->source->getName()) ?? []);
        }

        return $this->source;
    }

    /**
     * Load the page's static content if needed
     *
     * @param bool $throwOnMissing
     * @return $this
     * @throws TemplateContentNotFoundException
     */
    public function load($throwOnMissing = false)
    {
        if (!$this->name || count($this->attributes)) {
            return $this;
        }

        if ($data = $this->getSource()->fetch($this)) {
            return $this->fill($data);
        }

        if ($throwOnMissing) {
            throw new TemplateContentNotFoundException($this->getSource()->getName(), $this->type, $this->name);
        }
        return $this;
    }

    /**
     * Set all the template's attributes
     *
     * @param array $data
     * @return Template
     * @throws \Exception
     */
    public function fill(array $data = [])
    {
        $this->raw = $data;
        $this->title = $data['title'] ?? null;
        $this->attributes = $data['attributes'] ?? [];

        $this->setDateIf('created_at', $data['created_at'] ?? null,
            function (Carbon $new, Carbon $current = null) {
                return (!$current || $new->lessThan($current));
            });

        $this->setDateIf('updated_at', $data['updated_at'] ?? null,
            function (Carbon $new, Carbon $current = null) {
                return (!$current || $new->greaterThan($current));
            });

        return $this;
    }

    /**
     * Create a new loaded template instance
     *
     * @param string $type
     * @param string $key
     * @param string $name
     * @param bool $throwOnMissing
     * @return Template
     * @throws TemplateContentNotFoundException
     */
    public function getNewTemplate($type, $key, $name, $throwOnMissing = false)
    {
        return new static($key, $type, $name, $throwOnMissing);
    }

    /**
     * Wrap calls to getter methods without the "get" prefix
     *
     * @param string $method
     * @param array $arguments
     * @return mixed
     */
    public function __call($method, $arguments)
    {
        $getter = 'get' . ucfirst($method);
        if (method_exists($this, $getter)) {
            return call_user_func_array([$this, $getter], $arguments);
        }

        throw new BadMethodCallException(sprintf(
            'Method %s::%s does not exist.', static::class, $method
        ));
    }

    /**
     * Retrieve the page name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Retrieve the page type
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Mimic Eloquent's getKeyName method, returning null
     *
     * @return null
     */
    public function getKeyName()
    {
        return null;
    }

    /**
     * Retrieve the page's title
     *
     * @param string $default
     * @param string $prepend
     * @param string $append
     * @return string
     */
    public function getTitle($default = null, $prepend = '', $append = '')
    {
        $title = $this->title ?? $default ?? '';
        $title = trim($prepend . $title . $append);
        return strlen($title) ? $title : null;
    }

    /**
     * Retrieve a page's attribute
     *
     * @param string $attribute
     * @param Closure $closure
     * @return mixed
     * @throws ValueNotFoundException
     */
    public function get($attribute, Closure $closure = null)
    {
        if ($closure) {
            return $closure($this->__get($attribute));
        }

        return $this->__get($attribute);
    }

    /**
     * Magically retrieve a page's attribute
     *
     * @param string $attribute
     * @return mixed
     * @throws ValueNotFoundException
     */
    public function __get($attribute)
    {
        if (!$attribute) {
            return;
        }

        if ($attribute === 'nova_page_title') {
            return $this->getTitle();
        }

        if ($attribute === 'nova_page_created_at') {
            return $this->getDate('created_at');
        }

        if (!isset($this->attributes[$attribute]) && $this->throwOnMissing) {
            $path = $this->getSource()->getErrorLocation($this->type, $this->name);
            throw new ValueNotFoundException($attribute, get_class($this), $path);
        }

        return $this->getAttribute($attribute);
    }

    /**
     * Get an attribute from the Template.
     *
     * @param string $key
     * @return mixed
     * @throws ValueNotFoundException
     */
    public function getAttribute($key)
    {
        if (!$key) {
            return;
        }

        // If the attribute exists in the attribute array or has a "get" mutator we will
        // get the attribute's value. Otherwise, we will proceed as if the developers
        // are asking for a relationship's value. This covers both types of values.
        if (array_key_exists($key, $this->attributes) ||
            $this->hasGetMutator($key)) {
            return $this->getAttributeValue($key);
        }

        if ($this->throwOnMissing) {
            $path = $this->getSource()->getFilePath($this->type, $this->name);
            throw new ValueNotFoundException($key, get_class($this), $path);
        }
    }

    /**
     * Retrieve a timestamp linked to this page resource
     *
     * @param string $timestamp
     * @return Carbon
     */
    public function getDate($timestamp = 'created_at')
    {
        return $this->dates[$timestamp] ?? null;
    }

    /**
     * Define a timestamp
     *
     * @param string $moment
     * @param mixed $date
     * @return Carbon
     * @throws \Exception
     */
    public function setDate($moment, $date = null)
    {
        if (!$date) {
            return;
        }

        if ($date instanceof Carbon) {
            return $this->dates[$moment] = $date;
        }

        return $this->dates[$moment] = new Carbon($date);
    }

    /**
     * Define a timestamp if closure condition is met
     *
     * @param string $moment
     * @param mixed $date
     * @param Closure $closure
     * @return mixed
     * @throws \Exception
     */
    public function setDateIf($moment, $date = null, Closure $closure)
    {
        if (!($date instanceof Carbon)) {
            $date = new Carbon($date);
        }

        if ($closure($date, $this->getDate($moment))) {
            return $this->setDate($moment, $date);
        }
    }

    /**
     * Magically set a page's attribute
     *
     * @param mixed $attribute
     * @param $value
     * @throws \Exception
     */
    public function __set($attribute, $value)
    {
        switch ($attribute) {
            case 'nova_page_title':
                $this->title = $value;
                break;
            case 'nova_page_created_at':
                $this->setDate('created_at', $value);
                break;

            default:
                $this->attributes[$attribute] = $value;
                break;
        }
    }

    /**
     * Determine if the given attribute exists.
     *
     * @param mixed $offset
     * @return bool
     * @throws ValueNotFoundException
     */
    public function offsetExists($offset)
    {
        return !is_null($this->__get($offset));
    }

    /**
     * Get the value for a given offset.
     *
     * @param mixed $offset
     * @return mixed
     * @throws ValueNotFoundException
     */
    public function offsetGet($offset)
    {
        return $this->__get($offset);
    }

    /**
     * Set the value for a given offset.
     *
     * @param mixed $offset
     * @param mixed $value
     * @return void
     * @throws \Exception
     */
    public function offsetSet($offset, $value)
    {
        $this->__set($offset, $value);
    }

    /**
     * Unset the value for a given offset.
     *
     * @param mixed $offset
     * @return void
     */
    public function offsetUnset($offset)
    {
        unset($this->attributes[$offset]);
    }

    /**
     * Get the fields displayed by the template.
     *
     * @param Request $request
     * @return array
     */
    abstract public function fields(Request $request);

    /**
     * Get the cards available for the request.
     *
     * @param Request $request
     * @return array
     */
    abstract public function cards(Request $request);

    /**
     * Mimic eloquent model method and return a fake Query builder
     *
     * @return Cyclops1101\PageObjectManager\Pages\Query
     */
    public function newQueryWithoutScopes()
    {
        return resolve(Manager::class)->newQueryWithoutScopes();
    }

    /**
     * Store template attributes in Source
     *
     * @return bool
     * @throws \Exception
     */
    public function save()
    {
        $this->setDateIf('created_at', Carbon::now(),
            function (Carbon $new, Carbon $current = null) {
                return !$current;
            }
        );
        return $this->getSource()->store($this);
    }

    /**
     * Get the casts array.
     *
     * @return array
     */
    public function getCasts()
    {
        return $this->casts;
    }

    /**
     * Get the attributes that should be converted to dates.
     *
     * @return array
     */
    public function getDates()
    {
        return [];
    }

    /**
     * Get the page's raw data
     *
     * @return array
     */
    public function getRaw()
    {
        return $this->raw;
    }

    public function toArray()
    {
        return array_merge($this->attributes,
            [
                'title' => $this->title,
            ]);
    }
}
