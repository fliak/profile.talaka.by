<?php

namespace Soil\CommentBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

class DefaultController extends Controller
{
    public function indexAction($name)
    {
        $service = $this->get('soil_comment.service.comment');
        $comment = $service->getById('54be8aa1138841a73b8b4567');

        $logger = $this->get('soil_event.service.event_logger');
        $logger->raiseComment($comment, $comment->getAuthorURI(), $comment->getEntityURI());

        $log = $logger->getRDFQueue();
        $logger->flush();

        return new Response($log, 200, ['Content-type' => 'application/json']);
    }
}
