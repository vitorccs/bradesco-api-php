<?php
declare(strict_types=1);

namespace ApiBradesco\Test;

use PHPUnit\Framework\TestCase;
use BradescoApi\BankSlip;
use BradescoApi\Exceptions\BradescoClientException;

class BankSlipTest extends TestCase
{
    /** @test */
    public function it_should_get_contract_not_found()
    {
        $data               = [];
        $bankSlip           = BankSlip::create($data);

        $errorCode          = $bankSlip->cdErro ?? null;
        $isContractNotFound = ($errorCode == -2);

        $this->assertTrue($isContractNotFound);
    }
}
