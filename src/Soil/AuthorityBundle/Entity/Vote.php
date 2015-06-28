<?php
/**
 * Created by PhpStorm.
 * User: fliak
 * Date: 23.6.15
 * Time: 21.23
 */

namespace Soil\AuthorityBundle\Entity;

use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;
use Soil\CommentBundle\Entity\Comment;
use Soil\CommentBundle\Entity\Entity;
use Symfony\Component\Validator\Constraints as Assert;

use Soil\CommentBundle\Entity\Author;

/**
 * Class Vote
 * @package Soil\AuthorityBundle\Entity
 * @ODM\Document(collection="vote")
 *
 */
class Vote {

    /**
     * @var string
     *
     * @ODM\Id
     */
    protected $id;

    /**
     * @var integer
     * @Assert\NotBlank()
     * @Assert\Choice(choices = {-1, 1}, message = "Vote should be -1 or 1")
     * @ODM\Int
     */
    protected $vote;

    /**
     * Vote object
     * If vote was given from profile use
     * tal:profile
     * @var string
     * @Assert\NotBlank()
     * @ODM\String(name="object_uri")
     */
    protected $objectURI;


    /**
     * Vote object entity
     * @var Author|Entity|Comment
     * @ODM\ReferenceOne()
     */
    protected $object;



    /**
     * Voter User URI
     * @var string
     * @Assert\NotBlank()
     * @ODM\String(name="voter_uri")
     */
    protected $voterURI;

    /**
     * Voter entity
     * @var Author
     * @ODM\ReferenceOne(
     *    targetDocument="\Soil\CommentBundle\Entity\Author",
     *    simple=true
     * )
     */
    protected $voter;

    /**
     * User URI
     * @var string
     * @ODM\String(name="agent_uri")
     */
    protected $agentURI;

    /**
     * User entity
     * @var Author
     * @ODM\ReferenceOne(
     *    targetDocument="\Soil\CommentBundle\Entity\Author",
     *    simple=true
     * )
     */
    protected $agent;


    /**
     * Comment text
     * @var string
     * @Assert\NotBlank()
     * @Assert\Length(max=300)
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

    public function __construct()   {
        $this->creationDate = new \DateTime();

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
    public function getObjectURI()
    {
        return $this->objectURI;
    }

    /**
     * @param string $objectURI
     */
    public function setObjectURI($objectURI)
    {
        $this->objectURI = $objectURI;
    }

    /**
     * @return Author
     */
    public function getAgent()
    {
        return $this->agent;
    }

    /**
     * @param Author $user
     */
    public function setAgent($user)
    {
        $this->agent = $user;
    }

    /**
     * @return string
     */
    public function getAgentURI()
    {
        return $this->agentURI;
    }

    /**
     * @param string $userURI
     */
    public function setAgentURI($userURI)
    {
        $this->agentURI = $userURI;
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

    /**
     * @return Author
     */
    public function getVoter()
    {
        return $this->voter;
    }

    /**
     * @param Author $voter
     */
    public function setVoter($voter)
    {
        $this->voter = $voter;
    }

    /**
     * @return string
     */
    public function getVoterURI()
    {
        return $this->voterURI;
    }

    /**
     * @param string $voterURI
     */
    public function setVoterURI($voterURI)
    {
        $this->voterURI = $voterURI;
    }

    /**
     * @return Author|Comment|Entity
     */
    public function getObject()
    {
        return $this->object;
    }

    /**
     * @param Author|Comment|Entity $object
     */
    public function setObject($object)
    {
        $this->object = $object;
    }





} 