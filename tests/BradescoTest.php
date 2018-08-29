<?php
declare(strict_types=1);

namespace ApiBradesco\Test;

use PHPUnit\Framework\TestCase;
use BradescoApi\Http\Bradesco;
use BradescoApi\Exceptions\BradescoClientException;

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
}
