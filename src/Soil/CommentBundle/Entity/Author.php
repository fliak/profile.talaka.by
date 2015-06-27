<?php
/**
 * Created by PhpStorm.
 * User: fliak
 * Date: 8.1.15
 * Time: 18.33
 */

namespace Soil\CommentBundle\Entity;

use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;
use Doctrine\Common\Collections\ArrayCollection;

use Soil\AuthorityBundle\Entity\VoteAggregatorInterface;
use Soil\CommentBundle\Entity\Comment;
use Soil\CommentBundle\Entity\Entity;

/**
 * Class Author
 * @package Soil\CommentBundle\Entity
 * @ODM\Document(db="comments_talaka", collection="authors")
 */
class Author implements VoteAggregatorInterface {


    public function __construct()   {
        $this->comments = new ArrayCollection();

    }

    /**
     * @var string
     * @ODM\Id
     */
    protected $id;

    /**
     * @var string
     * @ODM\String
     */
    protected $name;

    /**
     * @var string
     * @ODM\String
     */
    protected $surname;


    /**
     * @var Votes
     * @ODM\EmbedOne(
     *    targetDocument="Votes"
     * )
     */
    protected $votes;


    /**
     * @var string
     * @ODM\String(name="author_uri")
     */
    protected $authorURI;

    /**
     * @var string
     * @ODM\String(name="avatar_url")
     */
    protected $avatarURL;

    /**
     * @var int
     * @ODM\Int(name="comments_count")
     */
    protected $commentsCount;

    /**
     * @var string
     * @ODM\ReferenceMany(
     *    targetDocument="Comment",
     *    simple=true
     * )
     */
    protected $comments;

    /**
     * @var \DateTime
     * @ODM\Date(name="last_comment_date")
     */
    protected $lastCommentDate;

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
    public function getComments()
    {
        return $this->comments;
    }

    /**
     * @param string $comments
     */
    public function setComments($comments)
    {
        $this->comments = $comments;
    }

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

        return $this->commentsCount;
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
     * @return string
     */
    public function getAvatarURL()
    {
        return $this->avatarURL;
    }

    /**
     * @param string $avatarURL
     */
    public function setAvatarURL($avatarURL)
    {
        $this->avatarURL = $avatarURL;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getSurname()
    {
        return $this->surname;
    }

    /**
     * @param string $surname
     */
    public function setSurname($surname)
    {
        $this->surname = $surname;
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