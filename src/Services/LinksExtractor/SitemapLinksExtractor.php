<?php

declare(strict_types=1);

namespace IContent\Services\LinksExtractor;

use Exception;
use Illuminate\Support\Str;
use IContent\Services\Network\NetworkService;
use Illuminate\Support\Collection;

class SitemapLinksExtractor implements LinksExtractorInterface
{
    /**
     * @var NetworkService
     */
    private $network;
    
    /**
     * SitemapLinksExtractor constructor.
     *
     * @param NetworkService $network
     */
    public function __construct(NetworkService $network)
    {
        $this->network = $network;
    }
    
    /**
     * Extract links from origin and return collection
     *
     * @param string $url
     *
     * @return Collection
     */
    public function extractLinksFromOrigin(string $url): Collection
    {
        $links = collect([]);
        
        try
        {
            $content = $this->network->get($url);
        } catch (Exception $e) {
            return $links;
        }
        
        if (Str::endsWith($url, 'xml.gz')) {
            $content = gzdecode($content);
        }
        
        $xml = @simplexml_load_string($content);
        
        if ($xml === false) {
            return $links;
        }
        
        foreach ($xml as $link) {
            $loc = (string)$link->loc;
            
            if (Str::endsWith($loc, ['.xml', 'xml.gz'])) {
                $links = $links->merge($this->extractLinksFromOrigin($loc));
                continue;
            }
        
            $links[] = $loc;
        }
    
        return $links;
    }
}