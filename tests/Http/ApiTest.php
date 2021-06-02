<?php

declare(strict_types=1);

namespace BradescoApi\Test\Http;

use BradescoApi\Exceptions\BradescoRequestException;
use BradescoApi\Http\Api;
use BradescoApi\Test\AbstractTest;

class ApiTest extends AbstractTest
{
    public function testRequestException()
    {
        $this->expectException(BradescoRequestException::class);

        $api = new Api();
        $api->post([], 'INVALID');
    }

    /**
     * @dataProvider validDataProvider
     */
    public function testEncryption(array $validData)
    {
        $api = new Api();
        $encrypted = $api->encryptBodyData($validData);

        $this->assertNotEmpty($encrypted);
    }
}
