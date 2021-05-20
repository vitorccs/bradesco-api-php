<?php

namespace BradescoApi;

use BradescoApi\Http\Resource;
use BradescoApi\Helpers\Fixer;

class BankSlip extends Resource
{
    /**
     * @param array $data
     * @param bool $fix
     * @return \stdClass
     * @throws Exceptions\BradescoApiException
     * @throws Exceptions\BradescoRequestException
     */
    public static function create(array $data, bool $fix = true): \stdClass
    {
        if ($fix) {
            Fixer::fixAll($data);
        }

        return parent::create($data);
    }
}
