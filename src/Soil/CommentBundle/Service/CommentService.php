<?php
/**
 * Created by PhpStorm.
 * User: fliak
 * Date: 8.1.15
 * Time: 17.02
 */

namespace Soil\CommentBundle\Service;


use Doctrine\ODM\MongoDB\DocumentManager;
use Soil\CommentBundle\Entity\Comment;
use Soil\CommentBundle\Entity\LogEntry;
use Soil\CommentBundle\Service\Exception\DeleteForbiddenException;

class CommentService {

    /**
     * @var DocumentManager
     */
    protected $dm;

    protected $errors;

    /**
     * @var
     */
    protected $validator;

    /**
     * @var EntityService
     */
    protected $entityService;

    /**
     * @var AuthorService
     */
    protected $authorService;

    /**
     * @param mixed $authorService
     */
    public function setAuthorService($authorService)
    {
        $this->authorService = $authorService;
    }

    /**
     * @param mixed $entityService
     */
    public function setEntityService($entityService)
    {
        $this->entityService = $entityService;
    }




    public function __construct($dm, $validator)    {
        $this->dm = $dm->getManager();
        $this->validator = $validator;
    }

    public function factory()   {
        $entity = new Comment();
        $entity->setCreationDate(new \DateTime());

        return $entity;
    }

    public function hydrate($entity, $data) {
        $hydrator = $this->dm->getHydratorFactory();
        $hydrator->hydrate($entity, $data);
    }

    public function isValid($entity)    {
        $this->errors = $this->validator->validate($entity);

        if (count($this->errors) > 0) {
            return false;
            //FIXME: write log for debug mode
        }

        return true;
    }

    public function getLastValidatorViolations()    {
        return $this->errors;
    }

    public function persist($entity)   {
        $this->dm->persist($entity);
    }

    public function getDM() {
        return $this->dm;
    }

    /**
     * @param $id
     * @return Comment
     */
    public function getById($id)  {
        return $this->getRepository()->find($id);
    }

    public function getRepository()  {
        return $this->dm->getRepository('Soil\CommentBundle\Entity\Comment');
    }

    public function getQueryBuilder()   {
        return $this->dm->createQueryBuilder('Soil\CommentBundle\Entity\Comment');
    }

    public function remove(Comment $comment, $data = null)    {

        //check for children comments.
        $childrenCommentsCount = $comment->getChildren()->count();
        if ($childrenCommentsCount > 0)   {
            throw new DeleteForbiddenException("Comment has $childrenCommentsCount so cannot be removed");
        }

        //remove from children list of parent comment
        if ($comment->getParent())  {
            $comment->getParent()->removeChildren($comment);
        }

        //remove from entity comments list
        $entity = $comment->getEntity();
        if ($comment->getStatus() === Comment::COMMENT_STATUS_PUBLIC)   {
            $entity->incrementCommentsCount(-1);
            $entity->removeComment($comment);
        }
        else    {
            $entity->removeDirtyComment($comment);
        }

        //remove author comment from entity commentators list
        $entity->decrementUser($comment->getAuthor());

        $comment->setStatus(Comment::COMMENT_STATUS_REMOVED);

        $logEntry = new LogEntry();
        $logEntry->setType('remove');

        if ($data && is_array($data))   {
            if (array_key_exists('message', $data)) {
                $logEntry->setMessage($data['message']);
            }

            if (array_key_exists('user_uri', $data)) {
                $user = $this->authorService->getByURI($data['user_uri']);
                if ($user) {
                    $logEntry->setUser($user);
                }
            }

        }

        $comment->addLogEntry($logEntry);
    }

    /**
     * @param $entityURI
     * @return \Doctrine\MongoDB\Query\Builder
     */
    protected function getForEntityQB($entityURI) {
        $query = $this->getQueryBuilder()
            ->field('entity_uri')->equals($entityURI);

        return $query;
    }

    public function getQueryListByIds($ids, $onlyPublic = true, $onlyFirstLine = false, $hydrate = false)  {
        $query = $this->getQueryBuilder()
            ->field('_id')->in($ids);

        if ($onlyPublic)    {
            $query->field('status')->equals(Comment::COMMENT_STATUS_PUBLIC);
        }
        else    {
            $query->field('status')->in([
                Comment::COMMENT_STATUS_PUBLIC,
                Comment::COMMENT_STATUS_WAITING
            ]);
        }

        if ($onlyFirstLine) {
            $query->field('parent')->equals(null);
        }


        $query->eagerCursor(true)
            ->hydrate($hydrate);

        return $query;

    }

    public function getPublicRepresentation(Comment $comment)   {

        $children = $comment->getChildren();
        $childSet = [];
        if ($children)  {
            foreach ($children as $child)   {
                $childSet[] = $this->getPublicRepresentation($child);
            }
        }

        $authorEntity = $comment->getAuthor();
        $author = [
            'author_uri' => $comment->getAuthorURI(),
            'avatar' => $authorEntity ? $authorEntity->getAvatarURL() : null,
            'name'   => $authorEntity ? $authorEntity->getName() : null,
            'surname'   => $authorEntity ? $authorEntity->getSurname() : null,
        ];


        return [
            'id'               => $comment->getId(),
            'author_uri'       => $comment->getAuthorURI(),
            'author'           => $author,
            'entity_uri'       => $comment->getEntityURI(),
            'entity_namespace' => $comment->getEntityNamespace(),
            'status'           => $comment->getStatus(),
            'timestamp'        => $comment->getCreationDate()->getTimestamp(),
            'message'          => $comment->getMessage(),
            'parent'           => $comment->getParent() ? $comment->getParent()->getId() : null,
            'children'         => $childSet
        ];


    }

}