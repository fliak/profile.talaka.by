<?php
/**
 * Created by PhpStorm.
 * User: fliak
 * Date: 20.1.15
 * Time: 15.29
 */

namespace Soil\CommentBundle\Controller\CORS;


use Soil\CommentBundle\Controller\CORSController;

trait CORSTraitForController {
    /**
     * @var CORSController
     */
    protected $corsController;


    public function setCorsController(CORSController $controller)   {
        $this->corsController = $controller;
    }

} 