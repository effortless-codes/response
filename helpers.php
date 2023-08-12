<?php

if (!function_exists('array_to_object')) {

    /**
     * Convert Array into Object in deep
     *
     * @param array $array
     * @return
     */
    function array_to_object($array)
    {
        return json_decode(json_encode($array));
    }
}
if (!function_exists('getFillableAttribute')) {

    /**
     * Convert Array into Object in deep
     *
     * @param string $model
     * @param array $data
     * @return array
     */
    function getFillableAttribute(string $model, array $data): array
    {
        $fillable = (new $model)->getFillable();

        return Arr::only($data, Arr::flatten($fillable));
    }
}
