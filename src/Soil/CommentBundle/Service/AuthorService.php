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
use Soil\DiscoverBundle\Service\Resolver;
use Soil\DiscoverBundle\Services\Discoverer;

class AuthorService {
    /**
     * @var DocumentManager
     */
    protected $dm;

    /**
     * @var
     */
    protected $validator;

    /**
     * @var Resolver
     */
    protected $resolver;

    public function __construct($dm, $validator, Resolver $resolver)    {
        $this->dm = $dm->getManager();
        $this->validator = $validator;
        $this->resolver = $resolver;
    }

    public function factory()   {
        $entity = new Author();

        return $entity;
    }

    public function discover(Author $author)       {
        $agentEntity = $this->resolver->getEntityForURI($author->getAuthorURI(), 'Soil\DiscoverBundle\Entity\Agent');

        $author->setAvatarURL($agentEntity->img);

        $author->setName($agentEntity->firstName);
        $author->setSurname($agentEntity->lastName);

    }

    public function persist($entity)   {
        $this->dm->persist($entity);
    }

    public function getByURI($uri)  {
        return $this->getRepository()->findOneBy([
            'author_uri' => $uri
        ]);
    }

    public function getRepository()  {
        return $this->dm->getRepository('Soil\CommentBundle\Entity\Author');
    }
} 