<?php

namespace BradescoApi\Helpers;

class Formatter
{
    /**
     * Remove non numeric chars
     *
     * @param string|int $value
     * @return string
     */
    public static function cleanNumeric($value): string
    {
        return preg_replace("/[^0-9]/", '', (string) $value);
    }

    /**
     * Clip text up to max chars allowed
     *
     * @param string|int $value
     * @param int $clip
     * @return string
     */
    public static function clipText($value, int $clip): string
    {
        $value = (string) $value;

        if (strlen($value) > $clip) {
            $value = substr($value, 0, $clip);
        }

        return $value;
    }

    /**
     * Format text per Bradesco specs:
     * Transliterate the string into an ASCII value
     * (other chars may cause unexpected behaviour with Bradesco API)
     *
     * @param string $value
     * @return array|string|string[]|null
     */
    public static function formatAscii(string $value): string
    {
        $rules = [
            ['[ãáàâ]', 'a'],
            ['[ÃÁÀÂ]', 'A'],
            ['[ẽéèê]', 'e'],
            ['[ẼÉÈÊ]', 'E'],
            ['[ĩíìî]', 'i'],
            ['[ĨÍÌÎ]', 'I'],
            ['[õóòô]', 'o'],
            ['[ÕÓÒÔ]', 'O'],
            ['[ũúùû]', 'u'],
            ['[ŨÚÙÛ]', 'U'],
            ['[ç]', 'c'],
            ['[Ç]', 'C']
        ];

        foreach ($rules as $rule) {
            $value = preg_replace('/'. $rule[0] . '/u', $rule[1], $value);
        }

        return $value;
    }

    /**
     * Format number per Bradesco specs:
     * Without thousand and decimal separators
     *
     * @param $value
     * @return string
     */
    public static function formatCurrency($value): string
    {
        if (is_float($value) || is_int($value)) {
            $value = number_format($value, 2, "", "");
        } else {
            $value = static::cleanNumeric($value);
        }

        return $value;
    }

    /**
     * Format date per Bradesco specs:
     * dd.mm.yyyy
     *
     * @param $value
     * @return \DateTime|false|mixed|string
     */
    public static function formatDate($value)
    {
        // remove the time part
        if (is_string($value)) {
            $value = substr($value, 0, 10);
        }

        if (is_string($value) && preg_match('/\d{2}\/\d{2}\/\d{4}/', $value)) {
            $value = \DateTime::createFromFormat('d/m/Y', $value);
        }

        if (is_string($value) && preg_match('/\d{4}\-\d{2}\-\d{2}/', $value)) {
            $value = \DateTime::createFromFormat('Y-m-d', $value);
        }

        if (is_object($value) && method_exists($value, 'format')) {
            $value = $value->format('d.m.Y');
        }

        return $value;
    }

    /**
     * Format CPF/CNPJ per Bradesco specs:
     * Only numeric chars with 14 leading zeros
     *
     * @param string|int $value
     * @return string
     */
    public static function formatCpfCnpj($value): string
    {
        $value = static::cleanNumeric($value);

        return str_pad($value, 14, '0', STR_PAD_LEFT);
    }

    /**
     * Format CPF/CNPJ per Bradesco specs:
     * Only alphanumeric chars plus space and slash
     *
     * @param $value
     * @return string
     */
    public static function formatAlpha($value): string
    {
        $value = (string) $value;
        $value = static::formatAscii($value);
        return preg_replace("/[^0-9a-z \-]/i", '', $value);
    }
}

