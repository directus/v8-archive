<?php

namespace Directus\Database\Schema\Object;

use Directus\Database\SchemaService;

class FieldRelationship extends AbstractObject
{
    const ONE_TO_MANY = 'O2M';
    const MANY_TO_ONE = 'M2O';

    /**
     * The field this relationship belongs to.
     *
     * @var Field
     */
    protected $fromField;

    /**
     * FieldRelationship constructor.
     *
     * @param Field $fromField - Parent field
     */
    public function __construct(Field $fromField, array $attributes)
    {
        $this->fromField = $fromField;

        parent::__construct($attributes);

        $this->attributes->set('type', $this->guessType());
    }

    /**
     * Gets the parent collection.
     *
     * @return string
     */
    public function getCollectionMany()
    {
        return $this->attributes->get('collection_many');
    }

    /**
     * Gets the other collection of M2M relationship.
     *
     * @return string
     */
    public function getCollectionManyToMany()
    {
        $firstCollection = $this->attributes->get('collection_one');
        $junctionCollection = $this->attributes->get('collection_many');

        //get alias fields of junction table
        $junctionTableSchema = SchemaService::getCollection($junctionCollection);
        foreach ($junctionTableSchema->getFields() as $fieldColumnDetails) {
            if ($fieldColumnDetails->hasRelationship() && $fieldColumnDetails->isManyToOne() && $fieldColumnDetails->getRelationship()->getFieldOne() === $firstCollection) {
                return $fieldColumnDetails->getRelationship()->getCollectionOne();
            }
        }
    }

    /**
     * Get junction field relate to other collection.
     *
     * @return string
     */
    public function getJunctionOtherRelatedField()
    {
        $firstCollection = $this->attributes->get('collection_one');
        $junctionCollection = $this->attributes->get('collection_many');

        //get alias fields of junction table
        $junctionTableSchema = SchemaService::getCollection($junctionCollection);
        foreach ($junctionTableSchema->getFields() as $fieldColumnDetails) {
            if ($fieldColumnDetails->hasRelationship() && $fieldColumnDetails->isManyToOne() && $fieldColumnDetails->getRelationship()->getFieldOne() === $firstCollection) {
                return $fieldColumnDetails->getName();
            }
        }
    }

    /**
     * Gets the parent field.
     *
     * @return string
     */
    public function getFieldMany()
    {
        return $this->attributes->get('field_many');
    }

    public function getCollectionOne()
    {
        return $this->attributes->get('collection_one');
    }

    public function getFieldOne()
    {
        return $this->attributes->get('field_one');
    }

    /**
     * Checks whether the relationship has a valid type.
     *
     * @return bool
     */
    public function isValid()
    {
        return null !== $this->getType();
    }

    /**
     * Gets the relationship type.
     *
     * @return null|string
     */
    public function getType()
    {
        return $this->attributes->get('type');
    }

    /**
     * Checks whether the relatiopship is MANY TO ONE.
     *
     * @return bool
     */
    public function isManyToOne()
    {
        return $this->getType() === static::MANY_TO_ONE;
    }

    /**
     * Checks whether the relatiopship is ONE TO MANY.
     *
     * @return bool
     */
    public function isOneToMany()
    {
        return $this->getType() === static::ONE_TO_MANY;
    }

    /**
     * Guess the data type.
     *
     * @return null|string
     */
    protected function guessType()
    {
        $type = null;

        if (!$this->fromField) {
            return $type;
        }

        $fieldName = $this->fromField->getName();
        $fieldCollectionName = $this->fromField->getCollectionName();
        $isAlias = $this->fromField->isAlias();

        if (
            !$isAlias &&
            null !== $this->getCollectionOne() &&
            $this->getFieldMany() === $fieldName &&
            $this->getCollectionMany() === $fieldCollectionName
        ) {
            $type = static::MANY_TO_ONE;
        } elseif (
            $isAlias &&
            null !== $this->getCollectionMany() &&
            $this->getFieldOne() === $fieldName &&
            $this->getCollectionOne() === $fieldCollectionName
        ) {
            $type = static::ONE_TO_MANY;
        }

        return $type;
    }
}
