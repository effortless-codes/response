<?php

namespace Winata\Core\Response\Concerns;

/**
 * Trait HasOnResponse
 *
 * Adds formatting capability to enum names, converting them into
 * human-readable messages. Typically used in error responses.
 */
trait HasOnResponse
{
    /**
     * Convert an enum name (e.g., 'ERR_INVALID_OPERATION') into a
     * human-readable string (e.g., 'Invalid Operation').
     *
     * @return string
     */
    public function message(): string
    {
        return ucwords(strtolower(str_replace(['ERR_', '_'], ['', ' '], $this->name)));
    }
}
