<?php
/**
 * Created by PhpStorm.
 * User: fliak
 * Date: 10.1.15
 * Time: 23.16
 */

namespace Soil\DiscoverBundle\Services;


use Symfony\Component\DomCrawler\Crawler;

class Parser {

    /**
     * @var Crawler
     */
    protected $domCrawler;
    protected $content;

    public function __construct($domCrawler)   {
        $this->domCrawler = $domCrawler;
    }

    public function setContent($htmlContent)    {
        $this->domCrawler->clear();
        $this->domCrawler->addContent($htmlContent);
        $this->content = $htmlContent;
    }

    public function parseMeta()    {

        $metaTags = $this->domCrawler->filter('meta');

        $metaData = [];
        foreach ($metaTags as $element) {
            $name = $element->getAttribute('name');
            if ($name) {
                $metaData[$name] = $element->getAttribute('content');
            }
        }

        return $metaData;
    }
}