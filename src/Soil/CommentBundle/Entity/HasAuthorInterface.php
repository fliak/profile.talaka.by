<?php
/**
 * Created by PhpStorm.
 * User: fliak
 * Date: 26.6.15
 * Time: 11.39
 */

namespace Soil\CommentBundle\Entity;



interface HasAuthorInterface {


    /**
     * @return Author
     */
    public function getAuthor();

} 