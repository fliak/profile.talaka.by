<?php
    /**
 * Created by PhpStorm.
 * User: fliak
 * Date: 7.1.15
 * Time: 21.42
 */

namespace Soil\CommentBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;
use Soil\AuthorityBundle\Entity\VoteAggregatorInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Soil\CommentBundle\Entity\Author;
use Soil\CommentBundle\Entity\Entity;

/**
 * Class Comment
 * @package Soil\CommentBundle\Entity
 * @ODM\Document(collection="comment")
 */
class Comment implements VoteAggregatorInterface {

    const COMMENT_STATUS_REMOVED = 'removed';
    const COMMENT_STATUS_PUBLIC = 'public';
    const COMMENT_STATUS_WAITING = 'waiting';
    const COMMENT_STATUS_DECLINED = 'declined';

    public function __construct()   {
        $this->log = new ArrayCollection();
        $this->children = new ArrayCollection();
//        $this->votes = new Votes();
    }

    /**
     * @var string
     *
     * @ODM\Id
     */
    protected $id;

    /**
     * @var Votes
     * @ODM\EmbedOne(
     *    targetDocument="Votes"
     * )
     */
    protected $votes;


    /**
     * @var Comment
     * @ODM\ReferenceOne(
     *     targetDocument="Comment",
     *     simple=true
     * )
     */
    protected $parent;

    /**
     * @var string
     * @ODM\String(name="parent_uri")
     */
    protected $parentURI;


    /**
     * @var ArrayCollection
     * @ODM\ReferenceMany(
     *    targetDocument="Comment",
     *    simple=true
     * )
     */
    protected $children;

    /**
     * @var ArrayCollection
     * @ODM\EmbedMany(
     *    targetDocument="LogEntry"
     * )
     */
    protected $log;

    /**
     * @var string
     * @ODM\String
     */
    protected $status;

    /**
     * @var string
     * @ODM\String
     */
    protected $instanceNumber;

    /**
     * Author URI
     * @var string
     * @Assert\NotBlank()
     * @ODM\String(name="author_uri")
     */
    protected $authorURI;

    /**
     * Author statistic entity
     * @var Author
     * @ODM\ReferenceOne(
     *    targetDocument="Author",
     *    simple=true
     * )
     */
    protected $author;

    /**
     * Commented entity URI
     * @var string
     * @Assert\NotBlank()
     * @ODM\String(name="entity_uri")
     */
    protected $entityURI;

    /**
     * @var Entity
     * @ODM\ReferenceOne(
     *    targetDocument="Entity",
     *    simple=true
     * )
     */
    protected $entity;


    /**
     * Entity type
     * @var string
     * @ODM\String(name="entity_namespace")
     */
    protected $entityNamespace;

    /**
     * Comment text
     * @var string
     * @Assert\NotBlank()
     * @ODM\String(name="message")
     */
    protected $message;


    /**
     * Comment creation date
     * @var \DateTime
     * @Assert\NotBlank()
     * @ODM\Date(name="creation_date")
     */
    protected $creationDate;

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
    public function getAuthor()
    {
        return $this->author;
    }

    /**
     * @param Author $author
     */
    public function setAuthor($author)
    {
        $this->author = $author;
        $this->authorURI = $author->getAuthorURI();
    }

    /**
     * @return string
     */
    public function getAuthorURI()
    {
        return $this->authorURI;
    }

    /**
     * @param string $authorURI
     */
    public function setAuthorURI($authorURI)
    {
        $this->authorURI = $authorURI;
    }

    /**
     * @return string
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * @param string $message
     */
    public function setMessage($message)
    {
        $this->message = $message;
    }

    /**
     * @return \DateTime
     */
    public function getCreationDate()
    {
        return $this->creationDate;
    }

    /**
     * @param \DateTime $creationDate
     */
    public function setCreationDate($creationDate)
    {
        $this->creationDate = $creationDate;
    }

    /**
     * @return Entity
     */
    public function getEntity()
    {
        return $this->entity;
    }

    /**
     * @param Entity $entity
     */
    public function setEntity($entity)
    {
        $this->entity = $entity;
        $this->entityNamespace = $entity->getEntityNamespace();
        $this->entityURI = $entity->getEntityURI();
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
    public function getInstanceNumber()
    {
        return $this->instanceNumber;
    }

    /**
     * @param string $instanceNumber
     */
    public function setInstanceNumber($instanceNumber)
    {
        $this->instanceNumber = $instanceNumber;
    }

    /**
     * @return int
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @param int $status
     */
    public function setStatus($status)
    {
        $this->status = $status;
    }

    /**
     * @return ArrayCollection
     */
    public function getLog()
    {
        return $this->log;
    }

    /**
     * @param ArrayCollection $log
     */
    public function setLog($log)
    {
        $this->log = $log;
    }


    public function addLogEntry(LogEntry $logEntry) {
        $this->log->add($logEntry);
    }

    /**
     * @return ArrayCollection
     */
    public function getChildren()
    {
        return $this->children;
    }

    /**
     * @param ArrayCollection $children
     */
    public function setChildren($children)
    {
        $this->children = $children;
    }

    public function addChildren($children)  {
        $this->children->add($children);
    }

    public function removeChildren($children)   {
        $this->children->remove($children);
    }


    /**
     * @return Comment
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * @param Comment $parent
     */
    public function setParent($parent)
    {
        $this->parent = $parent;
    }

    /**
     * @return string
     */
    public function getParentURI()
    {
        return $this->parentURI;
    }

    /**
     * @param string $parentURI
     */
    public function setParentURI($parentURI)
    {
        $this->parentURI = $parentURI;
    }

    /**
     * @return Votes
     */
    public function getVotes()
    {
        if (!$this->votes)  {
            $this->votes = new Votes(); //for old comments
        }

        return $this->votes;
    }

    /**
     * @param Votes $votes
     */
    public function setVotes($votes)
    {
        $this->votes = $votes;
    }



}



