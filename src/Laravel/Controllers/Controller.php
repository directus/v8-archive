<?php

declare(strict_types=1);

namespace Directus\Laravel\Controllers;

use Illuminate\Routing\Controller as BaseController;

/**
 * Server controller.
 */
abstract class Controller extends BaseController
{
    public function __construct()
    {
        $this->middleware('directus');
    }
}
