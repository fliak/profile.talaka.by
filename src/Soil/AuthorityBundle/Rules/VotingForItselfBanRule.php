<?php
/**
 * Created by PhpStorm.
 * User: fliak
 * Date: 27.6.15
 * Time: 21.09
 */

namespace Soil\AuthorityBundle\Rules;


use Soil\CommentBundle\Entity\Author;

class VotingForItselfBanRule implements VoteRuleInterface {

    protected $message = "Agent can't vote for himself";
    protected $lastErrorCode = 'voting_for_itself_ban_rule';

    public function check(Author $subject, Author $object, $relatedEntity = null)    {
        $pass = $subject->getAuthorURI() !== $object->getAuthorURI();


        if (!$pass) {
            throw new VoteRuleException($this->lastErrorCode, $this->message);
        }

        return true;
    }

    public function hit(Author $subject, Author $object, $relatedEntity = null)    {

    }


    public function getLastMessage()    {
        return $this->message;
    }


    /**
     * @return string
     */
    public function getLastErrorCode()
    {
        return $this->lastErrorCode;
    }
} 