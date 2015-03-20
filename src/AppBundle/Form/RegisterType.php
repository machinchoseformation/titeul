<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class RegisterType extends AbstractType
{

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $builder
            ->add('username', null, array(
                    "label" => "Pseudo"
                ))
            ->add('email', 'email')
            ->add('plainPassword', 'repeated', array(
                    'type' => 'password',
                    'invalid_message' => 'Les mots de passe ne concordent pas.',
                    'first_options' => array(
                        'label' => "Mot de passe"
                    ),
                    'second_options' => array(
                        'label' => "Encore"
                    )
                ))
            ->add('Inscription !', 'submit')
        ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'validation_groups' => array('registration'),
            'data_class' => 'AppBundle\Entity\User'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'appbundle_register';
    }
}
