<?php

use Winata\Core\Response\Exception\ApiDumpException;

if (!function_exists('ddapi')) {
    /**
     * @param mixed $data
     * @return mixed
     * @throws ApiDumpException
     */
    function ddapi(mixed $data)
    {
        throw new ApiDumpException($data);
    }
}
