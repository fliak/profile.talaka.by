<?php
/**
 * Created by PhpStorm.
 * User: fliak
 * Date: 8.1.15
 * Time: 17.02
 */

namespace Soil\CommentBundle\Service;


use Doctrine\ODM\MongoDB\DocumentManager;
use Soil\CommentBundle\Entity\Comment;

class CommentService {

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
        $entity = new Comment();
        $entity->setCreationDate(new \DateTime());

        return $entity;
    }

    public function hydrate($entity, $data) {
        $hydrator = $this->dm->getHydratorFactory();
        $hydrator->hydrate($entity, $data);
    }

    public function isValid($entity)    {
        $errors = $this->validator->validate($entity);
        if (count($errors) > 0) {
            var_dump($errors);
            return false;
            //FIXME: write log for debug mode
        }

        return true;
    }

    public function persist($entity)   {
        $this->dm->persist($entity);
    }

    public function getDM() {
        return $this->dm;
    }
} 