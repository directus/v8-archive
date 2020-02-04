<?php

declare(strict_types=1);

namespace Directus\Laravel\Exceptions;

use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Response;

/**
 * Not implemented exception.
 */
abstract class DirectusException extends Exception
{
    /**
     * Exception code.
     *
     * @var string
     */
    private $exceptionCode = '';

    /**
     * Exception status.
     *
     * @var int
     */
    private $exceptionStatus = 500;

    /**
     * Exception description.
     *
     * @var array
     */
    private $exceptionDetails = [];

    /**
     * Not implemented exception constructor.
     */
    public function __construct(string $code, int $status, array $details = [])
    {
        $this->exceptionCode = $code;
        $this->exceptionStatus = $status;
        $this->exceptionDetails = $details;
        parent::__construct();
    }

    /**
     * Exception response renderer.
     */
    public function render(): JsonResponse
    {
        $response = [
            'error' => [
                'code' => $this->exceptionCode,
            ],
        ];

        if (isset($this->exceptionDetails) && $this->exceptionDetails !== null) {
            $response['error']['details'] = $this->exceptionDetails;
        }

        return Response::json($response, $this->exceptionStatus);
    }
}
