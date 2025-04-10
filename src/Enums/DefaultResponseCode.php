<?php

namespace Winata\Core\Response\Enums;

use Symfony\Component\HttpFoundation\Response;
use Winata\Core\Response\Concerns\HasOnResponse;
use Winata\Core\Response\Contracts\OnResponse;

/**
 * Enum DefaultResponseCode
 *
 * Defines a set of standardized response codes for API responses.
 * Each enum case can provide its own HTTP status code.
 */
enum DefaultResponseCode implements OnResponse
{
    use HasOnResponse;

    // Success
    case SUCCESS;

    // Validation & Auth
    case ERR_VALIDATION;
    case ERR_AUTHENTICATION;
    case ERR_ACTION_UNAUTHORIZED;
    case ERR_INVALID_IP_ADDRESS;

    // Signature / Security
    case ERR_MISSING_SIGNATURE_HEADER;
    case ERR_INVALID_SIGNATURE_HEADER;

    // Logic & Operation
    case ERR_INVALID_OPERATION;
    case ERR_ENTITY_NOT_FOUND;
    case ERR_ROUTE_NOT_FOUND;
    case ERR_RECORD_CONSTRAINT;
    case ERR_UNIQUE_RECORD;
    case ERR_QUERY_EXCEPTION;
    case ERR_UNKNOWN;

    // Optional / Extendable
    case ERR_FORBIDDEN;
    case ERR_CONFLICT;

    /**
     * Get the corresponding HTTP status code for each response case.
     *
     * @return int
     */
    public function httpCode(): int
    {
        return match ($this) {
            self::SUCCESS => Response::HTTP_OK,

            self::ERR_VALIDATION => Response::HTTP_UNPROCESSABLE_ENTITY,
            self::ERR_AUTHENTICATION,
            self::ERR_MISSING_SIGNATURE_HEADER,
            self::ERR_INVALID_SIGNATURE_HEADER,
            self::ERR_INVALID_IP_ADDRESS => Response::HTTP_UNAUTHORIZED,

            self::ERR_ACTION_UNAUTHORIZED,
            self::ERR_FORBIDDEN => Response::HTTP_FORBIDDEN,

            self::ERR_INVALID_OPERATION => Response::HTTP_EXPECTATION_FAILED,

            self::ERR_ENTITY_NOT_FOUND,
            self::ERR_ROUTE_NOT_FOUND => Response::HTTP_NOT_FOUND,

            self::ERR_CONFLICT,
            self::ERR_UNIQUE_RECORD => Response::HTTP_CONFLICT,

            self::ERR_RECORD_CONSTRAINT,
            self::ERR_QUERY_EXCEPTION => Response::HTTP_BAD_REQUEST,

            self::ERR_UNKNOWN => Response::HTTP_INTERNAL_SERVER_ERROR,
        };
    }
}
