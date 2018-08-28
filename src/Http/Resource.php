<?php
namespace BradescoApi\Http;

abstract class Resource
{
    protected static $api = null;

    public static function api()
    {
        if (is_null(static::$api)) {
            static::$api = new Api();
        }

        return static::$api;
    }

    public static function create(array $params)
    {
        $data = static::api()->post($params);

        return $data;
    }
}
