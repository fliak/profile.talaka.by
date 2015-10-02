<?php
/**
 * Created by PhpStorm.
 * User: fliak
 * Date: 23.6.15
 * Time: 20.54
 */

namespace Soil\AuthorityBundle\Controller;


use EasyRdf\Graph;
use EasyRdf\Literal;
use EasyRdf\RdfNamespace;
use EasyRdf\Resource;
use Monolog\Logger;
use Soil\CommentBundle\Controller\Exception\IsNotValidException;
use Soil\CommentBundle\Response\RdfResponse;
use Soil\CommentBundle\Service\AuthorService;
use Soil\CommentBundle\Service\URInator;
use Soil\AuthorityBundle\Service\VoteService;
use Soilby\EventComponent\Service\EventLogger;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class IndexController {

    /**
     * @var VoteService
     */
    protected $voteService;

    /**
     * @var AuthorService
     */
    protected $authorService;



    /**
     * @var Logger
     */
    protected $logger;

    /**
     * @var EventLogger
     */
    protected $eventLogger;


    /**
     * @var URInator
     */
    protected $urinator;




    public function __construct($voteService, $authorService)   {
        $this->voteService = $voteService;
        $this->authorService = $authorService;


    }

    public function getAgentStatAction($uri, Request $request)    {
        try {
            if ($request->isMethod(Request::METHOD_POST)) {
                $uriSet = json_decode($request->getContent(), true);

                if (!$uriSet || !is_array($uriSet)) throw new \Exception('Request malformed');
            } else {
                $uriSet = [$uri];
            }

            $response = [];

            foreach ($uriSet as $anId => $uri) {
                if (!is_string($uri)) throw new \Exception('Request malformed. Please specify array of URI.');
                $author = $this->authorService->getByURI($uri);

                if ($author) {
                    $voteValue = $author->getVotes()->getVoteValue();
                    $commentsCount = $author->getCommentsCount();
                } else {
                    $voteValue = 0;
                    $commentsCount = 0;
                }


                $response[$anId] = [
                    'authority' => $voteValue,
                    'comments_count' => $commentsCount
                ];
            }

            $response = new JsonResponse($response);

        }
        catch(\Exception $e)    {
                $response = new JsonResponse([
                    'success' => false,
                    'message' => $e->getMessage()
                ]);
                $response->setStatusCode(500);
            }

        return $response;

    }


    public function getAction($id, Request $request)  {
        try {
            $contentType = RdfResponse::selectAcceptable($request->getAcceptableContentTypes());


            $vote = $this->voteService->getById($id);
            if (!$vote) {
                throw new NotFoundHttpException('Vote is missing');
            }

            $publicRepresentation = $this->voteService->getPublicRepresentation($vote);

            if ($contentType === 'application/json') {
                $response = new JsonResponse(array_merge($publicRepresentation, ['success' => true]));
            }
            else {

                RdfNamespace::set('schema', 'http://schema.org/');
                RdfNamespace::set('foaf', 'http://xmlns.com/foaf/0.1/');
                RdfNamespace::set('tal', 'http://semantic.talaka.by/ns/talaka.owl#');

                $graph = new Graph();

                $voteURI = $this->urinator->generateURI($vote);

                $resource = $graph->resource($voteURI, 'schema:Review');
                $resource->set('tal:voteId', new Literal($id, null, 'xsd:string'));

                $resource->addResource('schema:itemReviewed', $vote->getAgentURI());

                if ($vote->getAgentURI() !== $vote->getObjectURI()) {
                    $resource->addResource('schema:itemReviewed', $vote->getObjectURI());
                }

                $resource->addLiteral('schema:reviewBody', $vote->getMessage());

                $ratingNode = $graph->newBNode('schema:Rating');
                $ratingNode->addLiteral('schema:bestRating', 1);
                $ratingNode->addLiteral('schema:worstRating', -1);
                $ratingNode->addLiteral('schema:ratingValue', $vote->getVote());

                $resource->addResource('schema:reviewRating', $ratingNode);

                $resource->addResource('schema:author', $vote->getVoterURI());

                $voter = $graph->resource($vote->getVoterURI(), 'foaf:Person');
                $voter->addLiteral('foaf:firstName', $vote->getAgent()->getName());
                $voter->addLiteral('foaf:lastName', $vote->getAgent()->getSurname());
                $voter->addLiteral('foaf:img', $vote->getAgent()->getAvatarURL());

                $resource->addLiteral('schema:dateCreated', new Literal\DateTime($vote->getCreationDate()));

                $response = new RdfResponse($graph, 200, $contentType);
            }

        }
        catch(NotFoundHttpException $e)    {
            $response = new JsonResponse([
                'success' => false,
                'message' => $e->getMessage()
            ]);
            $response->setStatusCode(404);
        }
        catch(\Exception $e)    {
            $response = new JsonResponse([
                'success' => false,
                'message' => $e->getMessage()
            ]);
            $response->setStatusCode(500);
        }

        return $response;
    }

    /**
     * @param EventLogger $eventLogger
     */
    public function setEventLogger($eventLogger)
    {
        $this->eventLogger = $eventLogger;
    }

    /**
     * @param Logger $logger
     */
    public function setLogger($logger)
    {
        $this->logger = $logger;
    }

    /**
     * @param URInator $urinator
     */
    public function setURInator($urinator)
    {
        $this->urinator = $urinator;
    }


    protected function answerJSON($data, $httpCode = 200)    {
        $response = new JsonResponse($data);
        $response->setStatusCode($httpCode);
        $response->headers->add(['Access-Control-Allow-Origin' => '*']);


        return $response;
    }
} 