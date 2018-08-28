<?php
namespace BradescoApi;

use BradescoApi\Http\Resource;

class BankSlip extends Resource
{
    protected static $defaultBankSlip = [
        "clubBanco" => "0",
        "cdTipoContrato" => "0",
        "nuSequenciaContrato" => "0",
        "cdBanco" => "237",
        "eNuSequenciaContrato" => "0",
        "tpRegistro" => "1",
        "cdProduto" => "0",
        "nuTitulo" => "0",
        "tpVencimento" => "0",
        "tpProtestoAutomaticoNegativacao" => "0",
        "prazoProtestoAutomaticoNegativacao" => "0",
        "controleParticipante" => "",
        "cdPagamentoParcial" => "",
        "qtdePagamentoParcial" => "0",
        "percentualJuros" => "0",
        "vlJuros" => "0",
        "qtdeDiasJuros" => "0",
        "percentualMulta" => "0",
        "vlMulta" => "0",
        "qtdeDiasMulta" => "0",
        "percentualDesconto1" => "0",
        "vlDesconto1" => "0",
        "dataLimiteDesconto1" => "",
        "percentualDesconto2" => "0",
        "vlDesconto2" => "0",
        "dataLimiteDesconto2" => "",
        "percentualDesconto3" => "0",
        "vlDesconto3" => "0",
        "dataLimiteDesconto3" => "",
        "prazoBonificacao" => "0",
        "percentualBonificacao" => "0",
        "vlBonificacao" => "0",
        "dtLimiteBonificacao" => "",
        "vlAbatimento" => "0",
        "vlIOF" => "0",
        "endEletronicoPagador" => "",
        "nomeSacadorAvalista" => "",
        "logradouroSacadorAvalista" => "",
        "nuLogradouroSacadorAvalista" => "0",
        "complementoLogradouroSacadorAvalista" => "",
        "cepSacadorAvalista" => "0",
        "complementoCepSacadorAvalista" => "0",
        "bairroSacadorAvalista" => "",
        "municipioSacadorAvalista" => "",
        "ufSacadorAvalista" => "",
        "cdIndCpfcnpjSacadorAvalista" => "0",
        "nuCpfcnpjSacadorAvalista" => "0",
        "endEletronicoSacadorAvalista" => ""
    ];

    public static function create(array $data)
    {
        $data = array_merge(static::$defaultBankSlip, $data);

        $response = parent::create($data);

        return $response;
    }
}
