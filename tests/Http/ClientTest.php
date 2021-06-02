<?php

declare(strict_types=1);

namespace BradescoApi\Test\Http;

use BradescoApi\Exceptions\BradescoParameterException;
use BradescoApi\Http\Bradesco;
use BradescoApi\Http\Client;
use BradescoApi\Test\AbstractTest;

class ClientTest extends AbstractTest
{
    /**
     * Run once before all tests
     */
    public static function setUpBeforeClass(): void
    {
        static::backupParameters();
    }

    public function testInvalidCertPassword()
    {
        $this->expectException(BradescoParameterException::class);

        Bradesco::setCertPassword('INVALID');

        new Client();
    }

    public function testInvalidCertPath()
    {
        $this->expectException(BradescoParameterException::class);

        Bradesco::setCertPath('INVALID');

        new Client();
    }

    public function testSuccessCert()
    {
        $client = new Client();

        $this->assertNotEmpty($client->getCertKey());
        $this->assertNotEmpty($client->getPrivateKey());
    }

    /**
     * Run after each test
     */
    public function tearDown(): void
    {
        static::restoreParameters();
    }
}
