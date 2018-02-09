<?php

namespace Directus\Tests\Database\Schemas\Object;

use Directus\Config\StatusMapping;
use Directus\Database\Schema\Object\Collection;
use Directus\Util\ArrayUtils;

class CollectionTest extends \PHPUnit_Framework_TestCase
{
    public function testTable()
    {
        $data = [
            'collection' => 'users',
            'item_name_template' => '{name}',
            'preview_url' => 'http://localhost/users/{id}',
            'hidden' => 0,
            'single' => 0,
            // 'status_mapping' => '[{"name": "active"}]',
            'comment' => 'All my clients',
            'schema' => 'marketing'
        ];

        $collection = new Collection($data);
        $this->assertSame(ArrayUtils::get($data, 'collection'), $collection->getName());
        $this->assertSame(ArrayUtils::get($data, 'item_name_template'), $collection->getItemNameTemplate());
        $this->assertSame(ArrayUtils::get($data, 'preview_url'), $collection->getPreviewUrl());
        $this->assertFalse($collection->isHidden());
        $this->assertFalse($collection->isSingle());
        // $this->assertInstanceOf(StatusMapping::class, $collection->getStatusMapping());
        $this->assertSame(ArrayUtils::get($data, 'comment'), $collection->getComment());
        $this->assertSame(ArrayUtils::get($data, 'schema'), $collection->getSchema());


        $fields = [
            new \Directus\Database\Schema\Object\Field(['field' => 'id']),
            new \Directus\Database\Schema\Object\Field(['field' => 'name']),
            new \Directus\Database\Schema\Object\Field(['field' => 'email'])
        ];

        $collection->setFields($fields);

        $this->assertCount(3, $collection->getFields());
        foreach ($collection->getFields() as $field) {
            $this->assertInstanceOf('\Directus\Database\Schema\Object\Field', $field);
        }

        $this->assertTrue($collection->hasField('email'));
        $this->assertFalse($collection->hasField('password'));
        $this->assertFalse($collection->hasStatusField());
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testInvalidColumnException()
    {
        $collection = new Collection(['collection' => 'users']);

        $collection->setFields([false, null]);
    }
}
