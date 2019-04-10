<?php

declare(strict_types=1);

namespace IContent\Services\Network;

use GuzzleHttp\Client;

class NetworkService
{
    /**
     * @var Client
     */
    private $client;
    
    /**
     * @var string
     */
    private $userAgent;
    
    /**
     * NetworkService constructor.
     *
     * @param Client $client
     */
    public function __construct(Client $client = null)
    {
        $this->client = $client ?? new Client;
    }
    
    /**
     * @param string $url
     * @param array $headers
     *
     * @return string
     */
    public function get(string $url, array $headers = []): string
    {
        $response = $this->client->get($url, [
            'timeout' => 20,
            'headers' => array_merge([
                'User-Agent' => $this->getUserAgent(),
                'Referer'    => $url,
            ], $headers),
        ]);
        
        return $response->getBody()->getContents();
    }
    
    public function setUserAgent(string $userAgent)
    {
        $this->userAgent = empty($userAgent) ? null : $userAgent;
    }
    
    public function getUserAgent(): string
    {
        return $this->userAgent ?? 'Mozilla/5.0 AppleWebKit/537.36 (KHTML, like Gecko; compatible; Googlebot/2.1; +http://www.google.com/bot.html) Safari/537.36';
    }
}