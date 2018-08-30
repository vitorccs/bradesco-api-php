<?php
namespace BradescoApi;

use BradescoApi\Http\Resource;
use BradescoApi\Helpers\Fixer;

class BankSlip extends Resource
{
    public static function create(array $data)
    {
        Fixer::fixAll($data);

        $response = parent::create($data);

        return $response;
    }
}
