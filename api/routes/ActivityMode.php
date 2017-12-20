<?php

namespace Directus\Api\Routes;

use Directus\Database\TableGateway\RelationalTableGateway;

trait ActivityMode
{
    public function getActivityMode()
    {
        // TODO: Do something with this. think about the need of this and find a better solution
        $activityLoggingEnabled = !(isset($_GET['skip_activity_log']) && (1 == $_GET['skip_activity_log']));
        return $activityLoggingEnabled ? RelationalTableGateway::ACTIVITY_ENTRY_MODE_PARENT : RelationalTableGateway::ACTIVITY_ENTRY_MODE_DISABLED;
    }
}
