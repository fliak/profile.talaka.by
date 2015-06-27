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

    protected $minimalBannedAuthority = -10;

    public function check(Author $subject, Author $object, $relatedEntity = null)    {

        return $subject->getVotes()->getVoteValue() > $this->minimalBannedAuthority;
    }

    public function hit(Author $subject, Author $object, $relatedEntity = null)    {

    }

    public function getLastMessage()    {
        return $this->message;
    }


}