<?php
/**
 * Created by PhpStorm.
 * User: fliak
 * Date: 26.6.15
 * Time: 10.59
 */

namespace Soil\CommentBundle\Service\Exception;


class DiscoverException extends \Exception {

    public function __construct($message, $previous = null)    {
        parent::__construct($message, 0, $previous);
    }

} 