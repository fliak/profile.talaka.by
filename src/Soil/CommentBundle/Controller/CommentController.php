<?php
/**
 * Created by PhpStorm.
 * User: fliak
 * Date: 8.1.15
 * Time: 11.51
 */

namespace Soil\CommentBundle\Controller;


use Doctrine\ODM\MongoDB\Tests\HydratorTest;
use Doctrine\ORM\Internal\Hydration\ObjectHydrator;
use Soil\CommentBundle\Entity\Comment;
use Soil\CommentBundle\Service\AuthorService;
use Soil\CommentBundle\Service\CommentService;
use Soil\CommentBundle\Service\EntityService;
use Soil\CommentBundle\Service\Exception;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class CommentController {

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

    public function __construct(CommentService $commentService, EntityService $entityService, AuthorService $authorService) {
        $this->commentService = $commentService;
        $this->entityService = $entityService;
        $this->authorService = $authorService;
    }


    public function pushAction(Request $request)   {
        $requestContent = $request->getContent();
        $data = json_decode($requestContent, true);

        $comment = $this->commentService->factory();

        $this->commentService->hydrate($comment, $data);
        $comment->setStatus(Comment::COMMENT_STATUS_PUBLIC);

        $result = $this->commentService->isValid($comment);


        if (!$result)   {
            throw new \Exception('Request malformed');
        }

        $commentedEntity = $this->entityService->getByURI($comment->getEntityURI());

        if (!$commentedEntity)  {
            $commentedEntity = $this->entityService->factory();
            $commentedEntity->setEntityNamespace($comment->getEntityNamespace());
            $commentedEntity->setEntityURI($comment->getEntityURI());

            $this->entityService->persist($commentedEntity);
        }

        $comment->setEntity($commentedEntity); //set entity back
        $commentedEntity->getComments()->add($comment); //make relation to comment
        $commentedEntity->incrementCommentsCount(); //update comment counter
        $commentedEntity->setLastCommentDate($comment->getCreationDate());


        $commentAuthor = $this->authorService->getByURI($comment->getAuthorURI());

        if (!$commentAuthor)    {
            $commentAuthor = $this->authorService->factory();
            $commentAuthor->setAuthorURI($comment->getAuthorURI());
            $this->authorService->discover($commentAuthor);

            $this->authorService->persist($commentAuthor);
        }

        $comment->setAuthor($commentAuthor);
        $commentAuthor->getComments()->add($comment);
        $commentAuthor->incrementCommentsCount();
        $commentAuthor->setLastCommentDate($comment->getCreationDate());

        $commentedEntity->incrementUser($commentAuthor);
        $commentedEntity->setLastCommentAuthor($commentAuthor);


        $parentCommentId = $comment->getParent();
        if ($parentCommentId)   {
            $parentComment = $this->commentService->getById($comment->getParent());
            if (!$parentComment)  {
                throw new NotFoundHttpException("Parent comment `$parentCommentId` is missing");
            }

            $comment->setParent($parentComment);
            $parentComment->addChildren($comment);
        }

        $this->commentService->persist($comment);

        $this->commentService->getDM()->flush();


        return new JsonResponse([
            'success' => true,
            'id' => $comment->getId()
        ]);
    }

    public function removeAction(Request $request)   {
        try {
            $id = $request->get('id');

            $body = $request->getContent();
            $data = json_decode($body, true);

            $comment = $this->commentService->getById($id);

            if ($comment->getStatus() === Comment::COMMENT_STATUS_REMOVED)  {
                throw new NotFoundHttpException('Comment is not exists');
            }
            $this->commentService->remove($comment, $data);

            $this->commentService->getDM()->flush();

            return new JsonResponse([
                'success' => true
            ]);
        }
        catch(\Exception $e)    {
            return new JsonResponse([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }

    }

    public function getAction(Request $request) {
        try {
            $id = $request->get('id');

            $comment = $this->commentService->getById($id);
            if (!$comment) {
                throw new NotFoundHttpException('Comment is missing');

            }

            $response = new JsonResponse([
                'success' => true,
                'id' => $id,
                'author_uri' => $comment->getAuthorURI(),
                'entity_uri' => $comment->getEntityURI(),
                'entity_namespace' => $comment->getEntityNamespace(),
                'comment_body' => $comment->getCommentBody(),
                'date' => $comment->getCreationDate(),
                'status' => $comment->getStatus()
            ]);
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
        $entityURI = $request->get('entity_uri');
        $onlyPublic = !$request->get('include_non_public', false);
        $page = (int)$request->get('page', 1);
        $limit = (int)$request->get('limit', 8);

        if ($limit > 100)    {
            $limit = 100;
        }

        $idList = $this->entityService->getCommentsList($entityURI);

        if (!$onlyPublic)    {
            $idList = array_merge($idList, $this->entityService->getDirtyCommentsList($entityURI));
        }

        $total = count($idList);

        $pages = ceil($total / $limit);
        if ($page > $pages) {
            $page = $pages;
        }

        $skip = ($page - 1) * $limit;


        $query = $this->commentService->getQueryListByIds($idList, $onlyPublic, true);
        $comments = $query->skip($skip)->limit($limit)->getQuery()->execute();

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

} 