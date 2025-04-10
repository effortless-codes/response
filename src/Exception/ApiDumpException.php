<?php

namespace Winata\Core\Response\Exception;

use Exception;
use Illuminate\Http\JsonResponse;

/**
 * Class ApiDumpException
 *
 * A custom exception class intended to return JSON response directly.
 * Useful for debugging or returning structured error data.
 */
class ApiDumpException extends Exception
{
    /**
     * @param mixed $data The data you want to dump in the API response.
     * @param string|null $message Optional custom message
     * @param int $code HTTP status code
     */
    public function __construct(
        public mixed $data,
        ?string $message = null,
        int $code = 500
    ) {
        parent::__construct($message ?? 'API Dump Exception', $code);
    }

    /**
     * Render the exception into an HTTP response.
     *
     * @return JsonResponse
     */
    public function render(): JsonResponse
    {
        return response()->json([
            'message' => $this->getMessage(),
            'data' => $this->data,
        ], $this->getCode() ?: 500);
    }
}
