<?php
namespace Soil\CommentBundle\Form;

/**
 * Created by PhpStorm.
 * User: fliak
 * Date: 16.1.15
 * Time: 14.31
 */




use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class CommentPushForm extends AbstractType {

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('parent')
            ->add('author_uri')
            ->add('entity_uri')
            ->add('message');
    }

    public function getName()
    {
        return 'CommentPush';
    }

} 