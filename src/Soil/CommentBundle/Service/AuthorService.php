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
     * @var Discoverer
     */
    protected $discoverer;

    public function __construct($dm, $validator, Discoverer $discoverer)    {
        $this->dm = $dm->getManager();
        $this->validator = $validator;
        $this->discoverer = $discoverer;
    }

    public function factory()   {
        $entity = new Author();

        return $entity;
    }

    public function discover(Author $author)       {
        $this->discoverer->discover($author->getAuthorURI());
        $author->setAvatarURL($this->discoverer->getImage());

        $author->setName($this->discoverer->getProfileName());
        $author->setSurname($this->discoverer->getProfileSurname());

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