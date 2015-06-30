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

    protected $lastErrorCode = 'minimal_comments_rule';

    protected $minimalCommentsCount = 1;

    public function check(Author $subject, Author $object, $relatedEntity = null)    {

        $pass = $subject->getCommentsCount() > 0;

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