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
            'headers' => array_merge([
                'User-Agent' => 'Mozilla/5.0 AppleWebKit/537.36 (KHTML, like Gecko; compatible; Googlebot/2.1; +http://www.google.com/bot.html) Safari/537.36',
            ], $headers),
        ]);
        
        return $response->getBody()->getContents();
    }
}