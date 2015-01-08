<?php

namespace Soil\CommentBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction($name)
    {
        return $this->render('SoilCommentBundle:Default:index.html.twig', array('name' => $name));
    }
}
