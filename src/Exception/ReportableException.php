<?php

namespace Winata\Core\Response\Exception;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Winata\Core\Response\Concerns\CatchableError;

/**
 * Class ReportableException
 *
 * Global exception handler to catch and format application exceptions
 * using the CatchableError trait.
 */
class ReportableException extends ExceptionHandler
{
    use CatchableError;
}
