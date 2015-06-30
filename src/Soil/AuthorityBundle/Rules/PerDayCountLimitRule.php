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

class PerDayCountLimitRule implements VoteRuleInterface {

    protected $message = 'Vote day limit is over';

    protected $lastErrorCode = 'per_day_count_limit';

    /**
     * Period in hours
     * @var int
     */
    protected $period = 24;

    protected $voteCountPerPeriod = 3;

    public function check(Author $subject, Author $object, $relatedEntity = null)    {

        $lastVotingDate = $subject->getVotes()->getLastVotingDate();
        if (!$lastVotingDate)   return true;

        $diff = DateAndTime::getTimeAgo('now', $lastVotingDate);


        if (DateAndTime::convertTimeTo($diff) > $this->period)  {
            $subject->getVotes()->setVoteCountPerDay(0);

            return true;
        }
        else    {
            if ($subject->getVotes()->getVoteCountPerDay() < $this->voteCountPerPeriod)    {
                return true;
            }
            else    {
                throw new VoteRuleException($this->lastErrorCode, $this->message, [
                    'period' => $this->period,
                    'vote_count_per_period' => $this->voteCountPerPeriod
                ]);
            }
        }

    }

    public function hit(Author $subject, Author $object, $relatedEntity = null)    {
        $subject->getVotes()->incrementVoteCount();
        $subject->getVotes()->setLastVotingDate(new \DateTime());
        if ($subject->getVotes()->getVoteCountPerDay() > $this->voteCountPerPeriod) {
            $subject->getVotes()->setVoteCountPerDay(0);
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