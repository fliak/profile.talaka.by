<?php
/**
 * Created by PhpStorm.
 * User: fliak
 * Date: 5.2.15
 * Time: 0.39
 */

namespace Soil\CommentBundle\Service;


use Soil\CommentBundle\Entity\Comment;
use Soilby\EventComponent\Service\UrinatorInterface;
use Symfony\Bundle\FrameworkBundle\Routing\Router;

class URInator implements UrinatorInterface {

    /**
     * @var Router
     */
    protected $router;

    public function __construct($router)   {
        $this->router = $router;
    }

    public function generateURI($entity)    {
        if (is_scalar($entity)) {
            return $entity; //return string directly
        }

        switch (true)   {
            case $entity instanceof Comment:
                $uri = $this->router->generate('soil_comment_get', [
                    'id' => $entity->getId()
                ], true);

                break;

            default:
                if ($entity)    {
                    $entityClass = get_class($entity);
                }
                else    {
                    $entityClass = 'NULL';
                }

                throw new \Exception("Entity of type `$entityClass` is not supported by provided URInator");
        }

        return $uri;
    }

} 