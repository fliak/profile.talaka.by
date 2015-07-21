<?php
/**
 * Created by PhpStorm.
 * User: fliak
 * Date: 5.2.15
 * Time: 0.39
 */

namespace Soil\CommentBundle\Service;


use EasyRdf\RdfNamespace;
use Soil\AuthorityBundle\Entity\Vote;
use Soil\CommentBundle\Entity\Author;
use Soil\CommentBundle\Entity\Comment;
use Soilby\EventComponent\Service\UrinatorInterface;
use Symfony\Bundle\FrameworkBundle\Routing\Router;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class DeURInator implements ContainerAwareInterface {

    /**
     * @var ContainerInterface
     */
    protected $container;

    public function setContainer(ContainerInterface $container = null)  {
        $this->container = $container;
    }

    public function parseUri($uri) {
        $iri = RdfNamespace::shorten($uri);

        if (!$iri)    return null;

        $colonPos = strpos($iri, ':');
        $namespace = substr($iri, 0, $colonPos);
        $uniquePart = substr($iri, $colonPos + 1);

        switch(true)    {
            case strpos($namespace, 'talcom') === 0:
                return [
                    'type' => $namespace,
                    'class' => Comment::class,
                    'id' => $uniquePart
                ];

            case strpos($namespace, 'talagent') === 0:
                return [
                    'type' => $namespace,
                    'class' => Author::class,
                    'id' => $uniquePart
                ];

            default:
                return [
                    'type' => $namespace,
                    'class' => null,
                    'id' => $uniquePart
                ];
        }
    }


    /**
     * @param $class
     *
     * @throws \Exception
     *
     *
     * @return Object
     */
    public function getEntityProvider($class) {

        switch ($class) {
            case Comment::class:
                return $this->container->get('soil_comment.service.comment');

            case Author::class:
                return $this->container->get('soil_comment.service.author');

            default:
                throw new \Exception("Class `$class` didn't supported");
        }

    }

    /**
     * @param $uri
     * @throws \Exception
     *
     * @return object
     */
    public function getEntity($uri) {
        $parsed = $this->parseUri($uri);

        if ($parsed && $parsed['class']) {
            $id = $parsed['id'];
            $class = $parsed['class'];

            $provider = $this->getEntityProvider($class);

            switch ($class) {
                case Comment::class:
                    return $provider->loadById($id);

                case Author::class:
                    return $provider->getByURI($uri);

                default:
                    throw new \Exception("Class `$class` didn't supported");
            }
        }
        else    {
            $entityService = $this->container->get('soil_comment.service.entity');
            return $entityService->getByURI($uri);
        }

    }


} 