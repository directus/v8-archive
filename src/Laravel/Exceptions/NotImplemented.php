<?php

declare(strict_types=1);

namespace Directus\Laravel\Exceptions;

use Illuminate\Http\Response;

/**
 * Not implemented exception.
 */
final class NotImplemented extends DirectusException
{
    /**
     * Not implemented exception constructor.
     */
    public function __construct(?string $method = null)
    {
        if ($method === null) {
            $trace = $this->getTrace()[0];
            if (!empty($trace['class'])) {
                $method = $trace['class'].':'.$trace['function'];
            } else {
                $method = $trace['function'];
            }
        }

        parent::__construct('not_implemented', Response::HTTP_NOT_IMPLEMENTED, [
            'message' => 'Method not implemented: '.$method,
        ]);
    }
}
