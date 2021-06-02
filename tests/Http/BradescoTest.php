<?php

declare(strict_types=1);

namespace BradescoApi\Test\Http;

use BradescoApi\Exceptions\BradescoParameterException;
use BradescoApi\Http\Bradesco;
use BradescoApi\Test\AbstractTest;

class BradescoTest extends AbstractTest
{
    /**
     * Run once before all tests
     */
    public static function setUpBeforeClass(): void
    {
        static::backupParameters();
    }

    public function testParamsSetByEnv()
    {
        $this->assertEquals(Bradesco::getTimeout(), getenv(Bradesco::TIMEOUT));
        $this->assertEquals(Bradesco::getCertPath(), getenv(Bradesco::CERT_PATH));
        $this->assertEquals(Bradesco::getCertPassword(), getenv(Bradesco::CERT_PASSWORD));
        $this->assertEquals(Bradesco::getFolderPath(), getenv(Bradesco::FOLDER_PATH));
    }

    public function testParamsSetByArray()
    {
        // set random value
        $params = [
            Bradesco::TIMEOUT => '60',
            Bradesco::CERT_PATH => 'BRADESCO_CERT_PATH',
            Bradesco::CERT_PASSWORD => 'BRADESCO_CERT_PASSWORD',
            Bradesco::FOLDER_PATH => 'BRADESCO_FOLDER_PATH'
        ];

        Bradesco::setParams($params);

        $this->assertEquals(Bradesco::getTimeout(), $params[Bradesco::TIMEOUT]);
        $this->assertEquals(Bradesco::getCertPath(), $params[Bradesco::CERT_PATH]);
        $this->assertEquals(Bradesco::getCertPassword(), $params[Bradesco::CERT_PASSWORD]);
        $this->assertEquals(Bradesco::getFolderPath(), $params[Bradesco::FOLDER_PATH]);
    }

    public function testCertPathIsRequired()
    {
        $this->expectException(BradescoParameterException::class);
        $this->expectExceptionMessage("Missing required parameter 'CERT_PATH'");

        self::unsetEnvParameter(Bradesco::CERT_PATH);

        Bradesco::setCertPath();
        Bradesco::getCertPath();
    }

    public function testCertPasswordIsRequired()
    {
        $this->expectException(BradescoParameterException::class);
        $this->expectExceptionMessage("Missing required parameter 'CERT_PASSWORD'");

        self::unsetEnvParameter(Bradesco::CERT_PASSWORD);

        Bradesco::setCertPassword();
        Bradesco::getCertPassword();
    }

    public function testDefaultIsSandbox()
    {
        $this->assertTrue(Bradesco::isSandbox());
    }

    public function testToggleSandbox()
    {
        Bradesco::setIsSandbox(false);
        $this->assertFalse(Bradesco::isSandbox());
        $this->assertEquals(Bradesco::getApiUrl(), Bradesco::getProductionUrl());

        Bradesco::setIsSandbox(true);
        $this->assertTrue(Bradesco::isSandbox());
        $this->assertEquals(Bradesco::getApiUrl(), Bradesco::getSandboxUrl());
    }

    /**
     * Unset Env Parameter
     */
    private static function unsetEnvParameter(string $parameter)
    {
        putenv($parameter);
    }

    /**
     * Run once after all tests
     */
    public function tearDown(): void
    {
        static::restoreParameters();
    }
}
