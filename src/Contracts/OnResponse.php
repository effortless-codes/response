<?php

namespace Winata\Core\Response\Contracts;

/**
 * Interface OnResponse
 *
 * Defines a contract for objects that can return a
 * human-readable message string (commonly used in API responses).
 */
interface OnResponse
{
    /**
     * Get the human-readable message for the current object.
     *
     * @return string
     */
    public function message(): string;
}
