<?php

namespace Directus\Database\Schema\Object;

use Directus\Util\ArrayUtils;

class FieldRelationship extends AbstractObject
{
    const ONE_TO_MANY = 'O2M';
    const MANY_TO_MANY = 'M2M';
    const MANY_TO_ONE = 'M2O';

    /**
     * The field this relationship belongs to
     *
     * @var Field
     */
    protected $fromField;

    /**
     * FieldRelationship constructor.
     *
     * @param Field $fromField - Parent field
     * @param array $attributes
     */
    public function __construct(Field $fromField, array $attributes)
    {
        $this->fromField = $fromField;

        parent::__construct($attributes);

        if ($this->fromField->getName() === $this->attributes->get('field_b')) {
            $this->attributes->replace(
                $this->swapRelationshipAttributes($this->attributes->toArray())
            );
        }

        $this->attributes->set('type', $this->guessType());
    }

    /**
     * Gets the parent collection
     *
     * @return string
     */
    public function getCollectionA()
    {
        return $this->attributes->get('collection_a');
    }

    /**
     * Gets the parent field
     *
     * @return string
     */
    public function getFieldA()
    {
        return $this->attributes->get('field_a');
    }

    /**
     *
     *
     * @return null|string
     */
    public function getJunctionKeyA()
    {
        return $this->attributes->get('junction_key_a');
    }

    public function getJunctionCollection()
    {
        return $this->attributes->get('junction_collection');
    }

    public function getJunctionMixedCollections()
    {
        return $this->attributes->get('junction_mixed_collections');
    }

    public function getJunctionKeyB()
    {
        return $this->attributes->get('junction_key_b');
    }

    public function getCollectionB()
    {
        return $this->attributes->get('collection_b');
    }

    public function getFieldB()
    {
        return $this->attributes->get('field_b');
    }

    /**
     * Checks whether the relationship has a valid type
     *
     * @return bool
     */
    public function isValid()
    {
        return $this->getType() !== null;
    }

    /**
     * Gets the relationship type
     *
     * @return string|null
     */
    public function getType()
    {
        return $this->attributes->get('type');
    }

    /**
     * Checks whether the relatiopship is MANY TO ONE
     *
     * @return bool
     */
    public function isManyToOne()
    {
        return $this->getType() === static::MANY_TO_ONE;
    }

    /**
     * Checks whether the relatiopship is MANY TO MANY
     *
     * @return bool
     */
    public function isManyToMany()
    {
        return $this->getType() === static::MANY_TO_MANY;
    }

    /**
     * Checks whether the relatiopship is ONE TO MANY
     *
     * @return bool
     */
    public function isOneToMany()
    {
        return $this->getType() === static::ONE_TO_MANY;
    }

    /**
     * Guess the data type
     *
     * @return null|string
     */
    protected function guessType()
    {
        $fieldName = $this->fromField->getName();
        $isAlias = $this->fromField->isAlias();
        $type = null;

        if (!$this->fromField) {
            $type = null;
        } else if (
            !$isAlias                                        &&
            $this->getCollectionB()                 !== null &&
            $this->getFieldA()                      === $fieldName &&
            $this->getJunctionKeyA()                === null &&
            $this->getJunctionCollection()          === null &&
            $this->getJunctionKeyB()                === null &&
            $this->getJunctionMixedCollections()    === null &&
            $this->getCollectionB()                 !== null
            // Can have or not this value depends if the backward (O2M) relationship is set
            // $this->getFieldB()                      === null
        ) {
            $type = static::MANY_TO_ONE;
        } else if (
            $isAlias                                         &&
            $this->getCollectionB()                 !== null &&
            $this->getFieldA()                      === $fieldName &&
            $this->getJunctionKeyA()                === null &&
            $this->getJunctionCollection()          === null &&
            $this->getJunctionKeyB()                === null &&
            $this->getJunctionMixedCollections()    === null &&
            $this->getCollectionB()                 !== null &&
            $this->getFieldB()                      !== null
        ) {
            $type = static::ONE_TO_MANY;
        } else if (
            $isAlias                                         &&
            $this->getCollectionB()                 !== null &&
            $this->getFieldA()                      === $fieldName &&
            $this->getJunctionKeyA()                !== null &&
            $this->getJunctionCollection()          !== null &&
            $this->getJunctionKeyB()                !== null &&
            $this->getJunctionMixedCollections()    === null &&
            $this->getCollectionB()                 !== null
            // $this->getFieldB()                      !== null
        ) {
            $type = static::MANY_TO_MANY;
        }

        return $type;
    }

    /**
     * Change the direction of the relationship
     *
     * @param array $attributes
     *
     * @return array
     */
    protected function swapRelationshipAttributes(array $attributes)
    {
        $newAttributes = [
            'collection_a' => ArrayUtils::get($attributes, 'collection_b'),
            'field_a' => ArrayUtils::get($attributes, 'field_b'),
            'junction_key_a' => ArrayUtils::get($attributes, 'junction_key_b'),
            'junction_collection' => ArrayUtils::get($attributes, 'junction_collection'),
            'junction_mixed_collections' => ArrayUtils::get($attributes, 'junction_mixed_collections'),
            'junction_key_b' => ArrayUtils::get($attributes, 'junction_key_a'),
            'collection_b' => ArrayUtils::get($attributes, 'collection_a'),
            'field_b' => ArrayUtils::get($attributes, 'field_a'),
        ];

        return $newAttributes;
    }
}
