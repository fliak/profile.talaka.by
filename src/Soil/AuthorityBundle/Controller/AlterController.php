<?php
/**
 * Created by PhpStorm.
 * User: fliak
 * Date: 23.6.15
 * Time: 20.54
 */

namespace Soil\AuthorityBundle\Controller;


use EasyRdf\RdfNamespace;
use Monolog\Logger;
use Soil\AuthorityBundle\Entity\VoteAggregatorInterface;
use Soil\AuthorityBundle\Rules\VoteRuleException;
use Soil\AuthorityBundle\Rules\VoteRuleInterface;
use Soil\CommentBundle\Controller\Exception\IsNotValidException;
use Soil\CommentBundle\Entity\Author;
use Soil\CommentBundle\Entity\Comment;
use Soil\CommentBundle\Service\AuthorService;
use Soil\CommentBundle\Service\CommentService;
use Soil\CommentBundle\Service\DeURInator;
use Soil\CommentBundle\Service\EntityService;
use Soil\CommentBundle\Service\URInator;
use Soil\AuthorityBundle\Service\VoteService;
use Soilby\EventComponent\Service\EventLogger;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class AlterController {

    /**
     * @var VoteService
     */
    protected $voteService;

    /**
     * @var DeURInator
     */
    protected $deurinator;



    /**
     * @var AuthorService
     */
    protected $authorService;

    /**
     * @var EntityService
     */
    protected $entityService;

    /**
     * @var CommentService
     */
    protected $commentService;



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


    /**
     * @var VoteRuleInterface[]
     */
    protected $rules = [];



    public function __construct($voteService, $deurinator)   {
        $this->voteService = $voteService;
        $this->deurinator = $deurinator;
    }

    public function alterAction(Request $request)   {
        try {
            $content = $request->getContent();

            $data = json_decode($content, true);

            if (!$data) throw new \Exception("Request malformed");

            $vote = $this->voteService->factory();


            $this->voteService->hydrate($vote, $data);

            $result = $this->voteService->isValid($vote);

            if (!$result) {
                throw new IsNotValidException(
                    $this->voteService->getLastValidatorViolations(), 'Request malformed');
            }

            //read subject and object agent URI's
            $voteSubjectURI = $vote->getVoterURI();


            //instantiate subject agent
            $voteSubject = $this->authorService->getByURI($voteSubjectURI, true);
            $vote->setVoter($voteSubject);

            $voteObjectURI = $vote->getObjectURI();

            $voteObject = null;

            $parsed = $this->deurinator->parseUri($voteObjectURI);

            switch ($parsed['class'])   {
                case Comment::class:
                    $voteObject = $this->commentService->getById($parsed['id']);
                    if (!$voteObject)   {
                        throw new \Exception("Provided vote object missing");
                    }
                    $voteObjectAgent = $voteObject->getAuthor();

                    break;

                case Author::class:
                    $voteObjectAgent = $this->authorService->getByURI($voteObjectURI, true);
                    break;

                default:
                    throw new \Exception("Voting for entity of this type isn't supported");
            }


            foreach ($this->rules as $rule) {
                $rule->check($voteSubject, $voteObjectAgent, $voteObject);
            }

            foreach ($this->rules as $rule) {
                $rule->hit($voteSubject, $voteObjectAgent, $voteObject);
            }


            //complete vote db record
            $vote->setAgent($voteObjectAgent);
            $vote->setAgentURI($voteObjectAgent->getAuthorURI());


            if ($voteObject) {
                $vote->setObject($voteObject);

                $voteObject->getVotes()->addVote($vote->getVote());
                $voteObject->getVotes()->getHistory()->add($vote);
            }
            else    {
                $vote->setObject($voteObjectAgent);
            }

            $voteObjectAgent->getVotes()->addVote($vote->getVote());
            $voteObjectAgent->getVotes()->getHistory()->add($vote);



            $this->voteService->persist($vote);
            $this->voteService->getDM()->flush();

            $debugMessage = null;

            try {
                $this->eventLogger->raiseVote(
                    $vote,
                    $vote->getVoterURI(),
                    $vote->getAgentURI(),
                    $vote->getVote(),
                    $vote->getObjectURI()
                );
                $this->eventLogger->flush();
            }
            catch(\Exception $e)    {
                $this->logger->addCritical('Problem with event logging:');
                $this->logger->addCritical((string) $e);

                $debugMessage = 'Problem with event logging!';
            }

            return $this->answerJSON([
                'success' => true,
                'id' => $vote->getId(),
                'debug' => $debugMessage
            ]);

        }
        catch (IsNotValidException $e)   {
            return $this->answerJSON([
                'success' => false,
                'message' => $e->getViolationsAsArray()
            ], 500);
        }
        catch (VoteRuleException $e) {
            return $this->answerJSON([
                'success' => false,
                'message' => $e->getMessage(),
                'error_code' => $e->getErrorCode(),
                'params' => $e->getParams()
            ], 500);
        }
        catch (\Exception $e)   {
            return $this->answerJSON([
                'success' => false,
                'message' => [$e->getMessage()],
                'trace' => $e->getTraceAsString()
            ], 500);
        }

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
    public function setUrinator($urinator)
    {
        $this->urinator = $urinator;
    }


    protected function answerJSON($data, $httpCode = 200)    {
        $response = new JsonResponse($data);
        $response->setStatusCode($httpCode);
        $response->headers->add(['Access-Control-Allow-Origin' => '*']);


        return $response;
    }

    /**
     * @param AuthorService $authorService
     */
    public function setAuthorService($authorService)
    {
        $this->authorService = $authorService;
    }

    /**
     * @param AuthorService $commentService
     */
    public function setCommentService($commentService)
    {
        $this->commentService = $commentService;
    }

    /**
     * @param EntityService $entityService
     */
    public function setEntityService($entityService)
    {
        $this->entityService = $entityService;
    }


    public function addRule($rule)  {
        $this->rules[] = $rule;
    }

}