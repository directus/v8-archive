<?php

namespace Directus\Database\TableGateway;

use Directus\Permissions\Acl;
use Directus\Permissions\Exception\UnauthorizedTableBigEditException;
use Directus\Util\ArrayUtils;
use Zend\Db\Adapter\Adapter;
use Zend\Db\Adapter\AdapterInterface;
use Zend\Db\Sql\Select;
use Zend\Db\Sql\Sql;

class DirectusSettingsTableGateway extends RelationalTableGateway
{
    public static $_tableName = 'directus_settings';

    public $primaryKeyFieldName = 'id';

    private $_defaults = [];

    public function __construct(AdapterInterface $adapter, Acl $acl = null)
    {
        parent::__construct(self::$_tableName, $adapter, $acl);

        $this->_defaults['global'] = [
            'cms_user_auto_sign_out' => 60,
            'project_name' => 'Directus',
            'project_url' => 'http://localhost/',
            'rows_per_page' => 200,
            'cms_thumbnail_url' => ''
        ];

        $this->_defaults['files'] = [
            'allowed_thumbnails' => '',
            'thumbnail_quality' => 100,
            'thumbnail_size' => 200,
            'file_naming' => 'file_id',
            'thumbnail_crop_enabled' => 1,
            'youtube_api_key' => ''
        ];
    }
}
