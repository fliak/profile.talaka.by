<?php
/**
 * Created by PhpStorm.
 * User: fliak
 * Date: 15.1.15
 * Time: 16.30
 */

namespace Soil\CommentBundle\Controller\Exception;


class IsNotValidException extends \Exception {

    /**
     * @var array
     */
    protected $violations;

    public function __construct($violations, $message = 'Data invalid', $previous = null) {
        $this->violations = $violations;

        parent::__construct($message, 0, $previous);
    }

    public function getViolations() {
        return $this->violations;
    }

    public function getViolationsAsArray()   {
        $data = [];
        foreach ($this->violations as $violation)   {
            $data[] = (string) $violation;
        }

        return $data;
    }

    public function getViolationsAsString() {
        return (string)$this->violations;
    }
} 