<?php
/**
 * Created by PhpStorm.
 * User: fliak
 * Date: 8.1.15
 * Time: 19.10
 */

namespace Soil\CommentBundle\Service;

use Doctrine\ODM\MongoDB\DocumentManager;
use Soil\CommentBundle\Entity\Author;

class AuthorService {
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
        $entity = new Author();

        return $entity;
    }

    public function persist($entity)   {
        $this->dm->persist($entity);
    }
} 