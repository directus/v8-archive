<?php

namespace Directus\Tests\Config;

use Directus\Config\Config;
use Directus\Config\StatusConfig;
use Directus\Config\StatusItem;

class ConfigTest extends  \PHPUnit_Framework_TestCase
{
    public function testItem()
    {
        $config = new Config([
            'option' => 1
        ]);

        $this->assertSame(1, $config->get('option'));
        $this->assertInstanceOf(StatusConfig::class, $config->getStatus());
        $this->assertEmpty($config->getAllStatusesValue());
        $this->assertEmpty($config->getDeletedStatusesValue());
        $this->assertEmpty($config->getPublishedStatusesValue());
        $this->assertEmpty($config->getStatusMapping());
    }

    public function testStatusConfig()
    {
        $config = new Config([
            'status' => [
                'deleted_value' => 0,
                'publish_value' => 1,
                'draft_value' => 2,
                'name' => 'status',
                'mapping' => [
                    0 => [
                        'name' => 'Deleted',
                        'text_color' => '#FFFFFF',
                        'background_color' => '#F44336',
                        'subdued_in_listing' => true,
                        'show_listing_badge' => true,
                        'hidden_globally' => true,
                        'hard_delete' => false,
                        'published' => false,
                        'sort' => 3
                    ],
                    1 => [
                        'name' => 'Published',
                        'text_color' => '#FFFFFF',
                        'background_color' => '#3498DB',
                        'subdued_in_listing' => false,
                        'show_listing_badge' => false,
                        'hidden_globally' => false,
                        'hard_delete' => false,
                        'published' => true,
                        'sort' => 1
                    ],
                    2 => [
                        'name' => 'Draft',
                        'text_color' => '#999999',
                        'background_color' => '#EEEEEE',
                        'subdued_in_listing' => true,
                        'show_listing_badge' => true,
                        'hidden_globally' => false,
                        'hard_delete' => false,
                        'published' => false,
                        'sort' => 2
                    ]
                ]
            ],
        ]);

        $this->assertInternalType('array', $config->getStatusMapping());

        // Published
        $publishedStatus = $config->getPublishedStatusesValue();
        $this->assertCount(1, $publishedStatus);
        $this->assertTrue(in_array(1, $publishedStatus));

        // Deleted
        // 0 is not a hard delete status, so it won't show up at deleted status
        $deletedStatus = $config->getDeletedStatusesValue();
        $this->assertCount(0, $deletedStatus);
        $this->assertFalse(in_array(0, $deletedStatus));

        // All status
        $allStatus = $config->getAllStatusesValue();
        $this->assertCount(3, $allStatus);
        $this->assertTrue(in_array(0, $allStatus));
        $this->assertTrue(in_array(1, $allStatus));
        $this->assertTrue(in_array(2, $allStatus));

        $allStatuses = $config->getStatusMapping();
        /** @var StatusItem $status */
        foreach ($allStatuses as $status) {
            $this->assertInstanceOf(StatusItem::class, $status);
            $this->assertInternalType('integer', $status->getSort());
            $this->assertInternalType('integer', $status->getValue());
            $this->assertInternalType('array', $status->getAttributes());
            $this->assertInternalType('string', $status->getName());
        }
    }

    /**
     * @expectedException \Directus\Config\Exception\InvalidValueException
     */
    public function testInvalidValue()
    {
        $config = new Config([
            'status' => [
                'deleted_value' => 0,
                'publish_value' => 1,
                'draft_value' => 2,
                'name' => 'status',
                'mapping' => [
                    'deleted' => [
                        'name' => 'Deleted',
                        'text_color' => '#FFFFFF',
                        'background_color' => '#F44336',
                        'subdued_in_listing' => true,
                        'show_listing_badge' => true,
                        'hidden_globally' => true,
                        'hard_delete' => false,
                        'published' => false,
                        'sort' => 3
                    ],
                    1 => [
                        'name' => 'Published',
                        'text_color' => '#FFFFFF',
                        'background_color' => '#3498DB',
                        'subdued_in_listing' => false,
                        'show_listing_badge' => false,
                        'hidden_globally' => false,
                        'hard_delete' => false,
                        'published' => true,
                        'sort' => 1
                    ],
                    2 => [
                        'name' => 'Draft',
                        'text_color' => '#999999',
                        'background_color' => '#EEEEEE',
                        'subdued_in_listing' => true,
                        'show_listing_badge' => true,
                        'hidden_globally' => false,
                        'hard_delete' => false,
                        'published' => false,
                        'sort' => 2
                    ]
                ]
            ],
        ]);
    }

    /**
     * @expectedException \Directus\Config\Exception\InvalidStatusException
     */
    public function testInvalidAttributes()
    {
        $config = new Config([
            'status' => [
                'deleted_value' => 0,
                'publish_value' => 1,
                'draft_value' => 2,
                'name' => 'status',
                'mapping' => [
                    0 => [
                        'status_name' => 'Deleted', // invalid
                        'text_color' => '#FFFFFF',
                        'background_color' => '#F44336',
                        'subdued_in_listing' => true,
                        'show_listing_badge' => true,
                        'hidden_globally' => true,
                        'hard_delete' => false,
                        'published' => false,
                        'sort' => 3
                    ]
                ]
            ],
        ]);
    }

    /**
     * @expectedException \Directus\Config\Exception\InvalidStatusException
     */
    public function testInvalidStatus()
    {
        $config = new Config([
            'status' => [
                'deleted_value' => 0,
                'publish_value' => 1,
                'draft_value' => 2,
                'name' => 'status',
                'mapping' => [
                    0,
                    1,
                    2
                ]
            ],
        ]);
    }

    /**
     * @expectedException \Directus\Config\Exception\InvalidStatusException
     */
    public function testMissingStatusAttributes()
    {
        $config = new Config([
            'status' => [
                'name' => 'status'
            ],
        ]);
    }
}
