<?php

namespace Winata\Core\Response\Controllers\Api;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Pagination\CursorPaginator;
use Illuminate\Pagination\LengthAwarePaginator;
use Winata\Core\Response\Contracts\OnResponse;
use Winata\Core\Response\Enums\DefaultResponseCode;
use Winata\Core\Response\Http\Response;

/**
 * Base API Controller
 *
 * Provides a unified response structure for API endpoints with
 * support for Laravel resources, pagination, and custom messages.
 */
class Controller
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    /**
     * Holds contextual response messages.
     *
     * @var array<string, string>
     */
    protected array $responseMessages = [];

    /**
     * Get a response message from the context key.
     *
     * @param string $context
     * @return string
     */
    public function getResponseMessage(string $context): string
    {
        return $this->responseMessages[$context] ?? $context;
    }

    /**
     * Create a standardized API response.
     *
     * @param JsonResource|ResourceCollection|Arrayable<int|string, mixed>|LengthAwarePaginator<Model>|CursorPaginator<Model>|array<int|string, mixed>|null $data
     * @param string|null $message Optional response message
     * @param OnResponse $rc Response code enum instance
     * @return Response
     */
    public function response(
        JsonResource|ResourceCollection|Arrayable|LengthAwarePaginator|CursorPaginator|array|null $data = null,
        ?string $message = null,
        OnResponse $rc = DefaultResponseCode::SUCCESS,
    ): Response {
        return new Response($data, $message, $rc);
    }
}
