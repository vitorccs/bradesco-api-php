<?php

declare(strict_types=1);

namespace BradescoApi\Test\Helpers;

use BradescoApi\Helpers\Formatter;
use PHPUnit\Framework\TestCase;

class FormatterTest extends TestCase
{
    public function testNumericFormatter()
    {
        $before = "1234567890!@#$%*()_+{}:><,.;/]-'\" 1234567890";
        $after = "12345678901234567890";

        $this->assertEquals(Formatter::cleanNumeric($before), $after);
    }

    public function testBasicLatinFormatter()
    {
        $before = "ãáàâÃÁÀÂẽéèêẼÉÈÊĩíìîĨÍÌÎõóòôÕÓÒÔũúùûŨÚÙÛçÇ";
        $after = "aaaaAAAAeeeeEEEEiiiiIIIIooooOOOOuuuuUUUUcC";

        $this->assertEquals(Formatter::formatAscii($before), $after);
    }

    public function testCurrencyFormatter()
    {
        $before = [20.40, '20,40', "R$20,40", "R$ 20,40", "20.40"];
        $after = 2040;

        foreach ($before as $key => $value) {
            $this->assertEquals(Formatter::formatCurrency($value), $after);
        }
    }

    public function testDateFormatter()
    {
        $before = ["2020-12-31", "31/12/2020", new \DateTime("2020-12-31")];
        $after = "31.12.2020";

        foreach ($before as $b => $value) {
            $this->assertEquals(Formatter::formatDate($value), $after);
        }
    }

    public function testCpfFormatter()
    {
        $before = ["123.456.789-00", 12345678900];
        $after = "00012345678900";

        foreach ($before as $b => $value) {
            $this->assertEquals(Formatter::formatCpfCnpj($value), $after);
        }
    }

    public function testCnpjFormatter()
    {
        $before = ["60.746.948/0001-12", 60746948000112];
        $after = "60746948000112";

        foreach ($before as $b => $value) {
            $this->assertEquals(Formatter::formatCpfCnpj($value), $after);
        }
    }

    public function testAlphaFormatter()
    {
        $before = "ãáàâÃÁÀÂẽéèêẼÉÈÊĩíìîĨÍÌÎõóòôÕÓÒÔũúùûŨÚÙÛçÇ!@#$%*()_+{}:><,.;/]-'\" ";
        $after = "aaaaAAAAeeeeEEEEiiiiIIIIooooOOOOuuuuUUUUcC- ";

        $this->assertEquals(Formatter::formatAlpha($before), $after);
    }
}
