<?php
/**
 * Created by PhpStorm.
 * User: fliak
 * Date: 8.1.15
 * Time: 18.15
 */

namespace Soil\CommentBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;
use Soil\CommentBundle\Entity\Author;
use Soil\CommentBundle\Entity\Comment;

/**
 * Class Entity
 * @package Soil\CommentBundle\Entity
 * @ODM\Document(db="comments_talaka", collection="entity")
 *
 */
class Entity {

    public function __construct()   {
        $this->userList = new ArrayCollection();
        $this->comments = new ArrayCollection();
        $this->commentsCount = 0;
    }

    /**
     * @var string
     * @ODM\Id
     */
    protected $id;

    /**
     * @var string
     * @ODM\String(name="entity_uri")
     */
    protected $entityURI;

    /**
     * @var string
     * @ODM\String(name="entity_namespace")
     */
    protected $entityNamespace;

    /**
     * @var int
     * @ODM\Int(name="comments_count")
     */
    protected $commentsCount;

    /**
     * @var \DateTime
     * @ODM\Date(name="last_comment_date")
     */
    protected $lastCommentDate;

    /**
     * @var Author
     * @ODM\ReferenceOne(
     *    targetDocument="Author"
     * )
     */
    protected $lastCommentAuthor;

    /**
     * @var ArrayCollection
     * @ODM\ReferenceMany(
     *    targetDocument="Author"
     * )
     */
    protected $userList;

    /**
     * @var ArrayCollection
     * @ODM\ReferenceMany(
     *    targetDocument="Comment",
     *    simple=true
     * )
     */
    protected $comments;

    /**
     * @return int
     */
    public function getCommentsCount()
    {
        return $this->commentsCount;
    }

    /**
     * @param int $commentsCount
     */
    public function setCommentsCount($commentsCount)
    {
        $this->commentsCount = $commentsCount;
    }


    public function incrementCommentsCount($number = 1) {
        $this->commentsCount += $number;
    }

    /**
     * @return string
     */
    public function getEntityNamespace()
    {
        return $this->entityNamespace;
    }

    /**
     * @param string $entityNamespace
     */
    public function setEntityNamespace($entityNamespace)
    {
        $this->entityNamespace = $entityNamespace;
    }

    /**
     * @return string
     */
    public function getEntityURI()
    {
        return $this->entityURI;
    }

    /**
     * @param string $entityURI
     */
    public function setEntityURI($entityURI)
    {
        $this->entityURI = $entityURI;
    }

    /**
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param string $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return Author
     */
    public function getLastCommentAuthor()
    {
        return $this->lastCommentAuthor;
    }

    /**
     * @param Author $lastCommentAuthor
     */
    public function setLastCommentAuthor($lastCommentAuthor)
    {
        $this->lastCommentAuthor = $lastCommentAuthor;
    }

    /**
     * @return \DateTime
     */
    public function getLastCommentDate()
    {
        return $this->lastCommentDate;
    }

    /**
     * @param \DateTime $lastCommentDate
     */
    public function setLastCommentDate($lastCommentDate)
    {
        $this->lastCommentDate = $lastCommentDate;
    }


    /**
     * @return ArrayCollection
     */
    public function getUserList()
    {
        return $this->userList;
    }

    /**
     * @param ArrayCollection $userList
     */
    public function setUserList($userList)
    {
        $this->userList = $userList;
    }

    /**
     * @return ArrayCollection
     */
    public function getComments()
    {
        return $this->comments;
    }

    /**
     * @param ArrayCollection $comments
     */
    public function setComments($comments)
    {
        $this->comments = $comments;
    }





} 