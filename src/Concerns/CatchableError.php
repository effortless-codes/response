<?php

namespace Winata\Core\Response\Concerns;

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Validation\UnauthorizedException;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response as SymfonyResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Throwable;
use Winata\Core\Response\Enums\DefaultResponseCode;
use Winata\Core\Response\Exception\ApiDumpException;
use Winata\Core\Response\Exception\BaseException;

/**
 * Trait CatchableError
 *
 * Provides centralized exception mapping to `BaseException` and structured
 * JSON response handling based on configuration and request context.
 */
trait CatchableError
{
    /**
     * Render the exception as an HTTP response.
     *
     * If the application is configured to force JSON responses or the request
     * expects JSON, and the exception is not already a `BaseException`, it will
     * be converted using `mapToBaseException()`.
     *
     * @param Request $request
     * @param Throwable $e
     * @return SymfonyResponse|JsonResponse|Response
     * @throws Throwable
     */
    public function render($request, Throwable $e): SymfonyResponse|JsonResponse|Response
    {
        if ($e instanceof ApiDumpException){
            return parent::render($request, $e);
        }

        if (!$e instanceof BaseException) {
            $e = $this->mapToBaseException($request, $e);
        }

        if (config('winata.response.force_to_json')) {
            return response()->json($e->toArray(), $e->rc->httpCode());
        }

        if ($e instanceof BaseException && $request->expectsJson()) {
            return response()->json($e->toArray(), $e->rc->httpCode());
        }

        return parent::render($request, $e);
    }

    /**
     * Map Laravel core exceptions to custom BaseException instances.
     *
     * Converts known Laravel exceptions to a structured `BaseException`
     * format, enabling consistent error responses for API consumers.
     *
     * @param Request $request
     * @param Throwable $e
     * @return BaseException|Throwable
     */
    private function mapToBaseException(Request $request, Throwable $e): BaseException|Throwable
    {
        return match (true) {
            $e instanceof ModelNotFoundException =>
            new BaseException(DefaultResponseCode::ERR_ENTITY_NOT_FOUND, previous: $e),

            $e instanceof ValidationException =>
            new BaseException(DefaultResponseCode::ERR_VALIDATION, $e->getMessage(), $e->errors(), $e),

            $e instanceof AuthenticationException =>
            new BaseException(DefaultResponseCode::ERR_AUTHENTICATION, $e->getMessage(), null, $e),

            $e instanceof AuthorizationException,
                $e instanceof UnauthorizedException =>
            new BaseException(DefaultResponseCode::ERR_ACTION_UNAUTHORIZED, $e->getMessage(), null, $e),

            $e instanceof NotFoundHttpException =>
            new BaseException(DefaultResponseCode::ERR_ROUTE_NOT_FOUND, $e->getMessage(), null, $e),

            $e instanceof UniqueConstraintViolationException =>
            new BaseException(DefaultResponseCode::ERR_UNIQUE_RECORD, __('Unique Records Violation in Table.'), null, $e),

            $e instanceof QueryException => str($e->getMessage())->contains('Foreign key violation')
                ? new BaseException(DefaultResponseCode::ERR_RECORD_CONSTRAINT, __('Record probably in use.'), null, $e)
                : new BaseException(DefaultResponseCode::ERR_QUERY_EXCEPTION, __('An unknown query error occurred. Contact developer.'), null, $e),

            default => new BaseException(
                rc: DefaultResponseCode::ERR_UNKNOWN,
                message: $e->getMessage(),
                data: [
                    'base_url' => $request->getBaseUrl(),
                    'path'     => $request->getUri(),
                    'origin'   => $request->ip(),
                    'method'   => $request->getMethod(),
                ],
                previous: $e
            ),
        };
    }
}
