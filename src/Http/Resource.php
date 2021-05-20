<?php

namespace BradescoApi\Http;

use BradescoApi\Exceptions\BradescoApiException;
use BradescoApi\Exceptions\BradescoRequestException;

abstract class Resource
{
    /**
     * @var API|null
     */
    protected static $api = null;

    /**
     * Refresh API settings
     */
    public static function reconfig(): void
    {
        static::$api = new Api();
    }

    /**
     * @return API
     */
    public static function api(): API
    {
        if (is_null(static::$api)) {
            static::reconfig();
        }

        return static::$api;
    }

    /**
     * @param array $params
     * @return \stdClass
     * @throws BradescoApiException
     * @throws BradescoRequestException
     */
    public static function create(array $params): \stdClass
    {
        return static::api()->post($params);
    }
}
