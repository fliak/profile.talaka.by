<?php
/**
 * Created by PhpStorm.
 * User: fliak
 * Date: 8.1.15
 * Time: 11.51
 */

namespace Soil\CommentBundle\Controller;


use Doctrine\ODM\MongoDB\DocumentNotFoundException;
use EasyRdf\Graph;
use EasyRdf\Literal;
use EasyRdf\RdfNamespace;
use Soil\CommentBundle\Controller\CORS\CORSTraitForController;
use Soil\CommentBundle\Controller\Exception\IsNotValidException;
use Soil\CommentBundle\Entity\Comment;
use Soil\CommentBundle\Response\RdfResponse;
use Soil\CommentBundle\Service\AuthorService;
use Soil\CommentBundle\Service\CommentService;
use Soil\CommentBundle\Service\EntityService;
use Soil\CommentBundle\Service\Exception;
use Soil\CommentBundle\Service\URInator;
use Soilby\EventComponent\Service\EventLogger;
use Symfony\Bridge\Monolog\Logger;
use Symfony\Component\Form\FormFactory;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class CommentController {

    use CORSTraitForController;

    /**
     * @var CommentService
     */
    protected $commentService;

    /**
     * @var AuthorService
     */
    protected $authorService;

    /**
     * @var EntityService
     */
    protected $entityService;


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
     * @var FormFactory
     */
    protected $formFactory;

    public function setFormFactory(FormFactory $formFactory)    {
        $this->formFactory = $formFactory;
    }

    public function setURInator($urinator)  {
        $this->urinator = $urinator;
    }

    public function __construct(CommentService $commentService, EntityService $entityService, AuthorService $authorService) {
        $this->commentService = $commentService;
        $this->entityService = $entityService;
        $this->authorService = $authorService;
    }




    public function pushAction(Request $request)   {
        try {
            if ($request->isMethod('OPTIONS'))  {
                return $this->corsController->optionsAction();
            }

            $requestContent = $request->getContent();
            $data = json_decode($requestContent, true);
            if (!$data) throw new \Exception('Request malformed');

            $requiredFields = ['author_uri', 'entity_uri', 'message'];
            $missingFields = array_diff($requiredFields, array_keys($data));
            if (count($missingFields) !== 0)  {
                $missingFieldsString = implode(', ', $missingFields);
                throw new \Exception("Fields `$missingFieldsString` is required");
            }


            $comment = $this->commentService->factory();

            $this->commentService->hydrate($comment, $data);
            $comment->setStatus(Comment::COMMENT_STATUS_PUBLIC);

            $result = $this->commentService->isValid($comment);

            if (!$result) {
                throw new IsNotValidException(
                    $this->commentService->getLastValidatorViolations(), 'Request malformed');
            }

            $commentedEntity = $this->entityService->getByURI($comment->getEntityURI(), true);

            $comment->setEntity($commentedEntity); //set entity back
            $commentedEntity->getComments()->add($comment); //make relation to comment
            $commentedEntity->incrementCommentsCount(); //update comment counter
            $commentedEntity->setLastCommentDate($comment->getCreationDate());


            $commentAuthor = $this->authorService->getByURI($comment->getAuthorURI(), true);

            $comment->setAuthor($commentAuthor);
            $commentAuthor->getComments()->add($comment);
            $commentAuthor->incrementCommentsCount();
            $commentAuthor->setLastCommentDate($comment->getCreationDate());

            $commentedEntity->incrementUser($commentAuthor);
            $commentedEntity->setLastCommentAuthor($commentAuthor);


            if (array_key_exists('parent', $data) && !empty($data['parent'])) {
                try {
                    $parentComment = $comment->getParent();
                    $comment->setParent($parentComment);
                    $parentComment->addChildren($comment);
                }
                catch (DocumentNotFoundException $e)    {
                    $parentCommentId = $data['parent'];
                    throw new NotFoundHttpException("Parent comment `$parentCommentId` is missing", $e);
                }
            }
            else    {
                $comment->setParent(null);
            }

            $this->commentService->persist($comment);

            $this->commentService->getDM()->flush();

            $debugMessage = null;

            try {
                $this->eventLogger->raiseComment(
                    $comment,
                    $commentAuthor->getAuthorURI(),
                    $commentedEntity->getEntityURI(),
                    $comment->getParent()
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
                'id' => $comment->getId(),
                'model' => $this->commentService->getPublicRepresentation($comment),
                'debug' => $debugMessage
            ]);

        }
        catch (IsNotValidException $e)   {
            return $this->answerJSON([
                'success' => false,
                'message' => $e->getViolationsAsArray()
            ], 500);
        }
        catch (\Exception $e)   {
            return $this->answerJSON([
                'success' => false,
                'message' => $e->getMessage(),
                'debug' => (string)$e
            ], 500);
        }
    }

    protected function answerJSON($data, $httpCode = 200)    {
        $response = new JsonResponse($data);
        $response->setStatusCode($httpCode);
        $response->headers->add(['Access-Control-Allow-Origin' => '*']);


        return $response;
    }

    public function removeAction(Request $request)   {
        try {
            if ($request->isMethod('OPTIONS'))  {
                return $this->corsController->optionsAction();
            }

            $id = $request->get('id');

            $body = $request->getContent();
            $data = json_decode($body, true);

            $comment = $this->commentService->getById($id);

            if ($comment->getStatus() === Comment::COMMENT_STATUS_REMOVED)  {
                throw new NotFoundHttpException('Comment is not exists');
            }
            $this->commentService->remove($comment, $data);

            $this->commentService->getDM()->flush();

            return $this->answerJSON([
                'success' => true
            ]);
        }
        catch(\Exception $e)    {
            return $this->answerJSON([
                'success' => false,
                'message' => $e->getMessage(),
                'debug' => (string) $e
            ], 500);
        }

    }

    public function getAction(Request $request) {
        try {
            $id = $request->get('id');

            $contentType = RdfResponse::selectAcceptable($request->getAcceptableContentTypes());


            $comment = $this->commentService->getById($id);
            if (!$comment) {
                throw new NotFoundHttpException('Comment is missing');
            }

            if ($contentType === 'application/json')    {
                $response = new JsonResponse([
                    'success' => true,
                    'id' => $id,
                    'author_uri' => $comment->getAuthorURI(),
                    'entity_uri' => $comment->getEntityURI(),
                    'entity_namespace' => $comment->getEntityNamespace(),
                    'comment_body' => $comment->getMessage(),
                    'date' => $comment->getCreationDate(),
                    'status' => $comment->getStatus(),
                    'authority' => $comment->getVotes()->getVoteValue()
                ]);
            }
            else {

                RdfNamespace::set('schema', 'http://schema.org/');
                RdfNamespace::set('tal', 'http://semantic.talaka.by/ns/talaka.owl#');

                $graph = new Graph();

                $commentURI = $this->urinator->generateURI($comment);

                $resource = $graph->resource($commentURI, 'schema:UserComments');
                $resource->set('tal:commentId', new Literal($id, null, 'xsd:string'));
                $resource->addResource('schema:author', $comment->getAuthorURI());
                $resource->addResource('schema:discusses', $comment->getEntityURI());
                $resource->addResource('schema:replyToUrl', $comment->getEntityURI());
                $resource->set('schema:commentText', new Literal($comment->getMessage(), null, 'xsd:string'));
                $resource->set('schema:commentTime', new Literal\DateTime($comment->getCreationDate()));
                $resource->addResource('schema:eventStatus', $this->commentService->getCommentStatusSchemaCompatible($comment));
                $resource->set('schema:rating', new Literal($comment->getVotes()->getVoteValue()));

                $authorEntity = $comment->getAuthor();
                $author = $graph->resource($comment->getAuthorURI(), 'schema:author');
                $author->addLiteral('foaf:firstName', $authorEntity->getName());
                $author->addLiteral('foaf:lastName', $authorEntity->getSurname());
                $author->addLiteral('foaf:img', $authorEntity->getAvatarURL());



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
     * @param Request $request
     * @return JsonResponse
     * FIXME: service can be optimizaed by disabling hydration
     */
    public function indexAction(Request $request)   {
        try {
            $entityURI = $request->get('entity_uri');
            $onlyPublic = !$request->get('include_non_public', false);
            $page = (int)$request->get('page', 1);
            $limit = (int)$request->get('limit', 8);

            if ($limit > 100) {
                $limit = 100;
            }

            $idList = $this->entityService->getCommentsList($entityURI);

            if (!$onlyPublic) {
                $idList = array_merge($idList, $this->entityService->getDirtyCommentsList($entityURI));
            }

            $total = count($idList);

            $pages = ceil($total / $limit);
            if ($page > $pages) {
                $page = $pages;
            }

            $skip = ($page - 1) * $limit;


            $query = $this->commentService->getQueryListByIds($idList, $onlyPublic, true, true);
//            $comments = $query->skip($skip)->limit($limit)->getQuery()->execute();
            $comments = $query->getQuery()->execute();

            $dataSet = [];
            foreach ($comments as $comment) {
                $dataSet[] = $this->commentService->getPublicRepresentation($comment);
            }

            $response = new JsonResponse([
                'success' => true,
                'total' => $total,
                'page' => $page,
                'pages' => $pages,
                'comments' => $dataSet
            ]);

            $response->headers->add(['Access-Control-Allow-Origin' => '*']);


            return $response;
        }
        catch(\Exception $e)    {
            $response = new JsonResponse([
                'success' => false,
                'message' => $e->getMessage()
            ]);

            $response->setStatusCode(404);

            return $response;
        }
    }

    /**
     * @param Logger $logger
     */
    public function setLogger($logger)
    {
        $this->logger = $logger;
    }

    /**
     * @param EventLogger $eventLogger
     */
    public function setEventLogger($eventLogger)
    {
        $this->eventLogger = $eventLogger;
    }




}