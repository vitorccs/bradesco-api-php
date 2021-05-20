<?php

namespace BradescoApi\Helpers;

class Fixer
{
    /**
     * @var string[]
     */
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

    /**
     * @var string[]
     */
    protected static $dateFields = [
        'dtEmissaoTitulo',
        'dtVencimentoTitulo'
    ];

    /**
     * @var string[]
     */
    protected static $cpfCnpjFields = [
        'nuCpfcnpjPagador',
        'nuCpfcnpjSacadorAvalista'
    ];

    /**
     * @var string[]
     */
    protected static $currencyFields = [
        'vlJuros',
        'vlMulta',
        'vlNominalTitulo'
    ];

    /**
     * @var string[]
     */
    protected static $textFields = [
        'nomePagador',
        'logradouroPagador',
        'complementoLogradouroPagador',
        'bairroPagador',
        'municipioPagador',
        'ufPagador',
        'nomeSacadorAvalista',
        'logradouroSacadorAvalista',
        'complementoLogradouroSacadorAvalista',
        'bairroSacadorAvalista',
        'municipioSacadorAvalista',
        'ufSacadorAvalista'
    ];

    /**
     * @var array[]
     */
    protected static $clipTextields = [
        ['nuCliente', 10],
        ['controleParticipante', 25],
        ['nomePagador', 70],
        ['logradouroPagador', 40],
        ['complementoLogradouroPagador', 15],
        ['bairroPagador', 40],
        ['municipioPagador', 30],
        ['ufPagador', 2],
        ['nomeSacadorAvalista', 40],
        ['logradouroSacadorAvalista', 40],
        ['complementoLogradouroSacadorAvalista', 15],
        ['bairroSacadorAvalista', 40],
        ['municipioSacadorAvalista', 40],
        ['ufSacadorAvalista', 2]
    ];

    /**
     * @param array $data
     */
    public static function mergeWithDefaultData(array &$data)
    {
        $data = array_merge(static::$defaultBankSlip, $data);
    }

    /**
     * @param array $data
     */
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

        foreach(static::$textFields as $field) {
            if (!isset($data[$field])) continue;
            $data[$field] = Formatter::formatAlpha($data[$field]);
        }

        foreach(static::$clipTextields as $rule) {
            $field  = reset($rule);
            $clip   = end($rule);
            if (!isset($data[$field])) continue;
            $data[$field] = Formatter::clipText($data[$field], $clip);
        }
    }

    /**
     * @param array $data
     */
    public static function changeNullToEmpty(array &$data)
    {
        array_walk($data, function(&$item, $key) {
            if ($item === null) {
                $item = "";
            }
        });
    }

    /**
     * @param array $data
     */
    public static function changeNumericToString(array &$data)
    {
        array_walk($data, function(&$item, $key) {
            if (is_float($item) || is_int($item)) {
                $item = (string) $item;
            }
        });
    }

    /**
     * @param array $data
     */
    public static function setCustomerType(array &$data)
    {
        if ($data['cdIndCpfcnpjPagador'] ?? null) return;

        if (!isset($data['nuCpfcnpjPagador'])) return;

        $isPerson = preg_match('/^000/', $data['nuCpfcnpjPagador']);

        $data['cdIndCpfcnpjPagador'] = $isPerson ? "1" : "2";
    }

    /**
     * @param array $data
     */
    public static function fixAll(array &$data)
    {
        // Per Bradesco API specs, all non-used fields must be
        // sent anyways but with their default values (0 or "")
        static::mergeWithDefaultData($data);

        // Format currency, date, text  and "CPF/CNPJ" fields to API specs
        static::formatData($data);

        // Bradesco API does not accept null, only empty
        static::changeNullToEmpty($data);

        // Bradesco API does not accept integer or float, only string
        static::changeNumericToString($data);

        // Automatically fill "cdIndCpfcnpjPagador" field
        static::setCustomerType($data);
    }
}
