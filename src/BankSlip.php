<?php
namespace BradescoApi;

use BradescoApi\Http\Resource;
use BradescoApi\Helpers\Fixer;

class BankSlip extends Resource
{
    public static function create(array $data, bool $fix = true)
    {
        if ($fix) {
            Fixer::fixAll($data);
        }

        $response = parent::create($data);

        return $response;
    }
}
