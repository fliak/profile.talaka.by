<?php
/**
 * Created by PhpStorm.
 * User: fliak
 * Date: 27.6.15
 * Time: 22.53
 */

namespace Soil\AuthorityBundle\Entity;

use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;

/**
 * Class IndexElement
 * @package Soil\AuthorityBundle\Entity
 * @ODM\EmbeddedDocument
 */
class IndexElement {

    /**
     * @var \DateTime
     * @ODM\Date(name="last_voting_date")
     */
    protected $lastVotingDate;

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



} 