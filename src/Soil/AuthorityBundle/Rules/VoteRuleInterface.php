<?php
/**
 * Created by PhpStorm.
 * User: fliak
 * Date: 27.6.15
 * Time: 21.09
 */

namespace Soil\AuthorityBundle\Rules;


use Soil\CommentBundle\Entity\Author;

interface VoteRuleInterface {

    public function check(Author $subject, Author $object, $relatedEntity = null);

    public function hit(Author $subject, Author $object, $relatedEntity = null);

    public function getLastMessage();


    /**
     * @return string
     */
    public function getLastErrorCode();

} 