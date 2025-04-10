<?php

namespace Winata\Core\Response\Http;

use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Responsable;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Http\Response as HttpResponse;
use Illuminate\Pagination\AbstractPaginator;
use Illuminate\Pagination\CursorPaginator;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Symfony\Component\HttpFoundation\Response as SymfonyResponse;
use Winata\Core\Response\Contracts\OnResponse;
use Winata\Core\Response\Enums\DefaultResponseCode;
use DateTimeInterface;

/**
 * Class Response
 *
 * A standardized API response wrapper that formats data with consistent structure.
 * All paginator types are merged inside 'payload' (no 'meta' separation),
 */
class Response implements Responsable
{
    /**
     * Response constructor.
     *
     * @param JsonResource|ResourceCollection|Arrayable|LengthAwarePaginator|CursorPaginator|Collection|string|array|null $data
     * @param string|null $message
     * @param OnResponse $code
     */
    public function __construct(
        protected JsonResource|ResourceCollection|Arrayable|LengthAwarePaginator|CursorPaginator|Collection|string|array|null $data = null,
        protected ?string $message = null,
        protected OnResponse $code = DefaultResponseCode::SUCCESS,
    ) {
    }

    /**
     * Returns the response data converted to array or raw string if applicable.
     *
     * @return array|string|null
     */
    public function getData(): array|string|null
    {
        if ($this->data instanceof Arrayable) {
            return $this->data->toArray();
        }

        if ($this->data instanceof Collection) {
            return $this->data->all();
        }

        return $this->data;
    }

    /**
     * Get the response message.
     *
     * @return string
     */
    public function getMessage(): string
    {
        return $this->message ?? $this->code->message();
    }

    /**
     * Constructs the response payload structure.
     *
     * @return array<string, mixed>
     */
    public function getResponseData(): array
    {
        $timestamp = now();
        if ($timestamp instanceof DateTimeInterface) {
            $timestamp = $timestamp->toIso8601String();
        }

        $base = [
            'rc' => $this->code->name,
            'message' => $this->getMessage(),
            'timestamp' => $timestamp,
        ];

        // Null or string
        if (is_null($this->data)) {
            return [...$base, 'payload' => null];
        }

        if (is_string($this->data)) {
            return [...$base, 'payload' => $this->data];
        }

        // Resource or paginator
        if (
            ($this->data instanceof JsonResource || $this->data instanceof ResourceCollection) &&
            ($this->data->resource ?? null) instanceof AbstractPaginator
        ) {
            /** @var AbstractPaginator $paginator */
            $paginator = $this->data->resource;

            return [...$base, 'payload' => $paginator->toArray()];
        }

        // Paginator directly
        if ($this->data instanceof Paginator) {
            return [...$base, 'payload' => $this->data->toArray()];
        }

        // Arrayable / Collection / array
        return [
            ...$base,
            'payload' => is_array($this->data) ? $this->data : [JsonResource::$wrap => $this->getData()],
        ];
    }

    /**
     * Convert to HTTP response.
     *
     * @param Request $request
     * @return SymfonyResponse
     */
    public function toResponse($request): SymfonyResponse
    {
        $data = $this->getResponseData();
        $status = $this->code->httpCode();

        return $request->expectsJson()
            ? response()->json($data, $status)
            : new HttpResponse(
                json_encode($data, JSON_THROW_ON_ERROR),
                $status,
                ['Content-Type' => 'application/json']
            );
    }
}
