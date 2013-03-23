<?php

namespace Church\MakeItHappenBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class DonateType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        
        $builder->add('name', 'text', array(
          'attr' => array(
            'data-stripe' => 'name',
          ),
        ));
        
        $builder->add('email', 'email');
        
        $builder->add('amount', 'text', array(
          'label' => 'Donation Amount',
          'required' => TRUE,
        ));
        
        $builder->add('note', 'textarea', array(
          'label' => 'Additional Comments',
        ));
                
        $builder->add('stripe_token', 'hidden');
        
    }
    
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Church\MakeItHappenBundle\Form\Model\Donate'
        ));
    }

    public function getName()
    {
        return 'donate';
    }
}