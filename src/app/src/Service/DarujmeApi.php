<?php

namespace App\Service;

use DateTimeImmutable;
use Exception;

readonly class DarujmeApi
{
    const string BASE_URL = 'https://api.darujme.sk';

    public function __construct(
        private string $username,
        private string $password,
        private string $key,
        private string $secret,
        private string $organisationId
    )
    {
    }

    /**
     * @param int $page
     * @param int $limit
     * @param string|null $campaignId
     * @param string|null $status
     * @param DateTimeImmutable|null $createdGte
     * @param DateTimeImmutable|null $updatedGte
     * @return array
     * @throws Exception
     */
    public function payments(
        int                    $page = 1,
        int                    $limit = 100,
        null|string            $campaignId = null,
        null|string            $status = null,
        null|DateTimeImmutable $createdGte = null,
        null|DateTimeImmutable $updatedGte = null
    ): array
    {
        return $this->authRequest('/v1/payments/', parameters: $this->_([
            'status' => $status,
            'limit' => $limit,
            'page' => $page,
            'campaign_id' => $campaignId,
            'created_gte' => $createdGte?->format("Y-m-d\TH:i:s\Z"),
            'updated_gte' => $updatedGte?->format("Y-m-d\TH:i:s\Z"),
        ]));

    }

    /**
     * @param string $campaignId
     * @return array|null
     * @throws Exception
     */
    public function campaign(
        string $campaignId,
    ): array|null
    {
        return $this->authRequest("/v1/campaigns/$campaignId/")['response'] ?? null;

    }

    /**
     * @param string $campaignId
     * @return array|null
     * @throws Exception
     */
    public function publicCampaign(
        string $campaignId,
    ): array|null
    {
        return $this->request("/v1/public/$this->organisationId/campaigns/$campaignId/")['response'] ?? null;

    }

    /**
     * @param int $page
     * @param int $limit
     * @return array
     * @throws Exception
     */
    public function campaigns(
        int $page = 1,
        int $limit = 100,

    ): array
    {
        $response = $this->authRequest('/v1/campaigns/', parameters: [
            'limit' => $limit,
        ]);

        $result = [];

        foreach ($response['items'] ?? [] as $item) {
            $result[] = $item;
        }

        if (($response['metadata']['pages'] ?? 0) > $page) {
            $result = [...$result, ...$this->campaigns($page + 1, $limit)];
        }

        return $result;
    }

    /**
     * @param string $login
     * @param string $password
     * @return string
     * @throws Exception
     */
    private function auth(
        string $login,
        string $password
    ): string
    {
        $response = $this->request('/v1/tokens/', 'POST', null, [
            'username' => $login,
            'password' => $password,
        ]);

        if (isset($response['error'])) {
            throw new Exception($response['error']['message']);
        }

        return $response['response']['token'];
    }

    private function _(array $params): array
    {
        return array_filter($params, fn($param) => null !== $param);
    }

    /**
     * @param string $path
     * @param string $method
     * @param array|null $data
     * @param array $parameters
     * @return array
     * @throws Exception
     */
    private function authRequest(
        string     $path,
        string     $method = 'GET',
        array|null $data = null,
        array      $parameters = []
    ): array
    {
        return $this->request($path, $method, $this->auth($this->username, $this->password), $data, $parameters);
    }

    /**
     * @param string $path
     * @param string $method
     * @param string|null $token
     * @param array|null $data
     * @param array $parameters
     * @return array
     * @throws Exception
     */
    private function request(
        string      $path,
        string      $method = 'GET',
        string|null $token = null,
        array|null  $data = null,
        array       $parameters = []
    ): array
    {
        $url = self::BASE_URL . $path;

        $ch = curl_init($url . '?' . http_build_query($parameters));
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

        if (null === $data) {
            $payload = '';
        } else {
            $payload = json_encode($data);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
        }

        $headers = [
            'Content-Type: application/json',
            'X-ApiKey: ' . $this->key,
            'X-Organisation: ' . $this->organisationId,
            'X-Signature: ' . $this->signature($payload, $path),
        ];

        if (null !== $token) {
            $headers[] = 'Authorization: TOKEN ' . $token;
        }

        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        $response = curl_exec($ch);

        if (!json_validate($response)) {
            throw new Exception('Invalid JSON response');
        }

        return json_decode($response, true);
    }

    private function signature(
        string $payload,
        string $url
    ): string
    {
        return hash_hmac('sha256', "$payload:$url", $this->secret);
    }
}
