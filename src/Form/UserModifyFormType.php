<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;


class UserModifyFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            
            ->add('lastName', TextType::class, [
                'attr' => ['class' => 'form-control'],
                'label' => "Nom :"
            ])
            ->add('firstName', TextType::class, [
                'attr' => ['class' => 'form-control'],
                'label' => "Prénom :"
            ])
            ->add('update', Submittype::class, [
                'label' => "Modifier",
                'attr' => ['style' => 'width: 200px;border:solid #384050 1px;background-color:#384050; color:#f1f8fc;height:40px;border-radius:5px;']
                
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
