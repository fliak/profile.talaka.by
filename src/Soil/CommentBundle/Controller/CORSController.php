<?php
/**
 * Created by PhpStorm.
 * User: fliak
 * Date: 15.1.15
 * Time: 9.25
 */

namespace Soil\CommentBundle\Controller;


use Symfony\Component\HttpFoundation\Response;

class CORSController {

    public function optionsAction() {
        $response = new Response();
        $response->headers->add([
            'Access-Control-Allow-Origin' => '*',
            'Access-Control-Allow-Methods' => 'GET, POST, PUT, DELETE',
            'Content-Type' => 'text/html; charset=utf-8',
            'Access-Control-Max-Age' => 10,
            'Access-Control-Allow-Headers' => 'Content-Type, Access, Accept'
        ]);

        return $response;
    }
} 