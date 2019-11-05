<?php

namespace Directus\Api\Routes;

use Directus\Application\Application;
use Directus\Application\Route;

class Server extends Route
{
    /**
     * @param Application $app
     */
    public function __invoke(Application $app)
    {
        \Directus\create_ping_route($app);

        $app->get('/test', [$this, 'projects']);
    }

    /**
     * Return the projects
     * @return Response
     */
    public function projects()
    {
        echo base_path(); die;
    }
}
