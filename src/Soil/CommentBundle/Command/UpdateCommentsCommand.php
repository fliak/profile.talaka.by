<?php
/**
 * Created by PhpStorm.
 * User: fliak
 * Date: 27.7.15
 * Time: 23.28
 */

namespace Soil\CommentBundle\Command;


use EasyRdf\Graph;
use EasyRdf\Literal\DateTime;
use Soilby\EventComponent\Service\EventLogger;
use Soilby\EventComponent\Service\GearmanClient;
use Symfony\Component\Console\Command\Command;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;


class UpdateCommentsCommand extends Command  {

    /**
     * @var EventLogger
     */
    protected $eventLogger;

    protected $dm;

    protected $urinator;

    public function __construct($dm)   {
        $this->dm = $dm;

        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->setName('comments:update')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $context = $this->eventLogger->getUrinator()->getRouter()->getContext();
        $context->setHost('profile.talaka.soil.by');
        $context->setScheme('http');
//        $context->setBaseUrl('/');

        $from = new \DateTime('2015-07-27 01:00:00');

        $this->outputConfig = [
            'output_rdf_format' => 'ntriples',
            'queue_stream_name' => 'talaka_event'
        ];

        $graph = new Graph();

        $rep = $this->dm->getRepository('Soil\CommentBundle\Entity\Comment');
        $comments = $rep->findBy([
            'creationDate' => ['$gt' => $from]
        ]);

        var_dump($from);

var_dump(count($comments));
        foreach ($comments as $comment) {
            $targetURI = $this->urinator->generateURI($comment);

            $commentRes = $graph->resource($targetURI, 'tal:Comment');
            $commentRes->addLiteral('tal:creationDate', new DateTime($comment->getCreationDate()));
            $commentRes->addResource('tal:author', $comment->getAuthorURI());
            $commentRes->addResource('tal:relatedObject', $comment->getEntityURI());
            if ($comment->getParent()) {
                $commentRes->addResource('tal:parent', $this->urinator->generateURI($comment->getParent()));
            }

        }

        echo $graph->dump('text');
        $this->eventLogger->getLogCarrier()->setOutputConfig([
        'output_rdf_format' => 'ntriples',
        'queue_stream_name' => 'talaka_event'
        ]);
        var_dump($this->eventLogger->getLogCarrier()->send($graph));

    }

    /**
     * @param mixed $eventLogger
     */
    public function setEventLogger($eventLogger)
    {
        $this->eventLogger = $eventLogger;
        $this->urinator = $this->eventLogger->getUrinator();
    }







} 