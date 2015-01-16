<?php
/**
 * Created by PhpStorm.
 * User: fliak
 * Date: 8.1.15
 * Time: 11.51
 */

namespace Soil\CommentBundle\Controller;


use Doctrine\ODM\MongoDB\Tests\HydratorTest;
use Doctrine\ORM\Internal\Hydration\ObjectHydrator;
use Soil\CommentBundle\Entity\Comment;
use Soil\CommentBundle\Service\AuthorService;
use Soil\CommentBundle\Service\CommentService;
use Soil\CommentBundle\Service\EntityService;
use Soil\CommentBundle\Service\Exception;
use Soil\DiscoverBundle\Services\Discoverer;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Templating\EngineInterface;

class DiscoverController {

    /**
     * @var Discoverer
     */
    protected $discoverer;

    /**
     * @var EngineInterface
     */
    protected $templating;


    public function __construct(Discoverer $discoverer, EngineInterface $templating) {
        $this->discoverer = $discoverer;
        $this->templating = $templating;
    }


    public function discoverAction($entity_uri)    {
        $this->discoverer->discover($entity_uri);

        return new Response($this->templating->render(
            'SoilCommentBundle:Discover:show.html.twig', ['discoverer' => $this->discoverer]
        ));
    }

} 