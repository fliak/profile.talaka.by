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
use Soil\AuthorityBundle\Entity\VoteAggregatorInterface;
use Soil\CommentBundle\Entity\Author;
use Soil\CommentBundle\Entity\Comment;

/**
 * Class Entity
 * @package Soil\CommentBundle\Entity
 * @ODM\Document(db="comments_talaka", collection="entity")
 *
 */
class Entity implements VoteAggregatorInterface {

    public function __construct()   {
        $this->userList = [];
        $this->comments = new ArrayCollection();
        $this->dirty_comments = new ArrayCollection();
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
     *    targetDocument="Author",
     *    simple=true
     * )
     */
    protected $lastCommentAuthor;

    /**
     * @var ArrayCollection
     * @ODM\Hash
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
     * @var ArrayCollection
     * @ODM\ReferenceMany(
     *    targetDocument="Comment",
     *    simple=true
     * )
     */
    protected $dirty_comments;

    /**
     * @var Votes
     * @ODM\EmbedOne(
     *    targetDocument="Votes"
     * )
     */
    protected $votes;


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
     * @return array
     */
    public function getUserList()
    {
        return $this->userList;
    }

    /**
     * @param array $userList
     */
    public function setUserList($userList)
    {
        $this->userList = $userList;
    }

    public function incrementUser(Author $user)  {
        $plainId = (string)$user->getId();
        if (array_key_exists($plainId, $this->userList))  {
            $this->userList[$plainId]++;
        }
        else    {
            $this->userList[$plainId] = 1;
        }
    }

    public function decrementUser(Author $user) {
        $plainId = (string)$user->getId();
        if (array_key_exists($plainId, $this->userList))  {
            $commentsCount = $this->userList[$plainId];
            if ($commentsCount <= 1)   {
                unset($this->userList[$plainId]);
            }
            else    {
                $this->userList[$plainId]--;
            }

        }
        else    {
            //do nothing
        }
    }


    public function addUser2(Author $user)  {
        $exist = $this->userList->exists(function($key, Author $existedUser) use ($user) {
            if ($existedUser->getAuthorURI() === $user->getAuthorURI()) {
                return true;
            }
            else    {
                return false;
            }
        });

        if ($exist) {
            return false; //already exist
        }
        else    {
            $this->userList->add($user);
        }
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

    public function removeComment($comment)    {
        $this->comments->removeElement($comment);
    }

    /**
     * @return ArrayCollection
     */
    public function getDirtyComments()
    {
        return $this->dirty_comments;
    }

    /**
     * @param ArrayCollection $dirty_comments
     */
    public function setDirtyComments($dirty_comments)
    {
        $this->dirty_comments = $dirty_comments;
    }

    public function removeDirtyComment($comment)    {
        $this->dirty_comments->removeElement($comment);
    }

    /**
     * @return Votes
     */
    public function getVotes()
    {
        $this->votes = new Votes();

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