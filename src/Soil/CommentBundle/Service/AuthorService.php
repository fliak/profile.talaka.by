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
use Soil\CommentBundle\Service\Exception\DiscoverException;
use Soil\CommentBundle\Service\Exception\WrongAgentException;
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
        try {
            $uri = $author->getAuthorURI();
            $agentEntity = $this->resolver->getEntityForURI($uri, 'Soil\DiscoverBundle\Entity\Agent');

        }
        catch(\Exception $e)    {
            throw new DiscoverException("Cannot discover entity`$uri`", $e);
        }

        $author->setAvatarURL((string)$agentEntity->getImg());

        $author->setName((string)$agentEntity->getFirstName());
        $author->setSurname((string)$agentEntity->getLastName());

    }

    public function persist($entity)   {
        $this->dm->persist($entity);
    }

    public function getByURI($uri, $createIfNotExist = false)  {
        $agent = $this->getRepository()->findOneBy([
            'author_uri' => $uri
        ]);

        if (!$agent && $createIfNotExist) {
            $agent = $this->factory();
            $agent->setAuthorURI($uri);

            try {
                $this->discover($agent);
            }
            catch (DiscoverException $e)    {
                throw new WrongAgentException("Provided agent URI cannot be discovered", $e);
            }


            $this->persist($agent);
        }


        return $agent;
    }

    public function getRepository()  {
        return $this->dm->getRepository('Soil\CommentBundle\Entity\Author');
    }

    public function getPublicRepresentation(Author $agent) {
        $data = [
            'id'      => $agent->getId(),
            'avatar'  => $agent->getAvatarURL(),
            'name'    => $agent->getName(),
            'surname' => $agent->getSurname(),
        ];

        return $data;
    }
} 