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

class MinimalCommentsRule implements VoteRuleInterface {

    protected $message = 'Voter need at least one comment on platform for voting.';

    protected $minimalCommentsCount = 1;

    public function check(Author $subject, Author $object, $relatedEntity = null)    {

        return $subject->getCommentsCount() > 0;
    }

    public function hit(Author $subject, Author $object, $relatedEntity = null)    {

    }

    public function getLastMessage()    {
        return $this->message;
    }


}