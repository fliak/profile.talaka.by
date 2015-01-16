<?php
/**
 * Created by PhpStorm.
 * User: fliak
 * Date: 10.1.15
 * Time: 23.32
 */

namespace Soil\DiscoverBundle\Services\Exception;


use Buzz\Message\Response;

class DownloadException extends \Exception {


    /**
     * @var Response
     */
    protected $response;

    /**
     * @return Response
     */
    public function getResponse()
    {
        return $this->response;
    }

    /**
     * @param mixed $response
     */
    public function setResponse(Response $response)
    {
        $this->response = $response;
    }


} 