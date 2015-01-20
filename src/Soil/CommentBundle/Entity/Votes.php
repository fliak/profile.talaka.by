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

/**
 * Class Votes
 * @package Soil\CommentBundle\Entity
 * @ODM\EmbeddedDocument
 */
class Votes {

    /**
     * @var int
     * @ODM\Int
     */
    protected $votesPositive;

    /**
     * @var int
     * @ODM\Int
     */
    protected $votesNegative;


    /**
     * @var ArrayCollection
     * @ODM\EmbedMany(
     *   targetDocument="VoteHistoryEntry"
     * )
     */
    protected $history;

    /**
     * @return Array
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
    public function getVotesNegative()
    {
        return $this->votesNegative;
    }

    /**
     * @param int $votesNegative
     */
    public function setVotesNegative($votesNegative)
    {
        $this->votesNegative = $votesNegative;
    }

    /**
     * @return int
     */
    public function getVotesPositive()
    {
        return $this->votesPositive;
    }

    /**
     * @param int $votesPositive
     */
    public function setVotesPositive($votesPositive)
    {
        $this->votesPositive = $votesPositive;
    }


    public function addVote($user, $number) {
        if ($number > 0)    {
            $this->votesPositive += $number;
        }
        else    {
            $this->votesNegative += abs($number);
        }

        $historyEntry = new VoteHistoryEntry();
        $historyEntry->setUser($user);
        $historyEntry->setVote($number);

        $this->history->add($historyEntry);
    }

    public function __construct()   {
        $this->votesNegative = 0;
        $this->votesPositive = 0;

        $this->history = new ArrayCollection();
    }

    public function userCanVote(Author $user)  {
        return !$this->history->exists(function($key, $element) use($user) {
            if ($element->getUser() === $user)  {
                return true;
            }
        });
    }


} 