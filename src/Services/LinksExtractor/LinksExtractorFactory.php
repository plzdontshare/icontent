<?php

declare(strict_types=1);

namespace IContent\Services\LinksExtractor;

use GuzzleHttp\Client;
use IContent\Services\Network\NetworkService;

class LinksExtractorFactory
{
    /**
     * @param string $type
     *
     * @return LinksExtractorInterface
     */
    public static function make(string $type): LinksExtractorInterface
    {
        $client = new Client;
        $network = new NetworkService($client);
        switch ($type) {
            case 'sitemap':
                $instance = new SitemapLinksExtractor($network);
                break;
            default:
                throw new \RuntimeException("Invalid type for LinksExtractorFactory \"{$type}\"");
        }
        
        return $instance;
    }
}