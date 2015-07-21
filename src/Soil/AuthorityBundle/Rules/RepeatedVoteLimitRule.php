<?php
/**
 * Created by PhpStorm.
 * User: fliak
 * Date: 27.6.15
 * Time: 20.55
 */

namespace Soil\AuthorityBundle\Rules;


use ClassesWithParents\D;
use Soil\AuthorityBundle\Entity\IndexElement;
use Soil\AuthorityBundle\Util\DateAndTime;
use Soil\CommentBundle\Entity\Author;

class RepeatedVoteLimitRule implements VoteRuleInterface {

    protected $message;

    protected $lastErrorCode = 'repeated_vote_for_user_so_often';


    /**
     * @var int
     * In hours
     *
     */
    protected $repeatedVotingFrequency = 24;

    public function check(Author $subject, Author $object, $relatedEntity = null)    {

        if ($relatedEntity) {
            $index = $subject->getVotes()->getByEntityIndex();

            if ($index->containsKey($relatedEntity->getId()))  {
                $this->message = 'You can not change user reputation for one comment twice';
                $this->lastErrorCode = 'repeated_vote_for_comment';


                throw new VoteRuleException($this->lastErrorCode, $this->message);

            }

        }


        $index = $subject->getVotes()->getByUserIndex();

        if (!$index->containsKey($object->getId()))  {
            return true;
        }

        $indexElement = $index->get($object->getId());
        $lastVotingDate = $indexElement->getLastVotingDate();

        if (!$lastVotingDate) {
            return true;
        }

        $diff = DateAndTime::getTimeAgo('now', $lastVotingDate);
        $hours = DateAndTime::convertTimeTo($diff);
        if ($hours >= $this->repeatedVotingFrequency) {
            return true;
        }

        $this->message = 'You can not change the reputation of the same user so often';
        $this->lastErrorCode = 'repeated_vote_for_user_so_often';

        throw new VoteRuleException($this->lastErrorCode, $this->message, [
            'repeated_voting_frequency' => $this->repeatedVotingFrequency,
            'passed' => round($hours, 2)
        ]);
    }

    public function hit(Author $subject, Author $object, $relatedEntity = null)    {

        $indexElement = new IndexElement();
        $indexElement->setLastVotingDate(new \DateTime());

        $subject->getVotes()->getByUserIndex()->set($object->getId(), $indexElement);

        if ($relatedEntity) {
            $subject->getVotes()->getByEntityIndex()->set($relatedEntity->getId(), $indexElement);
        }

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