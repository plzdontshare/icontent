<?php

namespace IContent\Services\ContentExtractor;

use andreskrey\Readability\Configuration;
use andreskrey\Readability\Readability;
use Illuminate\Support\Str;
use IContent\Services\Network\NetworkService;
use stdClass;

class ContentExtractorService
{
    /**
     * @var NetworkService
     */
    private $network;
    
    /**
     * ContentExtractorService constructor.
     *
     * @param NetworkService $network
     */
    public function __construct(NetworkService $network)
    {
        $this->network = $network;
    }
    
    /**
     * @param string $url
     *
     * @return stdClass
     * @throws \andreskrey\Readability\ParseException
     */
    public function extract(string $url): \stdClass
    {
        $html = $this->network->get($url);
        
        $readability = $this->initReadability($url, $html);
        
        $title = (string)$readability->getTitle();
        $content = (string)$readability->getContent();
        $content = $this->sanitizeContent($url, $content);
        
        return (object)compact('title', 'content');
    }
    
    /**
     * @param string $url
     * @param string $html
     *
     * @return Readability
     * @throws \andreskrey\Readability\ParseException
     */
    private function initReadability(string $url, string $html): Readability
    {
        $conf = new Configuration;
        $conf->setFixRelativeURLs(true);
        $conf->setOriginalURL($url);
        $conf->setSummonCthulhu(true);
        $readability = new Readability($conf);
        
        $readability->parse($html);
        
        return $readability;
    }
    
    /**
     * Remove junk from content
     *
     * @param string $base_url
     * @param string $content
     * @param array $cleanup_rules
     *
     * @return string
     */
    private function sanitizeContent(string $base_url, string $content, array $cleanup_rules = []): string
    {
        // Fix encoding
        if (($encoding = mb_detect_encoding($content)) !== 'UTF-8') {
            $content = mb_convert_encoding($content, 'UTF-8', $encoding);
        }
        
        foreach ($cleanup_rules as $rule => $replacement) {
            $content = preg_replace("~{$rule}~Uuis", $replacement, $content);
        }
        
        // Remove links
        $content = preg_replace("#<a.*>(.*)</a>#Uuis", "$1", $content);
        // Remove tag attributes (class/id/etc)
        $content = preg_replace("#<(?!img)(\w+)\s(.*)>#Uuis", "<$1>", $content);
        // Remove closing img tag
        $content = preg_replace("#</img>#Uuis", "", $content);
        // Remove attributes from img tag
        $content = preg_replace("#<img.*src=['\"](.*)['\"].*>#Uuis", "<img src=\"$1\">", $content);
        
        return $content;
    }
}