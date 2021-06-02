<?php

declare(strict_types=1);

namespace BradescoApi\Test;

use BradescoApi\Http\Api;
use BradescoApi\BankSlip;
use BradescoApi\Exceptions\BradescoApiException;

class BankSlipTest extends AbstractTest
{
    /**
     * @dataProvider validDataProvider
     */
    public function testSuccessfulResponse(array $validData)
    {
        $bankSlip = BankSlip::create($validData);

        $this->assertObjectHasAttribute('cdErro', $bankSlip);
        $this->assertEquals(Api::SUCCESS_CODE, $bankSlip->cdErro);
    }

    /**
     * @dataProvider invalidDataProvider
     */
    public function testFailedResponse(array $invalidData)
    {
        $this->expectException(BradescoApiException::class);
        $this->expectExceptionMessage('Contrato não encontrado');

        BankSlip::create($invalidData);
    }
}
