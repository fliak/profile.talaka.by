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


class RebuildAuthorsCommand extends Command  {

    protected $authorService;

    public function __construct($authorService)   {
        $this->authorService = $authorService;

        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->setName('authors:rebuild')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        try {
            $authors = $this->authorService->getRepository()->findAll();

            $output->writeln(count($authors) . ' authors to process');

            foreach ($authors as $author) {
                $this->authorService->discover($author);
                $output->writeln($author->getId() . ' ..done');
            }

            $this->authorService->getRepository()->getDocumentManager()->flush();
        }
        catch (\Exception $e)   {
            $output->writeln((string)$e);
        }

    }


}