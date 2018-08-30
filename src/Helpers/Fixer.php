<?php
namespace BradescoApi\Helpers;

class Fixer
{
    protected static $defaultBankSlip = [
        // const values
        "cdBanco" => "237",
        "cdTipoAcesso" => "2",
        "tpRegistro" => "1",
        "cdTipoContrato" => "48",
        "clubBanco" => "2269651",
        "tpVencimento" => "0",

        // empty values
        "nuSequenciaContrato" => "0",
        "eNuSequenciaContrato" => "0",
        "cdProduto" => "0",
        "nuTitulo" => "0",
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

    public static function mergeWithDefaultData(array &$data)
    {
        $data = array_merge(static::$defaultBankSlip, $data);
    }

    public static function formatData(array &$data)
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
    }

    public static function changeNullToEmpty(array &$data)
    {
        array_walk($data, function(&$item, $key) {
            if ($item === null) $item = "";
        });
    }

    public static function setCustomerType(array &$data)
    {
        if ($data['cdIndCpfcnpjPagador'] ?? null) return;

        if (!isset($data['nuCpfcnpjPagador'])) return;

        $isPerson = preg_match('/^000/', $data['nuCpfcnpjPagador']);

        $data['cdIndCpfcnpjPagador'] = $isPerson ? "1" : "2";
    }

    public static function fixAll(array &$data)
    {
        // Per Bradesco API specs, all non-used fields must be
        // sent anyways but with their default values (0 or "")
        static::mergeWithDefaultData($data);

        // Format currency, date  and "CPF/CNPJ" values per Bradeso API specs
        static::formatData($data);

        // Bradesco API does not accept null, only empty
        static::changeNullToEmpty($data);

        // Automatically fill "cdIndCpfcnpjPagador" field
        static::setCustomerType($data);
    }
}

?>
