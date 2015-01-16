<?php
/**
 * Created by PhpStorm.
 * User: fliak
 * Date: 10.1.15
 * Time: 22.52
 */

namespace Soil\DiscoverBundle\Services;


use Buzz\Browser;
use Soil\DiscoverBundle\Services\Exception\DownloadException;

class Downloader {

    /**
     * @var Browser
     */
    protected $client;

    public function __construct($client)    {
        $this->client = $client;
    }

    /**
     * @param $uri
     * @return \Buzz\Message\Response
     */
    protected function get($uri)    {
        return $this->client->get($uri);
    }

    public function getDocument($uri)   {
        $response = $this->client->get($uri);

        $statusCode = $response->getStatusCode();
        if ($statusCode === 200) {
            return $response->getContent();
        }

        throw new DownloadException("HTTP $statusCode");
    }
} 