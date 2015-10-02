<?php
/**
 * Created by PhpStorm.
 * User: fliak
 * Date: 5.2.15
 * Time: 0.39
 */

namespace Soil\CommentBundle\Service;


use Soil\AuthorityBundle\Entity\Vote;
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

    /**
     * @return Router
     */
    public function getRouter()
    {
        return $this->router;
    }



    public function assemble($routeName, $params = [])  {
        if (is_scalar($params)) {
            $params = ['id' => $params];
        }

        return $this->router->generate($routeName, $params, true);
    }

    public function generateURI($entity)    {
        if (is_scalar($entity)) {
            return $entity; //return string directly
        }

        switch (true)   {
            case $entity instanceof Comment:
                $uri = $this->assemble('soil_comment_get', $entity->getId());

                break;

            case $entity instanceof Vote:
                $uri = $this->assemble('soil_authority_get', $entity->getId());

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