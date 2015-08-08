<?php

namespace NameRankBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class NameType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name')
            ->add('isMale', 'checkbox', ['required' => false])
            //->add('rank')
            //->add('numberOfComparisons')
        ;

        $builder->add('save', 'submit', array('label' => 'Add Name'));
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'NameRankBundle\Entity\Name'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'namerankbundle_name';
    }
}
