<?php

declare(strict_types=1);

namespace BradescoApi\Test\Http;

use BradescoApi\Exceptions\BradescoRequestException;
use BradescoApi\Http\Api;
use PHPUnit\Framework\TestCase;

class ApiTest extends TestCase
{
    public function testRequestException()
    {
        $this->expectException(BradescoRequestException::class);

        $api = new Api();
        $api->post([], 'INVALID');
    }
}
