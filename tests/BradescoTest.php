<?php
declare(strict_types=1);

namespace ApiBradesco\Test;

use PHPUnit\Framework\TestCase;
use BradescoApi\Http\Bradesco;

class BradescoTest extends TestCase
{
    /** @test */
    public function it_should_be_sandbox_mode()
    {
        $isSandboxMode = Bradesco::isSandbox();

        $this->assertTrue($isSandboxMode);
    }

    /** @test */
    public function it_should_be_sandbox_url()
    {
        $apiUrl        = Bradesco::getApiUrl();
        $urlIsSandbox  = strpos($apiUrl, 'registrotitulohomologacao') !== FALSE;

        $this->assertTrue($urlIsSandbox);
    }

    /** @test */
    public function it_should_set_folder_timeout()
    {
        $envValue         = getenv(Bradesco::TIMEOUT);
        $bradescoValue    = Bradesco::getTimeout();

        $this->assertEquals($bradescoValue, $envValue);
    }

    /** @test */
    public function it_should_set_cert_path()
    {
        $envValue         = getenv(Bradesco::CERT_PATH);
        $bradescoValue    = Bradesco::getCertPath();

        $this->assertEquals($bradescoValue, $envValue);
    }

    /** @test */
    public function it_should_set_cert_password()
    {
        $envValue         = getenv(Bradesco::CERT_PASSWORD);
        $bradescoValue    = Bradesco::getCertPassword();

        $this->assertEquals($bradescoValue, $envValue);
    }

    /** @test */
    public function it_should_set_folder_path()
    {
        $envValue         = getenv(Bradesco::FOLDER_PATH);
        $bradescoValue    = Bradesco::getFolderPath();

        $this->assertEquals($bradescoValue, $envValue);
    }

    /** @test */
    public function it_should_set_by_params()
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
        $params = [
            'BRADESCO_TIMEOUT' => getenv(Bradesco::TIMEOUT),
            'BRADESCO_CERT_PATH' => getenv(Bradesco::CERT_PATH),
            'BRADESCO_CERT_PASSWORD' => getenv(Bradesco::CERT_PASSWORD),
            'BRADESCO_FOLDER_PATH' => getenv(Bradesco::FOLDER_PATH)
        ];
        Bradesco::setParams($params);
    }
}
