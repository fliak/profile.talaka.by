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
use Soil\CommentBundle\Service\AuthorService;
use Soil\CommentBundle\Service\CommentService;
use Soil\CommentBundle\Service\EntityService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

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
        $request = $request->getContent();
        $data = json_decode($request, true);

        $comment = $this->commentService->factory();

        $this->commentService->hydrate($comment, $data);

        $commentedEntity = $this->entityService->getByURI($comment->getEntityURI());

        if (!$commentedEntity)  {
            $commentedEntity = $this->entityService->factory();
            $commentedEntity->setEntityNamespace($comment->getEntityNamespace());
            $commentedEntity->setEntityURI($comment->getEntityURI());

            $this->entityService->persist($commentedEntity);
        }

        $comment->setEntity($commentedEntity);
        $commentedEntity->getComments()->add($comment);
        $commentedEntity->incrementCommentsCount();




//print_r($comment);exit();


        $result = $this->commentService->isValid($comment);

        if (!$result)   {
            throw new \Exception('Request malformed');
        }

        $this->commentService->persist($comment);

        $this->commentService->getDM()->flush();



        print_r($comment);

        return new Response(json_encode(['a' => 1]));
    }
} 