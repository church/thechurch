<?php

namespace Church\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class NameType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $builder->add('first_name', 'text', array(
          'label' => 'First Name',
        ));

        $builder->add('last_name', 'text', array(
          'label' => 'Last Name',
        ));

    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Church\Form\Model\Name'
        ));
    }

    public function getName()
    {
        return 'name';
    }
}
