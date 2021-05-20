<?php

declare(strict_types=1);

namespace BradescoApi\Test;

use PHPUnit\Framework\TestCase;
use BradescoApi\Http\Bradesco;

class BradescoTest extends TestCase
{
    public function testSandboxModeSetByEnv()
    {
        $this->assertTrue(Bradesco::isSandbox());
    }

    public function testIsSandboxUrl()
    {
        $apiUrl        = Bradesco::getApiUrl();
        $urlIsSandbox  = strpos($apiUrl, 'registrotitulohomologacao') !== FALSE;

        $this->assertTrue($urlIsSandbox);
    }

    public function testFolderTimeoutSetByEnv()
    {
        $envValue         = getenv(Bradesco::TIMEOUT);
        $bradescoValue    = Bradesco::getTimeout();

        $this->assertEquals($bradescoValue, $envValue);
    }

    public function testCertPathSetByEnv()
    {
        $envValue         = getenv(Bradesco::CERT_PATH);
        $bradescoValue    = Bradesco::getCertPath();

        $this->assertEquals($bradescoValue, $envValue);
    }

    public function testCertPasswordSetByEnv()
    {
        $envValue         = getenv(Bradesco::CERT_PASSWORD);
        $bradescoValue    = Bradesco::getCertPassword();

        $this->assertEquals($bradescoValue, $envValue);
    }

    public function testFolderPathSetByEnv()
    {
        $envValue         = getenv(Bradesco::FOLDER_PATH);
        $bradescoValue    = Bradesco::getFolderPath();

        $this->assertEquals($bradescoValue, $envValue);
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

        // rollback
        Bradesco::setParams([
            Bradesco::SANDBOX => getenv(Bradesco::SANDBOX),
            Bradesco::TIMEOUT => getenv(Bradesco::TIMEOUT),
            Bradesco::CERT_PATH => getenv(Bradesco::CERT_PATH),
            Bradesco::CERT_PASSWORD => getenv(Bradesco::CERT_PASSWORD),
            Bradesco::FOLDER_PATH => getenv(Bradesco::FOLDER_PATH)
        ]);
    }
}
