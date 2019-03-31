<?php

declare(strict_types=1);

namespace IContent\Services\LinksExtractor;

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
        $content = $this->network->get($url);
        
        $xml = simplexml_load_string($content);
    
        $links = collect([]);
        foreach ($xml as $link) {
            $loc = (string)$link->loc;
            
            if (Str::endsWith($loc, '.xml')) {
                $links = $links->merge($this->extractLinksFromOrigin($loc));
                continue;
            }
        
            $links[] = $loc;
        }
    
        return $links;
    }
    
    /**
     * @param string $userAgent
     *
     * @return mixed
     */
    public function setUserAgent(string $userAgent)
    {
        $this->network->setUserAgent($userAgent);
    }
}