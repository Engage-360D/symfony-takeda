<?php

namespace Engage360d\Bundle\TakedaTestBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;


class TestResultType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('sex');
        $builder->add('birthday', null, array('widget' => 'single_text', 'input' => 'datetime'));
        $builder->add('growth');
        $builder->add('weight');
        $builder->add('smoking');
        $builder->add('cholesterolLevel');
        $builder->add('cholesterolDrugs');
        $builder->add('diabetes');
        $builder->add('sugarProblems');
        $builder->add('sugarDrugs');
        $builder->add('arterialPressure');
        $builder->add('arterialPressureDrugs');
        $builder->add('physicalActivity');
        $builder->add('heartAttackOrStroke');
        $builder->add('extraSalt');
        $builder->add('acetylsalicylicDrugs');
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Engage360d\Bundle\TakedaTestBundle\Entity\TestResult',
        ));
    }

    public function getName()
    {
        return 'testResult';
    }
}
