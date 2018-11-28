<?php
namespace BradescoApi\Http;

use Psr\Http\Message\ResponseInterface;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\RequestException;
use BradescoApi\Exceptions\BradescoApiException;
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
        } catch (RequestException $e) {
            if (!$e->hasResponse()) {
                throw new BradescoRequestException($e->getMessage());
            }

            $response = $e->getResponse();
        }

        return $this->response($response);
    }

    private function response(ResponseInterface $response)
    {
        $content = $response->getBody()->getContents();

        $data = $this->soapToJson($content);

        $this->checkForErrors($response, $data);

        return $data;
    }

    private function soapToJson(string $content)
    {
        $data = "{}";

        if (preg_match('/<return>(\{[^}]+\})<\/return>/smi', $content, $matches)) {
            $data = $matches[1];
        }

        return json_decode($data);
    }

    private function checkForErrors(ResponseInterface $response, \stdClass $data)
    {
        // NOTE: All API errors are received as 200 OK
        // (not in accordance with RESTful specs)
        $this->checkForApiException($data);

        $this->checkForRequestException($response);
    }

    private function checkForApiException(\stdClass $data)
    {
        $code     = (int) ($data->cdErro ?? 0);
        $message  = $data->msgErro ?? 'Undefined error';

        if ($code === 0) return;

        // Bradesco API issue
        // Fixes text of 'CdErro' field, which contains double enconded
        // HTML entities ("&amp;atilde;" rather than "&atilde;")
        $message = html_entity_decode($message);
        $message = html_entity_decode($message);

        throw new BradescoApiException($message, $code);
    }

    private function checkForRequestException(ResponseInterface $response)
    {
        $code           = $response->getStatusCode();
        $message        = $response->getReasonPhrase();
        $statusClass    = (int) ($code / 100);

        if ($statusClass !== 4 && $statusClass !== 5) {
            return;
        }

        throw new BradescoRequestException($message, $code);
    }

    public function encryptBodyData($params)
    {
        // Bradesco API issue
        // Do not escape special chars to Unicode since Bradesco API
        // does not decode them and Bank Slips are issued with
        // strange chars like "Jo\u00e3o"
        $message      = json_encode($params, JSON_UNESCAPED_UNICODE);

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
