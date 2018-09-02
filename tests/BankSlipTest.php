<?php
declare(strict_types=1);

namespace ApiBradesco\Test;

use PHPUnit\Framework\TestCase;
use BradescoApi\BankSlip;
use BradescoApi\Exceptions\BradescoValidationException;

class BankSlipTest extends TestCase
{
    /**
     * @test
     * @expectedException        BradescoApi\Exceptions\BradescoValidationException
     * @expectedExceptionMessage Contrato não encontrado (-2)
     */
    public function it_should_get_contract_not_found()
    {
        $data = [];
        $bankSlip = BankSlip::create($data);
    }

    /**
     * @test
     */
    public function it_should_register_bank_slip()
    {
        $data               = (array) json_decode(getenv('DATA'));

        $defaultData        = [
            "nomePagador"           => "Cliente Teste",
            "logradouroPagador"     => "Rua Teste",
            "nuLogradouroPagador"   => "90",
            "cepPagador"            => "12345",
            "complementoCepPagador" => "500",
            "bairroPagador"         => "Bairro Teste",
            "municipioPagador"      => "Teste",
            "ufPagador"             => "SP",
            "cdIndCpfcnpjPagador"   => "1",
            "nuCpfcnpjPagador"      => "00087912543023"
        ];

        $data               = array_merge($defaultData, $data);

        $bankSlip           = BankSlip::create($data);

        $errorCode          = $bankSlip->cdErro ?? 0;
        $success            = ($errorCode == 0);

        $this->assertTrue($success);
    }
}
