<?php

namespace BradescoApi\Http;

use GuzzleHttp\Client as Guzzle;
use GuzzleHttp\TransferStats;
use BradescoApi\Exceptions\BradescoParameterException;

class Client extends Guzzle
{
    /**
     * @var string
     */
    protected $fullUrl;

    /**
     * @var resource|string
     */
    protected $certKey;

    /**
     * @var resource|string
     */
    protected $privateKey;

    /**
     * @param array $config
     * @throws BradescoParameterException
     */
    public function __construct(array $config = [])
    {
        $this->setCertKeys();

        $this->setConfig($config);

        parent::__construct($config);
    }

    /**
     * @param array $config
     * @return array
     */
    public function setConfig(array &$config): array
    {
        $sdkVersion = Bradesco::getSdkVersion();
        $host = $_SERVER['HTTP_HOST'] ?? '';
        $url = &$this->fullUrl;

        $config = array_merge([
            'verify' => false,
            'base_uri' => Bradesco::getApiUrl(),
            'timeout' => Bradesco::getTimeout(),
            'on_stats' => function (TransferStats $stats) use (&$url) {
                $url = $stats->getEffectiveUri();
            },
            'headers' => [
                'Content-Type' => 'application/json',
                'User-Agent' => "Bradesco-API-PHP/{$sdkVersion};{$host}"
            ]
        ], $config);

        return $config;
    }

    /**
     * @throws BradescoParameterException
     */
    public function setCertKeys(): void
    {
        $certPassword = Bradesco::getCertPassword();
        $certPath = Bradesco::getCertPath();

        if (!file_exists($certPath)) {
            throw new BradescoParameterException('Certificate file .pfx not found');
        }

        $certFile = file_get_contents($certPath);

        if (!openssl_pkcs12_read($certFile, $result, $certPassword)) {
            throw new BradescoParameterException('Unable to read certificate file .pfx (' . openssl_error_string() . ')');
        }

        $this->certKey = openssl_x509_read($result['cert']);
        $this->privateKey = openssl_pkey_get_private($result['pkey'], $certPassword);
    }

    /**
     * @return string
     */
    public function getFullUrl(): string
    {
        return $this->fullUrl;
    }

    /**
     * @return resource|string
     */
    public function getCertKey()
    {
        return $this->certKey;
    }

    /**
     * @return resource|string
     */
    public function getPrivateKey()
    {
        return $this->privateKey;
    }

    /**
     * @return string
     */
    public function getFolderPath(): string
    {
        return Bradesco::getFolderPath();
    }
}
