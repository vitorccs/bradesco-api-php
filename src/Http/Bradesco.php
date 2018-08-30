<?php
namespace BradescoApi\Http;

use BradescoApi\Exceptions\BradescoClientException;

class Bradesco
{
    const SANDBOX                 = 'BRADESCO_SANDBOX';
    const TIMEOUT                 = 'BRADESCO_TIMEOUT';
    const CERT_PATH               = 'BRADESCO_CERT_PATH';
    const CERT_PASSWORD           = 'BRADESCO_CERT_PASSWORD';
    const FOLDER_PATH             = 'BRADESCO_FOLDER_PATH';

    private static $apiUrl        = null;
    private static $sandbox       = null;
    private static $timeout       = null;

    private static $defIsSandbox  = true;
    private static $defTimeout    = 30;
    private static $certPath      = null;
    private static $certPassword  = null;
    private static $folderPath    = null;
    private static $sandboxUrl    = 'https://cobranca.bradesconetempresa.b.br/ibpjregistrotitulows/registrotitulohomologacao';
    private static $productionUrl = 'https://cobranca.bradesconetempresa.b.br/ibpjregistrotitulows/registrotitulo';
    private static $sdkVersion    = 1.0;

    public static function setIsSandbox(bool $enable = null)
    {
        static::$sandbox = $enable;

        if (static::$sandbox === null) {
            static::$sandbox = getenv(static::SANDBOX);
        }

        if (static::$sandbox === false) {
            static::$sandbox = static::$defIsSandbox;
        }

        if (strtolower(static::$sandbox) === "false") {
            static::$sandbox = false;
        }
    }

    public static function isSandbox()
    {
        if (static::$sandbox === null) {
            static::setIsSandbox();
        }

        return static::$sandbox;
    }

    public static function setApiUrl(string $url = null)
    {
        static::$apiUrl = $url;

        if (static::$apiUrl === null) {
            static::$apiUrl = static::isSandbox() ? static::$sandboxUrl : static::$productionUrl;
        }
    }

    public static function getApiUrl()
    {
        if (static::$apiUrl === null) {
            static::setApiUrl();
        }

        return static::$apiUrl;
    }

    public static function setTimeout(int $seconds = null)
    {
        static::$timeout = $seconds;

        if (static::$timeout === null) {
            static::$timeout = getenv(static::TIMEOUT);
        }

        if (static::$timeout === false) {
            static::$timeout = static::$defTimeout;
        }
    }

    public static function setCertPath(string $path = null)
    {
        static::$certPath = $path;

        if (static::$certPath === null) {
            static::$certPath = getenv(static::CERT_PATH);
        }

        if (static::$certPath === false) {
            throw new BradescoClientException("Missing required parameter 'CERT_PATH'");
        }
    }

    public static function setFolderPath(string $path = null)
    {
        static::$folderPath = $path;

        if (static::$folderPath === null) {
            static::$folderPath = getenv(static::FOLDER_PATH);
        }

        if (static::$folderPath === false) {
            static::$folderPath = '';
        }
    }

    public static function setCertPassword(string $password = null)
    {
        static::$certPassword = $password;

        if (static::$certPassword === null) {
            static::$certPassword = getenv(static::CERT_PASSWORD);
        }

        if (static::$certPassword === false) {
            throw new BradescoClientException("Missing required parameter 'CERT_PASSWORD'");
        }
    }

    public static function getTimeout()
    {
        if (static::$timeout === null) {
            static::setTimeout();
        }

        return static::$timeout;
    }

    public static function getCertPath()
    {
        if (static::$certPath === null) {
            static::setCertPath();
        }

        return static::$certPath;
    }

    public static function getFolderPath()
    {
        if (static::$folderPath === null) {
            static::setFolderPath();
        }

        return static::$folderPath;
    }

    public static function getCertPassword()
    {
        if (static::$certPassword === null) {
            static::setCertPassword();
        }

        return static::$certPassword;
    }

    public static function getSdkVersion()
    {
        return static::$sdkVersion;
    }
}
