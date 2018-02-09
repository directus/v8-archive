<?php

class FieldTest extends PHPUnit_Framework_TestCase
{
    protected $fieldData;
    protected $fieldData2;

    public function setUp()
    {
        $this->fieldData = [
            'collection' => 'articles',
            'field' => 'related_projects',
            'type' => 'ALIAS',
            'interface' => 'many_to_many',
            'options' => '{"key": "value"}',
            'required' => 0,
            'sort' => 999,
            'comment' => 'Projects related to this project',
            'hidden_input' => 0,
            'hidden_list' => 0,

            // from mysql
            'default_value' => 0,
            'nullable' => 1,
            'char_length' => null
        ];

        $this->fieldData2 = [
            'collection' => 'articles',
            'field' => 'price',
            'type' => 'DECIMAL',
            'interface' => 'numeric',
            'options' => '{"key": "value"}',
            'required' => 1,
            'sort' => 999,
            'comment' => 'Article price',
            'hidden_input' => 0,
            'hidden_list' => 0,

            // from mysql
            'default_value' => 0.00,
            'nullable' => 0,
            'precision' => 10,
            'scale' => 2,
            'char_length' => null
        ];
    }

    public function testField()
    {
        $fieldData = $this->fieldData;

        $field = new \Directus\Database\Schema\Object\Field($fieldData);
        $field2 = new \Directus\Database\Schema\Object\Field($this->fieldData2);
        $field3 = new \Directus\Database\Schema\Object\Field(array_merge(
            $this->fieldData2,
            ['precision' => '10', 'scale' => '2']
        ));
        $field4 = new \Directus\Database\Schema\Object\Field(array_merge(
            $this->fieldData2,
            [
                'sort' => 'abc',
                'precision' => 'a', 'scale' => 'b',
                'char_length' => 255
            ]
        ));

        // default value
        $this->assertSame(0, $field->getDefaultValue());
        $this->assertSame(0.00, $field2->getDefaultValue());

        // is nullable?
        $this->assertTrue($field->isNullable());
        $this->assertFalse($field2->isNullable());

        // numeric attributes
        $this->assertSame(10, $field2->getPrecision());
        $this->assertSame(2, $field2->getScale());

        // using string numbers
        $this->assertSame(10, $field3->getPrecision());
        $this->assertSame(2, $field3->getScale());

        // using characters
        $this->assertSame(0, $field4->getPrecision());
        $this->assertSame(0, $field4->getScale());

        // char length
        $this->assertSame(0, $field->getCharLength());
        $this->assertSame(0, $field->getLength());
        $this->assertSame(255, $field4->getLength());
        $this->assertSame(255, $field4->getCharLength());

        // sorting
        $this->assertSame(999, $field->getSort());
        $this->assertSame(0, $field4->getSort());
    }

    public function testArrayAccess()
    {
        $field = new \Directus\Database\Schema\Object\Field($this->fieldData);

        $this->assertTrue(isset($field['field']));
        $this->assertSame($field['field'], 'related_projects');
    }

    /**
     * @expectedException \Exception
     */
    public function testArrayAccessSet()
    {
        $column = new \Directus\Database\Schema\Object\Field($this->fieldData);
        $column['name'] = 'projects';
    }

    /**
     * @expectedException \Exception
     */
    public function testArrayAccessUnset()
    {
        $field = new \Directus\Database\Schema\Object\Field($this->fieldData);
        unset($field['name']);
    }
}
