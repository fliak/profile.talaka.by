<?php
/**
 * Created by PhpStorm.
 * User: fliak
 * Date: 27.6.15
 * Time: 20.55
 */

namespace Soil\AuthorityBundle\Rules;


use Soil\AuthorityBundle\Util\DateAndTime;
use Soil\CommentBundle\Entity\Author;

class BadAuthorityRule implements VoteRuleInterface {

    protected $message = "Your authority does not allow you to vote";

    protected $lastErrorCode = 'bad_authority';

    protected $minimalBannedAuthority = -10;

    public function check(Author $subject, Author $object, $relatedEntity = null)    {

        $pass = $subject->getVotes()->getVoteValue() > $this->minimalBannedAuthority;
        if (!$pass) {
            throw new VoteRuleException($this->lastErrorCode, $this->message, [
                'minimal_banned_authority' => $this->minimalBannedAuthority
            ]);
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