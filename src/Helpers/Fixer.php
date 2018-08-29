<?php
namespace BradescoApi\Helpers;

class Fixer
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

    protected static $dateFields = [
        'dtEmissaoTitulo',
        'dtVencimentoTitulo'
    ];

    protected static $cpfCnpjFields = [
        'nuCpfcnpjPagador',
        'nuCpfcnpjSacadorAvalista'
    ];

    protected static $currencyFields = [
        'vlJuros',
        'vlMulta',
        'vlNominalTitulo'
    ];

    public static function mergeWithDefaultData(array $data)
    {
        return array_merge(static::$defaultBankSlip, $data);
    }

    public static function formatData(array $data)
    {
        foreach(static::$dateFields as $field) {
            if (!isset($data[$field])) continue;
            $data[$field] = Formatter::formatDate($data[$field]);
        }

        foreach(static::$cpfCnpjFields as $field) {
            if (!isset($data[$field])) continue;
            $data[$field] = Formatter::formatCpfCnpj($data[$field]);
        }

        foreach(static::$currencyFields as $field) {
            if (!isset($data[$field])) continue;
            $data[$field] = Formatter::formatCurrency($data[$field]);
        }

        return $data;
    }
}

?>
