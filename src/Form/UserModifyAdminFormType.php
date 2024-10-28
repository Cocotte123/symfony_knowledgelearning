<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Constraints\IsTrue;


class UserModifyAdminFormType extends AbstractType
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
            ->add('isActivated', CheckboxType::class, [
                //'attr' => ['class' => 'tinymce'],
                'label' => "Activé :"
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
