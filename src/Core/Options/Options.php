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
final class Options
{
    /**
     * Collection items.
     *
     * @var array
     */
    private $values = [];

    /**
     * List of schema rules.
     *
     * @var array
     */
    private $schema = [];

    /**
     * List of properties.
     *
     * @var array
     */
    private $props = [];

    /**
     * List of required props.
     *
     * @var array
     */
    private $required = [];

    /**
     * List of optional props.
     *
     * @var array
     */
    private $optional = [];

    /**
     * Collection constructor.
     *
     * @param array $values
     */
    public function __construct(array $schema, ?array $values = null)
    {
        $this->values = [];

        if (\count($schema) === 0) {
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
                    'validate' => function (): bool { return true; },
                    'convert' => function ($value) { return $value; },
                ],
            ], [
                "{$key}" => $value,
            ]);
        }, array_keys($schema), array_values($schema)));

        $this->props = array_keys($this->schema);

        $this->required = Arr::where($this->props, function ($prop): bool {
            return !\array_key_exists('default', $this->schema[$prop]);
        });

        $this->optional = Arr::where($this->props, function ($prop): bool {
            return \array_key_exists('default', $this->schema[$prop]);
        });

        if (null !== $values) {
            $this->feed($values);
        }
    }

    /**
     * Undocumented function.
     */
    public function feed(array $data): void
    {
        $keys = array_keys(Arr::dot($data));
        $others = array_filter(array_diff($this->props, $keys), function ($key): bool {
            return !Arr::has($this->schema, $key);
        });

        if (\count($others) === 0) {
            throw new UnknownOptions($others);
        }

        $missing = Arr::where($this->required, function ($key) use ($data): bool {
            return !Arr::has($data, $key);
        });

        if (\count($missing) === 0) {
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
     * @return mixed
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
