<?php

declare(strict_types=1);

namespace David\Seeker;

class GoogleSeeker extends Seeker
{
    public function getSeekEndpoint(string $term, int $page = 0) : string
    {
        $params = [
            'q' => $term,
            'start' => $page
        ];

        $queryString = http_build_query($params);
        $endpoint = "http://www.google.com/search?$queryString";

        return $endpoint;
    }
}
