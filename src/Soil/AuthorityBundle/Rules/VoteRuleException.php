<?php
/**
 * Created by PhpStorm.
 * User: fliak
 * Date: 29.6.15
 * Time: 21.44
 */

namespace Soil\AuthorityBundle\Rules;


class VoteRuleException extends \Exception {

    protected $params;
    protected $errorCode;

    /**
     * @param string $code
     * @param string $message
     * @param array $params
     * @param \Exception $previous
     */
    public function __construct($code, $message, $params = [], $previous = null)    {
        parent::__construct($message, 0, $previous);

        $this->errorCode = $code;
        $this->params = $params;

    }

    /**
     * @return string
     */
    public function getErrorCode()
    {
        return $this->errorCode;
    }

    /**
     * @return array
     */
    public function getParams()
    {
        return $this->params;
    }


} 