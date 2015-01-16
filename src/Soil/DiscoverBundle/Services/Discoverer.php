<?php
/**
 * Created by PhpStorm.
 * User: fliak
 * Date: 10.1.15
 * Time: 23.34
 */

namespace Soil\DiscoverBundle\Services;


use Opengraph\Reader;

class Discoverer {

    protected $uri;
    protected $content;

    protected $metaData;

    protected $downloader;
    protected $reader;

    public function __construct(Downloader $downloader, Reader $reader)   {
        $this->downloader = $downloader;
        $this->reader = $reader;
    }

    public function discover($uri)  {
        $this->uri = $uri;

        $this->fields = [];

        $html = $this->downloader->getDocument($uri);
        $this->content = $html;
        $this->reader->parse($html);
        $this->metaData = $this->reader->getArrayCopy();
    }

    public function __call($name, $args)    {
        if (strpos($name, 'get') === 0) {
            $name = substr($name, 3);
            $propCall = preg_replace('/(?!^)[A-Z]{2,}(?=[A-Z][a-z])|[A-Z][a-z]/', ':$0', $name);

            $ogProp = 'og' . strtolower($propCall);
            return $this->reader->getMeta($ogProp);
        }
    }

    public function getMetaData()   {
        return $this->metaData;
    }

    public function getURI()    {
        return $this->uri;
    }

    public function getContent()    {
        return $this->content;
    }

} 