<?php

class FieldRelationshipTest extends PHPUnit_Framework_TestCase
{
    public function testColumnRelationship()
    {
        $fieldA = new \Directus\Database\Schema\Object\Field(['field' => 'category_id']);
        $fieldB = new \Directus\Database\Schema\Object\Field(['field' => 'products', 'type' => 'alias']);

        $data = [
            'collection_a' => 'projects',
            'field_a' => 'category_id',
            'junction_key_a' => null,
            'junction_collection' => null,
            'junction_mixed_collections' => null,
            'junction_key_b' => null,
            'collection_b' => 'categories',
            'field_b' => 'products'
        ];

        $relationshipA = new \Directus\Database\Schema\Object\FieldRelationship($fieldA, $data);
        $relationshipB = new \Directus\Database\Schema\Object\FieldRelationship($fieldB, $data);

        $this->assertTrue($relationshipA->isValid());
        $this->assertTrue($relationshipA->isManyToOne());

        $this->assertTrue($relationshipB->isValid());
        $this->assertTrue($relationshipB->isOneToMany());
    }
}
