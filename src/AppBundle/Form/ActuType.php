<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class ActuType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', 'text', array(
                    "label" => "Titre"
                ))
            ->add('content', 'textarea', array(
                    "label" => "Contenu"
                ))
            ->add('excerpt', null, array(
                    "label" => "Résumé",
                    "attr" => array(
                            "class" => "yo"
                        )
                ))
            ->add('isPublished', "choice", array(
                    "choices" => array(
                            true => "Publié",
                            false => "Brouillon"
                        ),
                    "multiple" => false,
                    "expanded" => true,
                    "label" => "Statut"
                ))
            ->add('Go', 'submit')
        ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Actu'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'appbundle_actu';
    }
}
