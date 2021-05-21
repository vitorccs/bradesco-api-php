<?php

namespace BradescoApi\Http;

use GuzzleHttp\Exception\ConnectException;
use Psr\Http\Message\ResponseInterface;
use GuzzleHttp\Exception\RequestException;
use BradescoApi\Exceptions\BradescoApiException;
use BradescoApi\Exceptions\BradescoRequestException;

class Api
{
    /**
     * @var Client
     */
    protected $client;

    /**
     * The success code value for 'cdErro' field
     */
    const SUCCESS_CODE = 0;

    /**
     * The error code value in case Bradesco API returns an empty body
     */
    const EMPTY_BODY_CODE = -100;

    /**
     * Api constructor.
     */
    public function __construct()
    {
        $this->client = new Client();
    }

    /**
     * @param array $params
     * @param string|null $endpoint
     * @return \stdClass
     * @throws BradescoRequestException
     * @throws BradescoApiException
     */
    public function post(array $params = [], string $endpoint = ''): \stdClass
    {
        $options = [
            'body' => $this->encryptBodyData($params)
        ];

        return $this->request('POST', $endpoint, $options);
    }

    /**
     * @param string $method
     * @param string|null $endpoint
     * @param array $options
     * @return \stdClass
     * @throws BradescoApiException
     * @throws BradescoRequestException
     */
    private function request(string $method, string $endpoint = '', array $options = []): \stdClass
    {
        try {
            $response = $this->client->request($method, $endpoint, $options);
        } catch (RequestException $e) {
            if (!$e->hasResponse()) {
                throw new BradescoRequestException($e->getMessage());
            }

            $response = $e->getResponse();
        } catch (ConnectException $e) { // Guzzle >= v7.x
            throw new BradescoRequestException($e->getMessage());
        }

        return $this->response($response);
    }

    /**
     * @param ResponseInterface $response
     * @return \stdClass|null
     * @throws BradescoApiException
     * @throws BradescoRequestException
     */
    private function response(ResponseInterface $response): ?\stdClass
    {
        $content = $response->getBody()->getContents();

        $data = $this->soapToJson($content);

        $this->checkForErrors($response, $data);

        return $data;
    }

    /**
     * @param string $content
     * @return \stdClass|null
     */
    private function soapToJson(string $content): ?\stdClass
    {
        $data = "{}";

        if (preg_match('/<return>(\{[^}]+\})<\/return>/smi', $content, $matches)) {
            $data = $matches[1];
        }

        return json_decode($data);
    }

    /**
     * @param ResponseInterface $response
     * @param \stdClass|null $data
     * @throws BradescoApiException
     * @throws BradescoRequestException
     */
    private function checkForErrors(ResponseInterface $response, ?\stdClass $data = null)
    {
        // NOTE: All API errors are received as 200 OK
        // (not in accordance with RESTful specs)
        $this->checkForApiException($data);

        $this->checkForRequestException($response, $data);
    }

    /**
     * @param \stdClass|null $data
     * @throws BradescoApiException
     */
    private function checkForApiException(?\stdClass $data = null)
    {
        $code = (int)($data->cdErro ?? null);
        $message = $data->msgErro ?? 'Unknown error';

        if ($code === self::SUCCESS_CODE) return;

        // Bradesco API issue
        // Fixes text of 'CdErro' field, which contains double encoded
        // HTML entities ("&amp;atilde;" rather than "&atilde;")
        $message = html_entity_decode($message);
        $message = html_entity_decode($message);

        throw new BradescoApiException($message, $code);
    }

    /**
     * @param ResponseInterface $response
     * @param \stdClass|null $data
     * @throws BradescoRequestException
     */
    private function checkForRequestException(ResponseInterface $response, ?\stdClass $data = null)
    {
        $code = $response->getStatusCode();
        $message = $response->getReasonPhrase();

        $statusClass = (int)($code / 100);
        $isHttpError = $statusClass === 4 || $statusClass === 5;

        if (!$isHttpError && !is_null($data)) return;

        if (is_null($data)) {
            $message = 'Bradesco returned an empty Body';
            $code = self::EMPTY_BODY_CODE;
        }

        throw new BradescoRequestException($message, $code);
    }

    /**
     * @param array $params
     * @return string
     */
    public function encryptBodyData(array $params): string
    {
        // Bradesco API issue
        // Do not escape special chars to Unicode since Bradesco API
        // does not decode them and Bank Slips are issued with
        // strange chars like "Jo\u00e3o"
        $message = json_encode($params, JSON_UNESCAPED_UNICODE);

        $certKey = $this->client->getCertKey();
        $privateKey = $this->client->getPrivateKey();
        $folderPath = $this->client->getFolderPath();

        $msgFile = $folderPath . uniqid('jsonFile', true);
        $signedFile = $folderPath . uniqid('signedFile', true);

        file_put_contents($msgFile, $message);

        openssl_pkcs7_sign(
            $msgFile, $signedFile, $certKey, $privateKey, [], PKCS7_BINARY | PKCS7_TEXT
        );

        $signature = file_get_contents($signedFile);
        $parts = preg_split("#\n\s*\n#Uis", $signature);

        $signedMessageBase64 = $parts[1];

        unlink($msgFile);
        unlink($signedFile);

        return $signedMessageBase64;
    }
}
