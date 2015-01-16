<?php

namespace Soil\DiscoverBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction($name)
    {

        $d = $this->get('soil_discover.downloader');
        $response = $d->getDocument('http://talaka.by.local/user/1');

        $html = $response->getContent();

        $parser = $this->get('soil_discover.parser');
        $parser->parse($html);

        return $this->render('SoilDiscoverBundle:Default:index.html.twig', array('name' => $name));
    }
}
