<?php
declare(strict_types=1);

namespace ApiBradesco\Test;

use PHPUnit\Framework\TestCase;
use BradescoApi\BankSlip;
use BradescoApi\Exceptions\BradescoApiException;
use BradescoApi\Exceptions\BradescoRequestException;
use BradescoApi\Http\Bradesco;

class BankSlipTest extends TestCase
{
    protected $data = [];

    public function setUp()
    {
        $userData    = (array) json_decode(getenv('DATA'));

        $defaultData = [
            "idProduto"             => "09",
            "nuCliente"             => "123456",
            "dtEmissaoTitulo"       => date('Y-m-d'),
            "dtVencimentoTitulo"    => date('Y-m-d'),
            "vlNominalTitulo"       => 10,
            "cdEspecieTitulo"       => "1",
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

        $this->data = array_merge($defaultData, $userData);
    }

    /**
     * @test
     */
    public function it_should_register_bank_slip()
    {
        $bankSlip           = BankSlip::create($this->data);

        $errorCode          = $bankSlip->cdErro ?? 0;
        $success            = ($errorCode == 0);

        $this->assertTrue($success);
    }

    /**
     * @test
     * @expectedException        BradescoApi\Exceptions\BradescoApiException
     * @expectedExceptionMessage Contrato não encontrado
     */
    public function it_should_get_api_exception()
    {
        $data = [];
        $bankSlip = BankSlip::create($data);
    }

    /**
     * @test
     * @expectedException        BradescoApi\Exceptions\BradescoRequestException
     */
    public function it_should_get_request_exception()
    {
        Bradesco::setApiUrl('https://cobranca.bradesconetempresa.b.br/INVALID');
        BankSlip::reconfig();

        $bankSlip = BankSlip::create($this->data);
    }
}
