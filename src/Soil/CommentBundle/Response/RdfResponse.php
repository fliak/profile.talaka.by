<?php
/**
 * Created by PhpStorm.
 * User: fliak
 * Date: 18.2.15
 * Time: 22.55
 */

namespace Soil\CommentBundle\Response;



use EasyRdf\Graph;
use Symfony\Component\HttpFoundation\Response;

class RdfResponse extends Response {

    protected static $contentTypeMap = [
        'text/plain'               => 'ntriples',
        'text/ntriples'            => 'ntriples',
        'application/n-triples'    => 'ntriples',
        'application/x-ntriples'   => 'ntriples',
        'text/n3'                  => 'ntriples',

        'application/turtle'       => 'turtle',
        'application/x-turtle'     => 'turtle',
        'text/turtle'              => 'turtle',


        'application/rdf+xml'      => 'rdfxml',
        'application/xml'          => 'rdfxml',
        'application/xhtml+xml'    => 'rdfxml',

        'application/json'         => 'json',
        'application/rdf+json'     => 'json',
        'text/json'                => 'json',
//        'application/ld+json'     => 'jsonld', //didn't support
    ];

    protected $outputType = 'ntriples';

    /**
     * @var Graph
     */
    protected $graph;

    public static function selectAcceptable($acceptTypes, $default = 'text/n3')  {
        $acceptVariants = array_flip($acceptTypes);

        $possible = array_intersect_key($acceptVariants, self::$contentTypeMap);

        return $possible ? key($possible) : $default;
    }

    public function __construct(Graph $graph = null, $status = 200, $contentType = 'text/n3')    {
        parent::__construct('', $status, [
            'Content-Type' => $contentType
        ]);

        $this->outputType = self::$contentTypeMap[$contentType];

        if ($graph) $this->setGraph($graph);
    }


    public function setGraph(Graph $graph)  {
        $this->graph = $graph;
        $this->update();
    }

    protected function update() {
        $content = $this->graph->serialise($this->outputType);

        $this->setContent($content);
    }



} 