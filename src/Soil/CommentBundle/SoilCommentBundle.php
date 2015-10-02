<?php

namespace Soil\CommentBundle;

use EasyRdf\RdfNamespace;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class SoilCommentBundle extends Bundle
{

    public function boot()  {
        parent::boot();

        $namespaces = $this->container->getParameter('semantic_namespaces');

        foreach ($namespaces as $namespace => $uri) {
            RdfNamespace::set($namespace, $uri);
        }
    }

}
