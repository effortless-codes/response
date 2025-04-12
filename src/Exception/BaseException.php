<?php

namespace Winata\Core\Response\Exception;

use Exception;
use Illuminate\Contracts\Support\Arrayable;
use Throwable;
use Winata\Core\Response\Contracts\OnResponse;
use Winata\Core\Response\Enums\DefaultResponseCode;

/**
 * Class BaseException
 *
 * A standardized base exception class designed to unify API error responses.
 * It supports an enum-based response code, custom messages, optional payload,
 * and enhanced debug information.
 */
class BaseException extends Exception implements Arrayable
{
    /**
     * The response code enum used to determine status and message.
     *
     * @var OnResponse
     */
    public OnResponse $rc;

    /**
     * Additional data to be returned with the response.
     *
     * @var array|null
     */
    public ?array $data;

    /**
     * Create a new BaseException instance.
     *
     * @param OnResponse $rc Enum implementing OnResponse, default to ERR_UNKNOWN.
     * @param string|null $message Optional message to override enum default.
     * @param array|null $data Optional additional payload to return.
     * @param Throwable|null $previous Optional previous exception for chaining.
     */
    public function __construct(
        OnResponse $rc = DefaultResponseCode::ERR_UNKNOWN,
        ?string $message = null,
        ?array $data = null,
        ?Throwable $previous = null
    ) {
        $this->rc = $rc;
        $this->data = $data;

        $code = $rc->httpCode() ?? 0;

        if (is_null($message)) {
            $message = $this->rc->message();
        }

        parent::__construct($message, $code, $previous ?? $this->getPrevious());
    }

    /**
     * Get the response code name from the enum.
     *
     * @return string
     */
    public function getResponseCode(): string
    {
        return $this->rc->name;
    }

    /**
     * Get the response message to be sent to the client.
     * If the app is in debug mode and a previous exception exists,
     * it returns the previous exception's message instead.
     *
     * @return string
     */
    public function getResponseMessage(): string
    {
        if (config('app.debug') && $this->getPrevious() instanceof Throwable) {
            return $this->getPrevious()->getMessage();
        }

        return $this->message;
    }

    /**
     * Get the optional error payload.
     *
     * @return array|null
     */
    public function getErrorData(): ?array
    {
        return $this->data;
    }

    /**
     * Convert the exception into an array for JSON responses.
     *
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        $carrier = [
            'rc' => $this->getResponseCode(),
            'message' => $this->getResponseMessage(),
            'timestamp' => now(),
            'payload' => $this->getErrorData(),
        ];

        if (config('winata.response.enable_debug') && $this->getPrevious() instanceof Throwable) {
            $carrier['trace'] = $this->getPrevious();
            $carrier['debug'] = [
                'origin_message' => $this->getPrevious()->getMessage(),
                'class' => get_class($this->getPrevious()),
                'file' => $this->getPrevious()->getFile(),
                'line' => $this->getPrevious()->getLine(),
                'trace' => $this->getPrevious()->getTrace(),
            ];
        }

        return $carrier;
    }
}
