<?php

declare(strict_types=1);

namespace IContent\Services\LinksExtractor;

use IContent\Services\Network\NetworkService;

class LinksExtractorFactory
{
    /**
     * @param string $type
     * @param NetworkService $networkService
     *
     * @return LinksExtractorInterface
     */
    public static function make(string $type, NetworkService $networkService): LinksExtractorInterface
    {
        $network = $networkService;
        
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