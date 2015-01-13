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
use Soil\CommentBundle\Service\Exception\AmbiguityException;
use Soil\CommentBundle\Service\Exception\EntityMissing;

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

    /**
     * @param $uri
     * @return Entity
     */
    public function getByURI($uri)  {
        return $this->getRepository()->findOneBy([
            'entity_uri' => $uri
        ]);
    }

    public function getRepository()  {
        return $this->dm->getRepository('Soil\CommentBundle\Entity\Entity');
    }

    public function getQueryBuilder()   {
        return $this->dm->createQueryBuilder('Soil\CommentBundle\Entity\Entity');
    }

    protected function getRawEntity($entityURI) {
        $query = $this->getQueryBuilder()
            ->select('comments')
            ->field('entity_uri')->equals($entityURI)
            ->eagerCursor(true)->hydrate(false)
            ->getQuery();

        $cursor = $query->execute();
        $entitiesCount = $cursor->count();

        switch (true)   {
            case $entitiesCount === 0:
                throw new EntityMissing('Entity is missing');

            case $entitiesCount > 1:
                throw new AmbiguityException('Ambiguity detected, more than one entity with provided URI');

            default:
        }

        return $cursor->getSingleResult();

    }

    public function getCommentsList($entityURI) {
        $data = $this->getRawEntity($entityURI);

        return array_key_exists('comments', $data) ? $data['comments'] : [];
    }

    public function getDirtyCommentsList($entityURI) {
        $data = $this->getRawEntity($entityURI);

        return array_key_exists('dirty_comments', $data) ? $data['dirty_comments'] : [];
    }
} 