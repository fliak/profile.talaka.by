<?php
/**
 * Created by PhpStorm.
 * User: fliak
 * Date: 8.1.15
 * Time: 19.10
 */

namespace Soil\CommentBundle\Service;


use Doctrine\ODM\MongoDB\DocumentManager;
use Soil\CommentBundle\Entity\Entity;

class EntityService {
    /**
     * @var DocumentManager
     */
    protected $dm;

    /**
     * @var
     */
    protected $validator;

    public function __construct($dm, $validator)    {
        $this->dm = $dm->getManager();
        $this->validator = $validator;
    }

    public function factory()   {
        $entity = new Entity();

        return $entity;
    }

    public function persist($entity)   {
        $this->dm->persist($entity);
    }

    public function getByURI($uri)  {
        return $this->getRepository()->findOneBy([
            'entity_uri' => $uri
        ]);
    }

    public function getRepository()  {
        return $this->dm->getRepository('Soil\CommentBundle\Entity\Entity');
    }
} 