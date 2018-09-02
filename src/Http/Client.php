<?php
namespace BradescoApi\Http;

use GuzzleHttp\Client as Guzzle;
use GuzzleHttp\TransferStats;
use BradescoApi\Exceptions\BradescoParameterException;

class Client extends Guzzle
{
    protected $fullUrl;
    protected $certKey;
    protected $privateKey;

    public function __construct(array $config = [])
    {
        $this->setCertKeys();

        $this->setConfig($config);

        parent::__construct($config);
    }

    public function setConfig(array &$config)
    {
        $sdkVersion = Bradesco::getSdkVersion();
        $host       = $_SERVER['HTTP_HOST'] ?? '';
        $url        = &$this->fullUrl;

        $config = array_merge([
            'verify'        => false,
            'base_uri'      => Bradesco::getApiUrl(),
            'timeout'       => Bradesco::getTimeout(),
            'on_stats'      => function (TransferStats $stats) use (&$url) {
                $url = $stats->getEffectiveUri();
            },
            'headers'       => [
                'Content-Type'      => 'application/json',
                'User-Agent'        => "Bradesco-API-PHP/{$sdkVersion};{$host}"
            ]
        ], $config);

        return $config;
    }

    public function setCertKeys()
    {
        $certPassword = Bradesco::getCertPassword();
        $certPath     = Bradesco::getCertPath();

        if (!file_exists($certPath)) {
            throw new BradescoParameterException('Certificate file .pfx not found');
        }

        $certFile = file_get_contents($certPath);

        if (!openssl_pkcs12_read($certFile, $result, $certPassword)) {
            throw new BradescoParameterException('Unable to read certificate file .pfx. Please check the certificate password.');
        }

        $this->certKey    = openssl_x509_read($result['cert']);
        $this->privateKey = openssl_pkey_get_private($result['pkey'], $certPassword);
    }

    public function getFullUrl()
    {
        return $this->fullUrl;
    }

    public function getCertKey()
    {
        return $this->certKey;
    }

    public function getPrivateKey()
    {
        return $this->privateKey;
    }

    public function getFolderPath()
    {
        return Bradesco::getFolderPath();
    }
}
