<?php

/**
 * This file is part of the Engage360d package bundles.
 *
 */

namespace Engage360d\Bundle\TakedaUserBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class PostUserFormType extends AbstractType
{
    private $class;

    public function __construct($class)
    {
        $this->class = $class;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('firstname', 'text', array('description' => 'User firstname'))
            ->add('lastname', 'text', array('description' => 'User lastname'))
            ->add('email', 'email', array('label' => 'form.email', 'translation_domain' => 'FOSUserBundle'))
            ->add('plainPassword', 'repeated', array(
                'type' => 'password',
                'options' => array('translation_domain' => 'FOSUserBundle'),
                'first_options' => array('label' => 'form.password'),
                'second_options' => array('label' => 'form.password_confirmation'),
                'invalid_message' => 'fos_user.password.mismatch',
            ))
            ->add('birthday', 'date', array('description' => 'User birthday', 'widget' => 'single_text'))
            ->add('region', 'text', array('description' => 'User region'))
            ->add('doctor', 'checkbox', array('description' => 'User doctor label'))
            ->add('specialization', 'text', array('description' => 'User doctor specialization'))
            ->add('experience', 'number', array('description' => 'User doctor experience'))
            ->add('address', 'text', array('description' => 'User doctor address'))
            ->add('phone', 'text', array('description' => 'User doctor phone'))
            ->add('institution', 'text', array('description' => 'User doctor institution'))
            ->add('graduation', 'date', array(
                'description' => 'User doctor graduation',
                'widget' => 'single_text'
            ))
        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => $this->class,
            'intention'  => 'registration',
        ));
    }

    public function getName()
    {
        return 'engage360d_takeda_user_post_user';
    }
}
