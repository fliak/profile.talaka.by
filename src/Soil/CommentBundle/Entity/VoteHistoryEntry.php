<?php
/**
 * Created by PhpStorm.
 * User: fliak
 * Date: 20.1.15
 * Time: 15.25
 */

namespace Soil\CommentBundle\Entity;

use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;

/**
 * Class VoteHistoryEntry
 * @package Soil\CommentBundle\Entity
 * @ODM\EmbeddedDocument
 */
class VoteHistoryEntry {

    /**
     * @var Author
     * @ODM\ReferenceOne(
     *   targetDocument="Author",
     *   simple=true
     * )
     */
    protected $user;

    /**
     * @var \DateTime
     * @ODM\Date
     */
    protected $date;

    /**
     * @var int
     * @ODM\Int
     */
    protected $vote;

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

    /**
     * @return int
     */
    public function getVote()
    {
        return $this->vote;
    }

    /**
     * @param int $vote
     */
    public function setVote($vote)
    {
        $this->vote = $vote;
    }


    public function __construct()   {
        $this->date = new \DateTime();
    }

} 