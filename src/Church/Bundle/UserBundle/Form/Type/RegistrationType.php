<?php

namespace Church\Bundle\UserBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class RegistrationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $builder->add('name', 'text');
        $builder->add('email', 'email');
        $builder->add('username', 'text');
        $builder->add('password', 'password');
        $builder->add('address', 'text');
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Church\Bundle\UserBundle\Form\Model\RegistrationEmail'
        ));
    }

    public function getName()
    {
        return 'registration';
    }
}
