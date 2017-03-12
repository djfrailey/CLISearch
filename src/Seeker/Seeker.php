<?php

declare(strict_types=1);

namespace David\Seeker;

abstract class Seeker
{
    public function seek(string $term, int $page = 0) : string
    {
        $endpoint = $this->getSeekEndpoint($term, $page);

        $options = [
            CURLOPT_FOLLOWLOCATION => 1,
            CURLOPT_MAXREDIRS => 2,
            CURLOPT_RETURNTRANSFER => 1
        ];
        
        $curl = curl_init($endpoint);
        curl_setopt_array($curl, $options);
        $response = curl_exec($curl);
        $info = curl_getinfo($curl);
        curl_close($curl);

        return $response;
    }

    abstract protected function getSeekEndpoint(string $term, int $page = 0) : string;
}
