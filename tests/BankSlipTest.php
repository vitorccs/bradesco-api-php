<?php

declare(strict_types=1);

namespace BradescoApi\Test;

use BradescoApi\Http\Api;
use PHPUnit\Framework\TestCase;
use BradescoApi\BankSlip;
use BradescoApi\Exceptions\BradescoApiException;

class BankSlipTest extends TestCase
{
    /**
     * @dataProvider validDataProvider
     */
    public function testSuccessfulResponse(array $validData)
    {
        $bankSlip = BankSlip::create($validData);

        $errorCode = (int)$bankSlip->cdErro ?? null;
        $success = $errorCode === Api::SUCCESS_CODE;

        $this->assertTrue($success);
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

    /**
     * Valid Data Provider
     */
    public function validDataProvider(): array
    {
        $userData = (array)json_decode(getenv('DATA'));

        $defaultData = [
            "idProduto" => "09",
            "nuCliente" => "123456",
            "dtEmissaoTitulo" => date('Y-m-d'),
            "dtVencimentoTitulo" => date('Y-m-d'),
            "vlNominalTitulo" => 10,
            "cdEspecieTitulo" => "1",
            "nomePagador" => "Cliente Teste",
            "logradouroPagador" => "Rua Teste",
            "nuLogradouroPagador" => "90",
            "cepPagador" => "12345",
            "complementoCepPagador" => "500",
            "bairroPagador" => "Bairro Teste",
            "municipioPagador" => "Teste",
            "ufPagador" => "SP",
            "cdIndCpfcnpjPagador" => "1",
            "nuCpfcnpjPagador" => "00087912543023"
        ];

        return [
            'bank_slip' => [ array_merge($defaultData, $userData) ]
        ];
    }

    /**
     * Invalid Data Provider
     */
    public function invalidDataProvider(): array
    {
        return [
            'bank_slip' => [ [] ]
        ];
    }
}
