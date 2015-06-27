<?php
/**
 * Created by PhpStorm.
 * User: fliak
 * Date: 24.6.15
 * Time: 8.09
 */

namespace Soil\AuthorityBundle\Entity;


use Doctrine\Common\Collections\ArrayCollection;
use Soil\CommentBundle\Entity\Votes;

interface VoteAggregatorInterface {

    /**
     * @return Votes
     */
    public function getVotes();

    /**
     * @param Votes $votes
     */
    public function setVotes($votes);

}