<?php

declare(strict_types=1);

namespace David\Seeker;

abstract class Seeker
{
    public function seek(string $term, int $page = 0) : string
    {
        $endpoint = $this->getSeekEndpoint($term, $page);
        return file_get_contents($endpoint);
    }

    abstract protected function getSeekEndpoint(string $term, int $page = 0) : string;
}
