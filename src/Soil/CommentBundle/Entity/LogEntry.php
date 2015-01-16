<?php
/**
 * Created by PhpStorm.
 * User: fliak
 * Date: 9.1.15
 * Time: 13.22
 */

namespace Soil\CommentBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;

/**
 * Class LogEntry
 * @package Soil\CommentBundle\Entity
 *
 * @ODM\EmbeddedDocument
 */
class LogEntry {

    const TYPE_COMMENT = 'comment';
    const TYPE_PATCH = 'patch';
    const TYPE_CREATE = 'create';
    const TYPE_REMOVE = 'remove';

    public function __construct()   {
        $this->date = new \DateTime();
    }

    /**
     * @var string
     * @ODM\String
     */
    protected $message;

    /**
     * @var Author
     * @ODM\ReferenceOne(
     *      targetDocument="Author"
     * )
     */
    protected $user;

    /**
     * @var \DateTime
     * @ODM\Date
     */
    protected $date;

    /**
     * @var string
     * @ODM\String
     */
    protected $type;

    /**
     * @var ArrayCollection
     * @ODM\Hash
     */
    protected $changeSet;

    /**
     * @return ArrayCollection
     */
    public function getChangeSet()
    {
        return $this->changeSet;
    }

    /**
     * @param ArrayCollection $changeSet
     */
    public function setChangeSet($changeSet)
    {
        $this->changeSet = $changeSet;
    }

    /**
     * @return \DateTime
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * @param \DateTime $date
     */
    public function setDate($date)
    {
        $this->date = $date;
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
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param string $type
     */
    public function setType($type)
    {
        $this->type = $type;
    }

    /**
     * @return Author
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @param Author $user
     */
    public function setUser($user)
    {
        $this->user = $user;
    }



} 