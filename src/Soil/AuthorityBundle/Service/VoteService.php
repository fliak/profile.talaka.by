<?php
/**
 * Created by PhpStorm.
 * User: fliak
 * Date: 23.6.15
 * Time: 21.21
 */

namespace Soil\AuthorityBundle\Service;


use Doctrine\ODM\MongoDB\DocumentManager;
use Soil\AuthorityBundle\Entity\Vote;
use Soil\CommentBundle\Service\AuthorService;


class VoteService {

    /**
     * @var DocumentManager
     */
    protected $dm;

    protected $errors;

    /**
     * @var AuthorService
     */
    protected $agentService;

    /**
     * @var
     */
    protected $validator;

    public function __construct($dm, $validator)    {
        $this->dm = $dm->getManager();
        $this->validator = $validator;
    }

    /**
     * @return Vote
     */
    public function factory()   {
        $entity = new Vote();

        return $entity;
    }

    public function hydrate($entity, $data) {
        $hydrator = $this->dm->getHydratorFactory();
        $hydrator->hydrate($entity, $data);
    }

    public function isValid($entity)    {
        $this->errors = $this->validator->validate($entity);

        if (count($this->errors) > 0) {
            return false;
            //FIXME: write log for debug mode
        }

        return true;
    }

    public function getLastValidatorViolations()    {
        return $this->errors;
    }

    public function persist($entity)   {
        $this->dm->persist($entity);
    }

    public function getDM() {
        return $this->dm;
    }

    /**
     * @param $id
     * @return Vote
     */
    public function getById($id)  {
        return $this->getRepository()->find($id);
    }

    public function getRepository()  {
        return $this->dm->getRepository('Soil\AuthorityBundle\Entity\Vote');
    }

    public function getQueryBuilder()   {
        return $this->dm->createQueryBuilder('Soil\AuthorityBundle\Entity\Vote');
    }

    /**
     * @param mixed $agentService
     */
    public function setAgentService($agentService)
    {
        $this->agentService = $agentService;
    }


    public function getPublicRepresentation(Vote $vote)  {

        $data = [
            'id' => $vote->getId(),
            'vote' => $vote->getVote(),
            'object_uri' => $vote->getObjectURI(),
            'voter_uri' => $vote->getVoterURI(),
            'voter' => $this->agentService->getPublicRepresentation($vote->getVoter()),
            'agent_uri' => $vote->getAgentURI(),
            'agent' => $this->agentService->getPublicRepresentation($vote->getAgent()),
            'message' => $vote->getMessage(),
            'creationDate' => $vote->getCreationDate()
        ];

        return $data;
    }

}