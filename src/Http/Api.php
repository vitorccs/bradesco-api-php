<?php
namespace BradescoApi\Http;

use Psr\Http\Message\ResponseInterface;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\RequestException;
use BradescoApi\Exceptions\BradescoClientException;
use BradescoApi\Exceptions\BradescoRequestException;

class Api
{
    protected $client;

    public function __construct()
    {
        $this->client = new Client();
    }

    public function post(array $params = [], string $endpoint = null)
    {
        $options = [
            'body' => $this->encryptBodyData($params)
        ];

        return $this->request('POST', $endpoint, $options);
    }

    private function request(string $method, string $endpoint = null, array $options = [])
    {
        try {
            $response = $this->client->request($method, $endpoint, $options);
        } catch (ClientException $e) {
            $response = $e->getResponse();
        } catch (RequestException $e) {
            $response = $e->getResponse();
        }

        if (!$response) {
            throw new BradescoClientException('Unable to connect to the host');
        }

        return $this->response($response);
    }

    private function response(ResponseInterface $response)
    {
        $content = $response->getBody();

        $data = $this->soapToJson($content);

        $this->checkForErrors($response, $data);

        return $data;
    }

    private function soapToJson(string $content)
    {
        $doc = new \DOMDocument();
        $doc->loadXML($content);

        $data = $doc->getElementsByTagName('return')->item(0)->nodeValue;
        $data = preg_replace('/, }/i', '}', $data);
        $data = json_decode($data);

        return $data;
    }

    private function checkForErrors(ResponseInterface $response, \stdClass $data)
    {
        $code           = $response->getStatusCode();
        $statusClass    = (int) ($code / 100);

        if ($statusClass === 4 || $statusClass === 5) {
            $this->checkForRequestException($data);
            $this->checkForClientException($response);
        }
    }

    private function checkForRequestException(\stdClass $data)
    {
        $code    = $data->cdErro ?? null;
        $reason  = $data->msgErro ?? null;

        if (!$code && !$reason) return;

        $message = "{$code} ($reason)";

        throw new BradescoRequestException($message);
    }

    private function checkForClientException(ResponseInterface $response)
    {
        $code    = $response->getStatusCode();
        $reason  = $response->getReasonPhrase();

        $message = "{$code} ($reason)";

        throw new BradescoClientException($message);
    }

    public function encryptBodyData($params)
    {
        $message      = json_encode($params);
        $certKey      = $this->client->getCertKey();
        $privateKey   = $this->client->getPrivateKey();
        $folderPath   = $this->client->getFolderPath();

        $msgFile      = $folderPath . uniqid('jsonFile', true);
        $signedFile   = $folderPath . uniqid('signedFile', true);

        file_put_contents($msgFile, $message);

        openssl_pkcs7_sign(
            $msgFile, $signedFile, $certKey, $privateKey, [], PKCS7_BINARY | PKCS7_TEXT
        );

        $signature  = file_get_contents($signedFile);
        $parts      = preg_split("#\n\s*\n#Uis", $signature);

        $signedMessageBase64 = $parts[1];

        unlink($msgFile);
        unlink($signedFile);

        return $signedMessageBase64;
    }
}
