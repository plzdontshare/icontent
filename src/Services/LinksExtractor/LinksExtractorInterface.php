<?php

namespace IContent\Services\LinksExtractor;

use App\Models\Origin;
use Illuminate\Support\Collection;
use SDL\Models\Site;
use stdClass;

interface LinksExtractorInterface
{
    /**
     * Extract links from origin and return collection
     *
     * @param string $url
     *
     * @return Collection
     */
    public function extractLinksFromOrigin(string $url): Collection;
}