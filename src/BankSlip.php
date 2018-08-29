<?php
namespace BradescoApi;

use BradescoApi\Http\Resource;
use BradescoApi\Helpers\Fixer;

class BankSlip extends Resource
{
    public static function create(array $data)
    {
        $data = Fixer::mergeWithDefaultData($data);
        $data = Fixer::formatData($data);

        $response = parent::create($data);

        return $response;
    }
}
