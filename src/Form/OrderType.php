<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use App\Entity\Address;
use App\Entity\Carrier;

class OrderType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->setAction($options['action'])
            ->add('addresses', EntityType::class, [
                'label' => false,
                'required' => true,
                'class' => Address::class,
                'choices' => $options['user']->getAddresses(),
                'multiple' => false,
                'expanded' => true
            ])
            ->add('carriers', EntityType::class, [
                'label' => 'Choisissez votre adresse transporteur',
                'required' => true,
                'class' => Carrier::class,
                'multiple' => false,
                'expanded' => true
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Valider ma commande',
                'attr' => [
                    'class' => 'btn btn-success btn-block'
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'user' => [],
            'action' => ""
        ]);
    }
}
