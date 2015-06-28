<?php
/**
 * Created by PhpStorm.
 * User: fliak
 * Date: 20.1.15
 * Time: 15.14
 */

namespace Soil\CommentBundle\Entity;
use Doctrine\Common\Collections\ArrayCollection;

use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;
use Soil\AuthorityBundle\Entity\Vote;

/**
 * Class Votes
 * @package Soil\CommentBundle\Entity
 * @ODM\EmbeddedDocument
 */
class Votes {

    /**
     * @var int
     * @ODM\Integer(name="vote_value")
     */
    protected $voteValue;

    /**
     * @var \DateTime
     * @ODM\Date(name="last_voting_date")
     */
    protected $lastVotingDate;

    /**
     * @var int
     * @ODM\Integer(name="vote_count_per_day")
     */
    protected $voteCountPerDay;

    /**
     * @var int
     * @ODM\Integer(name="vote_count_total")
     */
    protected $voteCountTotal;


    /**
     * @var ArrayCollection
     * @ODM\ReferenceMany(
     *   targetDocument="Soil\AuthorityBundle\Entity\Vote",
     *   simple="true"
     * )
     */
    protected $history;

    /**
     * @var array
     * @ODM\EmbedMany(
     *      name="by_user_index",
     *      targetDocument="Soil\AuthorityBundle\Entity\IndexElement",
     *      strategy="set"
     * )
     */
    protected $byUserIndex;


    /**
     * @var array
     * @ODM\EmbedMany(
     *      name="by_entity_index",
     *      targetDocument="Soil\AuthorityBundle\Entity\IndexElement",
     *      strategy="set"
     * )
     */
    protected $byEntityIndex;




    /**
     * @return ArrayCollection
     */
    public function getHistory()
    {
        return $this->history;
    }

    /**
     * @param Array $history
     */
    public function setHistory($history)
    {
        $this->history = $history;
    }

    /**
     * @return int
     */
    public function getVoteValue()
    {
        return $this->voteValue;
    }

    /**
     * @param int $voteValue
     */
    public function setVoteValue($voteValue)
    {
        $this->voteValue = $voteValue;
    }

    public function addVote($voteIncrement) {
        $this->voteValue += $voteIncrement;
    }





    public function __construct()   {
        $this->voteValue = 0;

        $this->history = new ArrayCollection();

        $this->byUserIndex = new ArrayCollection();
    }

    /**
     * @return \DateTime
     */
    public function getLastVotingDate()
    {
        return $this->lastVotingDate;
    }

    /**
     * @param \DateTime $lastVotingDate
     */
    public function setLastVotingDate($lastVotingDate)
    {
        $this->lastVotingDate = $lastVotingDate;
    }

    /**
     * @return int
     */
    public function getVoteCountPerDay()
    {
        return $this->voteCountPerDay;
    }

    /**
     * @param int $voteCountPerDay
     */
    public function setVoteCountPerDay($voteCountPerDay)
    {
        $this->voteCountPerDay = $voteCountPerDay;
    }

    /**
     * @return int
     */
    public function getVoteCountTotal()
    {
        return $this->voteCountTotal;
    }

    /**
     * @param int $voteCountTotal
     */
    public function setVoteCountTotal($voteCountTotal)
    {
        $this->voteCountTotal = $voteCountTotal;
    }

    public function incrementVoteCount()    {
        $this->voteCountPerDay++;
        $this->voteCountTotal++;
    }

    /**
     * @return ArrayCollection
     */
    public function getByUserIndex()
    {
        return $this->byUserIndex;
    }

    /**
     * @param array $byUserIndex
     */
    public function setByUserIndex($byUserIndex)
    {
        $this->byUserIndex = $byUserIndex;
    }

    /**
     * @return array
     */
    public function getByEntityIndex()
    {
        if (!$this->byEntityIndex)  {
            $this->byEntityIndex = new ArrayCollection();
        }
        return $this->byEntityIndex;
    }

    /**
     * @param array $byEntityIndex
     */
    public function setByEntityIndex($byEntityIndex)
    {
        $this->byEntityIndex = $byEntityIndex;
    }



} 