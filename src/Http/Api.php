<?php
namespace BradescoApi\Http;

use Psr\Http\Message\ResponseInterface;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\RequestException;
use BradescoApi\Exceptions\BradescoValidationException;
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
        $code           = $response->getStatusCode();
        $statusClass    = (int) ($code / 100);

        // Not in accordante to REST API specification:
        // Request errors are received as "200 OK" rather than
        // "400 Bad Request" or "422 Unprocessable Entity"
        $this->BradescoValidationException($data);

        if ($statusClass === 4 || $statusClass === 5) {
            $this->checkForRequestException($response);
        }
    }

    private function BradescoValidationException(\stdClass $data)
    {
        $code    = $data->cdErro ?? 0;
        $reason  = $data->msgErro ?? 'Unknown error';

        if (!$code) return;

        // Bradesco API issue
        // Fixes text of 'CdErro' field, which contains double enconded
        // HTML entities ("&amp;atilde;" rather than "&atilde;")
        $reason = html_entity_decode($reason);
        $reason = html_entity_decode($reason);

        $message = "{$reason} ($code)";

        throw new BradescoValidationException($message);
    }

    private function checkForRequestException(ResponseInterface $response)
    {
        $code    = $response->getStatusCode();
        $reason  = $response->getReasonPhrase();

        $message = "{$reason} ($code)";

        throw new BradescoRequestException($message);
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
