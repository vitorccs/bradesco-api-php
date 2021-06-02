<?php

namespace BradescoApi\Test;

use BradescoApi\Http\Bradesco;
use PHPUnit\Framework\TestCase;

abstract class AbstractTest extends TestCase
{
    protected static $parameters = [];

    protected static function backupParameters(): void
    {
        $parameters = [
            Bradesco::SANDBOX => "true",
            Bradesco::TIMEOUT => getenv(Bradesco::TIMEOUT),
            Bradesco::CERT_PATH => getenv(Bradesco::CERT_PATH),
            Bradesco::CERT_PASSWORD => getenv(Bradesco::CERT_PASSWORD),
            Bradesco::FOLDER_PATH => getenv(Bradesco::FOLDER_PATH)
        ];

        $validParameters = array_filter($parameters, function($param) {
            return $param !== false;
        });

        static::$parameters = $validParameters;
    }

    protected static function restoreParameters(): void
    {
        Bradesco::setParams(static::$parameters);

        foreach (static::$parameters as $key => $value) {
            putenv("{$key}={$value}");
        }
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
            'valid_bank_slip' => [ array_merge($defaultData, $userData) ]
        ];
    }

    /**
     * Invalid Data Provider
     */
    public function invalidDataProvider(): array
    {
        return [
            'invalid_bank_slip' => [ [] ]
        ];
    }
}
