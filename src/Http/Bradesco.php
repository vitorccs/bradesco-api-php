<?php

namespace BradescoApi\Http;

use BradescoApi\Exceptions\BradescoParameterException;

class Bradesco
{
    /**
     * The key name for setting Sandbox parameter
     */
    const SANDBOX = 'BRADESCO_SANDBOX';

    /**
     * The key name for setting Bradesco Timeout parameter
     */
    const TIMEOUT = 'BRADESCO_TIMEOUT';

    /**
     * The key name for setting Certification Path parameter
     */
    const CERT_PATH = 'BRADESCO_CERT_PATH';

    /**
     * The key name for setting Certification Password parameter
     */
    const CERT_PASSWORD = 'BRADESCO_CERT_PASSWORD';

    /**
     * The key name for setting Folder Path parameter
     */
    const FOLDER_PATH = 'BRADESCO_FOLDER_PATH';

    /**
     * @var string|null
     */
    private static $apiUrl = null;

    /**
     * @var bool|null
     */
    private static $sandbox = null;

    /**
     * @var int|null
     */
    private static $timeout = null;

    /**
     * @var string|null
     */
    private static $certPath = null;

    /**
     * @var string|null
     */
    private static $certPassword = null;

    /**
     * @var string|null
     */
    private static $folderPath = null;

    /**
     * @var bool
     */
    private static $defIsSandbox = true;

    /**
     * @var int
     */
    private static $defTimeout = 30;

    /**
     * @var string
     */
    private static $defFolderPath = '';

    /**
     * @var string
     */
    private static $sandboxUrl = 'https://cobranca.bradesconetempresa.b.br/ibpjregistrotitulows/registrotitulohomologacao';

    /**
     * @var string
     */
    private static $productionUrl = 'https://cobranca.bradesconetempresa.b.br/ibpjregistrotitulows/registrotitulo';

    /**
     * @var string
     */
    private static $sdkVersion = "1.6.0";

    /**
     * @return bool
     */
    public static function isSandbox(): bool
    {
        if (static::$sandbox === null) {
            static::setIsSandbox();
        }

        return static::$sandbox;
    }

    /**
     * @return string
     */
    public static function getApiUrl(): string
    {
        if (static::$apiUrl === null) {
            static::setApiUrl();
        }

        return static::$apiUrl;
    }

    /**
     * @return int
     */
    public static function getTimeout(): int
    {
        if (is_null(static::$timeout)) {
            static::setTimeout();
        }

        return static::$timeout;
    }

    /**
     * @return string
     * @throws BradescoParameterException
     */
    public static function getCertPath(): string
    {
        if (is_null(static::$certPath)) {
            static::setCertPath();
        }

        return static::$certPath;
    }

    /**
     * @return string
     */
    public static function getFolderPath(): string
    {
        if (is_null(static::$folderPath)) {
            static::setFolderPath();
        }

        return static::$folderPath;
    }

    /**
     * @return string
     * @throws BradescoParameterException
     */
    public static function getCertPassword(): string
    {
        if (is_null(static::$certPassword)) {
            static::setCertPassword();
        }

        return static::$certPassword;
    }

    /**
     * @return string
     */
    public static function getSdkVersion(): string
    {
        return static::$sdkVersion;
    }

    /**
     * @param array $params
     * @throws BradescoParameterException
     */
    public static function setParams(array $params)
    {
        if (isset($params[static::SANDBOX])) {
            static::setIsSandbox($params[static::SANDBOX]);
        }

        if (isset($params[static::TIMEOUT])) {
            static::setTimeout($params[static::TIMEOUT]);
        }

        if (isset($params[static::CERT_PATH])) {
            static::setCertPath($params[static::CERT_PATH]);
        }

        if (isset($params[static::CERT_PASSWORD])) {
            static::setCertPassword($params[static::CERT_PASSWORD]);
        }

        if (isset($params[static::FOLDER_PATH])) {
            static::setFolderPath($params[static::FOLDER_PATH]);
        }
    }

    /**
     * @param bool|null $enable
     */
    public static function setIsSandbox(bool $enable = null): void
    {
        $envValue = getenv(static::SANDBOX);

        if ($envValue === false) {
            $envValue = null;
        }

        if (is_string($envValue)) {
            $envValue = !(strtolower($envValue) === 'false');
        }

        $fallback = !is_null($envValue)
            ? $envValue
            : static::$defIsSandbox;

        static::$sandbox = !is_null($enable)
            ? $enable
            : $fallback;
    }

    /**
     * @param string|null $url
     */
    public static function setApiUrl(string $url = null): void
    {
        static::$apiUrl = !empty($url)
            ? $url
            : (static::isSandbox() ? static::$sandboxUrl : static::$productionUrl);
    }

    /**
     * @param int|null $seconds
     */
    public static function setTimeout(int $seconds = null): void
    {
        $fallback = is_numeric(getenv(static::TIMEOUT))
            ? (int) getenv(static::TIMEOUT)
            : static::$defTimeout;

        static::$timeout = is_numeric($seconds)
            ? $seconds
            : $fallback;
    }

    /**
     * @param string|null $path
     * @throws BradescoParameterException
     */
    public static function setCertPath(string $path = null): void
    {
        $envValue = getenv(static::CERT_PATH);

        $fallback = $envValue !== false && !is_null($envValue)
            ? $envValue
            : null;

        static::$certPath = !is_null($path)
            ? $path
            : $fallback;

        if (is_null(static::$certPath)) {
            throw new BradescoParameterException("Missing required parameter 'CERT_PATH'");
        }
    }

    /**
     * @param string|null $path
     */
    public static function setFolderPath(string $path = null): void
    {
        $envValue = getenv(static::FOLDER_PATH);

        $fallback = $envValue !== false && !is_null($envValue)
            ? $envValue
            : static::$defFolderPath;

        static::$folderPath = !is_null($path)
            ? $path
            : $fallback;
    }

    /**
     * @param string|null $password
     * @throws BradescoParameterException
     */
    public static function setCertPassword(string $password = null): void
    {
        $envValue = getenv(static::CERT_PASSWORD);

        $fallback = $envValue !== false && !is_null($envValue)
            ? $envValue
            : null;

        static::$certPassword = !is_null($password)
            ? $password
            : $fallback;

        if (is_null(static::$certPassword)) {
            throw new BradescoParameterException("Missing required parameter 'CERT_PASSWORD'");
        }
    }
}
