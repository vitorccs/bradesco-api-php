<?php

declare(strict_types=1);

namespace BradescoApi\Test;

use BradescoApi\Exceptions\BradescoParameterException;
use PHPUnit\Framework\TestCase;
use BradescoApi\BankSlip;
use BradescoApi\Http\Bradesco;

class ParameterTest extends TestCase
{
    /**
     * @var array
     */
    protected static $data = [];

    /**
     * @var array
     */
    protected static $backupParams = [];

    /**
     * Run once before all tests
     * Backup parameter values form env
     */
    public static function setUpBeforeClass()
    {
        static::$backupParams = [
            Bradesco::SANDBOX => getenv(Bradesco::SANDBOX),
            Bradesco::TIMEOUT => getenv(Bradesco::TIMEOUT),
            Bradesco::CERT_PATH => getenv(Bradesco::CERT_PATH),
            Bradesco::CERT_PASSWORD => getenv(Bradesco::CERT_PASSWORD),
            Bradesco::FOLDER_PATH => getenv(Bradesco::FOLDER_PATH)
        ];

        static::resetEnv();
    }

    public function testCertPathIsRequired()
    {
        $this->expectException(BradescoParameterException::class);
        $this->expectExceptionMessage("Missing required parameter 'CERT_PATH'");

        Bradesco::setCertPath();
        BankSlip::reconfig();
        BankSlip::create(static::$data);
    }

    public function testCertPasswordIsRequired()
    {
        $this->expectException(BradescoParameterException::class);
        $this->expectExceptionMessage("Missing required parameter 'CERT_PASSWORD'");

        Bradesco::setCertPassword();
        BankSlip::reconfig();
        BankSlip::create(static::$data);
    }

    public function testInvalidCertPassword()
    {
        $this->expectException(BradescoParameterException::class);

        Bradesco::setCertPassword('INVALID');
        BankSlip::reconfig();
        BankSlip::create(static::$data);
    }

    public function testInvalidCertPath()
    {
        $this->expectException(BradescoParameterException::class);

        Bradesco::setCertPath('INVALID');
        BankSlip::reconfig();
        BankSlip::create(static::$data);
    }

    /**
     * Reset env parameters to empty
     */
    public static function resetEnv()
    {
        foreach (static::$backupParams as $key => $value) {
            putenv($key);
        }
    }
}
