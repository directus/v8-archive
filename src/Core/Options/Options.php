<?php

declare(strict_types=1);

namespace Directus\Core\Options;

use Directus\Core\Options\Exception\EmptySchema;
use Directus\Core\Options\Exception\InvalidOption;
use Directus\Core\Options\Exception\MissingOptions;
use Directus\Core\Options\Exception\UnknownOptions;
use Illuminate\Support\Arr;

/**
 * Options collection and validation class.
 */
class Options
{
    /**
     * Collection items.
     *
     * @var array
     */
    protected $values = [];

    /**
     * List of schema rules.
     *
     * @var array
     */
    protected $schema = [];

    /**
     * List of properties.
     *
     * @var array
     */
    protected $props = [];

    /**
     * List of required props.
     *
     * @var array
     */
    protected $required = [];

    /**
     * List of optional props.
     *
     * @var array
     */
    protected $optional = [];

    /**
     * Collection constructor.
     *
     * @param array $items
     * @param array $values
     */
    public function __construct(array $schema, ?array $values = null)
    {
        $this->values = [];

        if (empty($schema)) {
            throw new EmptySchema();
        }

        $this->schema = array_replace_recursive([], ...array_map(function ($key, $value) {
            if (\is_string($key)) {
                if (!\is_array($value)) {
                    $value = [
                        'default' => $value,
                    ];
                }
            } else {
                $key = $value;
                $value = [];
            }

            return array_replace_recursive([], [
                "{$key}" => [
                    'validate' => function () { return true; },
                    'convert' => function ($value) { return $value; },
                ],
            ], [
                "{$key}" => $value,
            ]);
        }, array_keys($schema), array_values($schema)));

        $this->props = array_keys($this->schema);

        $this->required = Arr::where($this->props, function ($prop) {
            return !\array_key_exists('default', $this->schema[$prop]);
        });

        $this->optional = Arr::where($this->props, function ($prop) {
            return \array_key_exists('default', $this->schema[$prop]);
        });

        if (null !== $values) {
            $this->feed($values);
        }
    }

    /**
     * Undocumented function.
     */
    public function feed(array $data)
    {
        $keys = array_keys(Arr::dot($data));
        $others = array_filter(array_diff($this->props, $keys), function ($key) {
            return !Arr::has($this->schema, $key);
        });

        if (!empty($others)) {
            throw new UnknownOptions($others);
        }

        $missing = Arr::where($this->required, function ($key) use ($data) {
            return !Arr::has($data, $key);
        });

        if (!empty($missing)) {
            throw new MissingOptions($missing);
        }

        $this->values = $data;
        foreach ($this->schema as $key => $prop) {
            if (Arr::has($data, $key)) {
                $value = Arr::get($data, $key);
            } else {
                $value = $prop['default'];
            }

            if (!$prop['validate']($value)) {
                throw new InvalidOption($key);
            }

            Arr::set($this->values, $key, $prop['convert']($value));
        }
    }

    /**
     * Sets an item in the collection with the given key-value.
     *
     * @param mixed $value
     */
    public function set(string $key, $value): void
    {
        Arr::set($this->values, $key, $value);
    }

    /**
     * Gets an item in the collection with the given key.
     *
     * @param mixed $default
     */
    public function get(string $key)
    {
        return Arr::get($this->values, $key);
    }

    /**
     * Checks wheter an item exists in the collection with the given key.
     */
    public function has(string $key): bool
    {
        return Arr::has($this->values, $key);
    }
}
