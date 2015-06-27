<?php
/**
 * Created by PhpStorm.
 * User: fliak
 * Date: 26.6.15
 * Time: 11.04
 */

namespace Soil\CommentBundle\Service\Exception;


class WrongAgentException extends \Exception {
    public function __construct($message, $previous = null)    {
        parent::__construct($message, 0, $previous);
    }
} 