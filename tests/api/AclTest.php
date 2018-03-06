<?php

use Directus\Permissions\Acl;

class AclTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var \Directus\Permissions\Acl null
     */
    protected $acl = null;
    protected $permissions = null;

    public function setUp()
    {
        $this->acl = new Acl();
        $this->permissions = [
            'directus_files' => [
                [
                    'id' => 1,
                    'collection' => 'directus_files',
                    'group' => 2,
                    'status' => null,
                    'read_field_blacklist' => ['date_uploaded'],
                    'write_field_blacklist' => ['type'],
                    'navigate' => 1,
                    'read' => 3,
                    'create' => 1,
                    'update' => 3,
                    'delete' => 3,
                    'require_activity_message' => 0
                ]
            ],
            'test_table' => [
                [
                    'id' => 2,
                    'collection' => 'test_table',
                    'group' => 2,
                    'read_field_blacklist' => null,
                    'write_field_blacklist' => null,
                    'navigate' => 1,
                    'status' => null,
                    'create' => 1,
                    'read' => 1,
                    'update' => 1,
                    'delete' => 0,
                    'require_activity_message' => 1
                ],
                [
                    'id' => 20,
                    'collection' => 'test_table',
                    'group' => 2,
                    'read_field_blacklist' => ['read'],
                    'write_field_blacklist' => ['write'],
                    'navigate' => 1,
                    'status' => 1,
                    'create' => 0,
                    'read' => 2,
                    'update' => 0,
                    'delete' => 0
                ],
                [
                    'id' => 21,
                    'collection' => 'test_table',
                    'group' => 2,
                    'read_field_blacklist' => ['read_draft'],
                    'write_field_blacklist' => ['write_draft'],
                    'navigate' => 1,
                    'status' => 2,
                    'create' => 1,
                    'read' => 2,
                    'update' => 0,
                    'delete' => 0
                ]
            ],
            'forbid' => [
                [
                    'id' => 3,
                    'collection' => 'test_table',
                    'group' => 2,
                    'read_field_blacklist' => null,
                    'write_field_blacklist' => null,
                    'navigate' => 1,
                    'status' => 0,
                    'create' => 0,
                    'read' => 0,
                    'update' => 0,
                    'delete' => 0
                ]
            ],
            'directus_collection_presets' => [
                [
                    'create' => 1,
                    'read' => 1,
                    'update' => 1
                ]
            ],
            'odd_collection' => [[]]
        ];

        $this->acl->setPermissions($this->permissions);
        $this->acl->setUserId(2);
        $this->acl->setGroupId(2);
    }

    public function testPublic()
    {
        $acl = new Acl();

        $acl->setPublic(true);
        $this->assertTrue($acl->isPublic());

        $acl->setPublic(false);
        $this->assertFalse($acl->isPublic());

        $acl->setPublic(1);
        $this->assertTrue($acl->isPublic());

        $acl->setPublic(0);
        $this->assertFalse($acl->isPublic());
    }

    public function testIsAdmin()
    {
        $acl = new Acl();

        $acl->setGroupId(1);
        $this->assertTrue($acl->isAdmin());

        $acl->setGroupId(2);
        $this->assertFalse($acl->isAdmin());
    }

    public function testUser()
    {
        $this->assertSame(2, $this->acl->getUserId());
        $this->assertSame(2, $this->acl->getGroupId());

        $acl = new Acl();

        $this->assertNull($acl->getUserId());
        $this->assertNull($acl->getGroupId());

        $acl->setUserId(2);
        $acl->setGroupId(1);
        $this->assertSame(2, $acl->getUserId());
        $this->assertSame(1, $acl->getGroupId());
    }

    public function testSetCollectionPermissions()
    {
        $acl = new Acl();
        $acl->setCollectionPermission('test', [
            $acl::ACTION_UPDATE => 1
        ]);

        $this->assertTrue($acl->canUpdateMine('test'));
        $this->assertFalse($acl->canUpdateFromGroup('test'));
        $this->assertFalse($acl->canUpdateAll('test'));
    }

    public function testSettingPermissions()
    {
        $acl = new Acl($this->permissions);
        $permissions = $acl->getPermissions();

        $this->assertTrue(count($acl->getPermissions()) > 0);
        $this->assertEquals(count($permissions), count($this->permissions));
        $this->assertNotEmpty($acl->getCollectionPermissions('directus_files'));
        $this->assertEmpty($acl->getCollectionPermissions('directus_users'));

        $collectionPermission = $acl->getCollectionPermissions('odd_collection');
        $this->assertCount(1, $collectionPermission);
        $this->assertArrayHasKey('*', $collectionPermission);
        $this->assertEmpty($collectionPermission['*']);
        unset($collectionPermission['*']);
        $this->assertEmpty($collectionPermission);
    }

    /**
     * @expectedException \Directus\Permissions\Exception\ForbiddenFieldReadException
     */
    public function testEnforceReadBlacklist()
    {
        $this->acl->enforceReadField('directus_files', ['name', 'type', 'date_uploaded']);
    }

    /**
     * @expectedException \Directus\Permissions\Exception\ForbiddenFieldWriteException
     */
    public function testEnforceWriteBlacklist()
    {
        $this->acl->enforceWriteField('directus_files', ['name', 'type', 'date_uploaded']);
    }

    /**
     * @expectedException \Directus\Permissions\Exception\ForbiddenFieldReadException
     */
    public function testEnforceReadBlacklistString()
    {
        $this->acl->enforceReadField('directus_files', 'date_uploaded');
    }

    /**
     * @expectedException \Directus\Permissions\Exception\ForbiddenFieldWriteException
     */
    public function testEnforceWriteBlacklistString()
    {
        $this->acl->enforceWriteField('directus_files', 'type');
    }

    public function testEnforceBlacklist()
    {
        // Nothing happens here
        $this->acl->enforceReadField('directus_files', ['name', 'title', 'size']);
        $this->acl->enforceWriteField('directus_files', ['name', 'title', 'size']);
    }

    public function testUnknownBlacklistType()
    {
        $fields = $this->acl->getFieldBlacklist('unknown', 'directus_files');
        $this->assertInternalType('array', $fields);
        $this->assertEmpty($fields);
    }

    public function testGetPrivilegeListPermissions()
    {
        $permissions = $this->acl->getCollectionPermissions('directus_files');
        $this->assertInternalType('array', $permissions);
        $this->assertNotEmpty($permissions);

        // table that does not exists in the privilege list
        $permissions = $this->acl->getCollectionPermissions('directus_users');
        $this->assertInternalType('array', $permissions);
        $this->assertEmpty($permissions);
    }

    public function testHasPermission()
    {
        $this->assertTrue($this->acl->canUpdateAll('directus_files'));

        // Test table: all
        $this->assertTrue($this->acl->canCreate('test_table'));

        $this->assertTrue($this->acl->canReadMine('test_table'));
        $this->assertFalse($this->acl->canReadFromGroup('test_table'));
        $this->assertFalse($this->acl->canReadAll('test_table'));

        $this->assertTrue($this->acl->canUpdateMine('test_table'));
        $this->assertFalse($this->acl->canUpdateFromGroup('test_table'));
        $this->assertFalse($this->acl->canUpdateAll('test_table'));

        $this->assertFalse($this->acl->canDeleteMine('test_table'));
        $this->assertFalse($this->acl->canDeleteFromGroup('test_table'));
        $this->assertFalse($this->acl->canDeleteAll('test_table'));

        // Test table: status 1
        $this->assertFalse($this->acl->canCreate('test_table', 1));

        $this->assertTrue($this->acl->canReadMine('test_table', 1));
        $this->assertTrue($this->acl->canReadFromGroup('test_table', 1));
        $this->assertFalse($this->acl->canReadAll('test_table', 1));

        $this->assertFalse($this->acl->canUpdateMine('test_table', 1));
        $this->assertFalse($this->acl->canUpdateFromGroup('test_table', 1));
        $this->assertFalse($this->acl->canUpdateAll('test_table', 1));

        $this->assertFalse($this->acl->canDeleteMine('test_table', 1));
        $this->assertFalse($this->acl->canDeleteFromGroup('test_table', 1));
        $this->assertFalse($this->acl->canDeleteAll('test_table', 1));
    }

    public function testCanDo()
    {
        $this->assertFalse($this->acl->canCreate('forbid'));
        $this->assertTrue($this->acl->canCreate('directus_files'));

        $this->assertFalse($this->acl->canRead('forbid'));
        $this->assertTrue($this->acl->canRead('directus_files'));

        $this->assertFalse($this->acl->canUpdate('forbid'));
        $this->assertTrue($this->acl->canUpdate('directus_files'));

        $this->assertFalse($this->acl->canDelete('forbid'));
        $this->assertTrue($this->acl->canDelete('directus_files'));

        $this->assertFalse($this->acl->canAlter('forbid'));
        $this->assertFalse($this->acl->canAlter('directus_files'));

        $acl = new Acl();
        $acl->setGroupId(1);
        $this->assertTrue($acl->canAlter('directus_files'));
    }

    public function testRequireMessageActivity()
    {
        $this->assertTrue($this->acl->requireActivityMessage('test_table'));
        $this->assertFalse($this->acl->requireActivityMessage('forbidden'));
    }

    /**
     * @expectedException \Directus\Permissions\Exception\ForbiddenCollectionAlterException
     */
    public function testEnforceCanAlterFails()
    {
        $this->acl->enforceAlter('directus_files');
    }

    public function testEnforceCanAlterPassed()
    {
        $acl = new Acl();
        $acl->setGroupId(1);
        $acl->enforceAlter('directus_files');
    }

    /**
     * @expectedException \Directus\Permissions\Exception\ForbiddenCollectionCreateException
     */
    public function testEnforceCanCreateFails()
    {
        $this->acl->enforceCreate('forbid');
    }

    public function testEnforceCanCreatePassed()
    {
        $this->acl->enforceCreate('directus_files');
    }

    public function testEnforceCanReadPasses()
    {
        $this->acl->enforceReadMine('directus_files');
        $this->acl->enforceReadFromGroup('directus_files');
        $this->acl->enforceReadAll('directus_files');
        $this->acl->enforceRead('directus_files');
    }

    public function testEnforceCanUpdatePasses()
    {
        $this->acl->enforceUpdateMine('directus_files');
        $this->acl->enforceUpdateFromGroup('directus_files');
        $this->acl->enforceUpdateAll('directus_files');
        $this->acl->enforceUpdate('directus_files');
    }

    public function testEnforceCanDeletePasses()
    {
        $this->acl->enforceDeleteMine('directus_files');
        $this->acl->enforceDeleteFromGroup('directus_files');
        $this->acl->enforceDeleteAll('directus_files');
        $this->acl->enforceDelete('directus_files');
    }

    /**
     * @expectedException \Directus\Permissions\Exception\ForbiddenCollectionReadException
     */
    public function testEnforceCanReadMineFails()
    {
        $this->acl->enforceReadMine('forbid');
    }

    /**
     * @expectedException \Directus\Permissions\Exception\ForbiddenCollectionReadException
     */
    public function testEnforceCanReadFromGroupFails()
    {
        $this->acl->enforceReadFromGroup('forbid');
    }

    /**
     * @expectedException \Directus\Permissions\Exception\ForbiddenCollectionReadException
     */
    public function testEnforceCanReadAllFails()
    {
        $this->acl->enforceReadAll('forbid');
    }

    /**
     * @expectedException \Directus\Permissions\Exception\ForbiddenCollectionReadException
     */
    public function testEnforceCanReadFails()
    {
        $this->acl->enforceRead('forbid');
    }

    /**
     * @expectedException \Directus\Permissions\Exception\ForbiddenCollectionUpdateException
     */
    public function testEnforceCanUpdateFails()
    {
        $this->acl->enforceUpdate('forbid');
    }

    /**
     * @expectedException \Directus\Permissions\Exception\ForbiddenCollectionUpdateException
     */
    public function testEnforceCanUpdateMineFails()
    {
        $this->acl->enforceUpdateMine('forbid');
    }

    /**
     * @expectedException \Directus\Permissions\Exception\ForbiddenCollectionUpdateException
     */
    public function testEnforceCanUpdateFromGroupFails()
    {
        $this->acl->enforceUpdateFromGroup('forbid');
    }

    /**
     * @expectedException \Directus\Permissions\Exception\ForbiddenCollectionUpdateException
     */
    public function testEnforceCanUpdateAllFails()
    {
        $this->acl->enforceUpdateAll('forbid');
    }

    /**
     * @expectedException \Directus\Permissions\Exception\ForbiddenCollectionDeleteException
     */
    public function testEnforceCanDeleteFails()
    {
        $this->acl->enforceDelete('forbid');
    }

    /**
     * @expectedException \Directus\Permissions\Exception\ForbiddenCollectionDeleteException
     */
    public function testEnforceCanDeleteMineFails()
    {
        $this->acl->enforceDeleteMine('forbid');
    }

    /**
     * @expectedException \Directus\Permissions\Exception\ForbiddenCollectionDeleteException
     */
    public function testEnforceCanDeleteFromGroupFails()
    {
        $this->acl->enforceDeleteFromGroup('forbid');
    }

    /**
     * @expectedException \Directus\Permissions\Exception\ForbiddenCollectionDeleteException
     */
    public function testEnforceCanDeleteAllFails()
    {
        $this->acl->enforceDeleteAll('forbid');
    }
    //
    // public function testOwnerColumn()
    // {
    //     $this->assertSame('user', $this->acl->getCmsOwnerColumnByTable('directus_files'));
    //     $this->assertSame('id', $this->acl->getCmsOwnerColumnByTable('directus_users'));
    //     $this->assertSame(false, $this->acl->getCmsOwnerColumnByTable('test_table'));
    //     $this->assertSame(false, $this->acl->getCmsOwnerColumnByTable('no_a_table'));
    // }
    //
    // protected function getMockAdapter()
    // {
    //     // mock the adapter, driver, and parts
    //     $mockResult = create_mock($this, 'Zend\Db\Adapter\Driver\ResultInterface');
    //     $mockStatement = create_mock($this,'Zend\Db\Adapter\Driver\StatementInterface');
    //     $mockStatement->expects($this->any())->method('execute')->will($this->returnValue($mockResult));
    //     $mockConnection = create_mock($this,'Zend\Db\Adapter\Driver\ConnectionInterface');
    //     $mockDriver = create_mock($this,'Zend\Db\Adapter\Driver\DriverInterface');
    //     $mockDriver->expects($this->any())->method('createStatement')->will($this->returnValue($mockStatement));
    //     $mockDriver->expects($this->any())->method('getConnection')->will($this->returnValue($mockConnection));
    //
    //     // setup mock adapter
    //     return create_mock($this,'Zend\Db\Adapter\Adapter', null, [$mockDriver]);
    // }
}
