<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Regex;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class RegistrationFormType extends AbstractType
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
            ->add('email', EmailType::class, [
                'attr' => ['class' => 'form-control'],
                'label' => "Adresse mail :"
            ])
            //->add('agreeTerms', CheckboxType::class, [
            //    'mapped' => false,
            //    'constraints' => [
            //        new IsTrue([
            //            'message' => 'You should agree to our terms.',
            //        ]),
            //    ],
            //])
            ->add('plainPassword', PasswordType::class, [
                // instead of being set onto the object directly,
                // this is read and encoded in the controller
                'mapped' => false,
                'attr' => [
                    'autocomplete' => 'new-password',
                    'class' => 'form-control',
                ],
                'label' => "Mot de passe :",
                'constraints' => [
                    new NotBlank([
                        'message' => 'Merci de saisir un mot de passe',
                    ]),
                    new Length([
                        'min' => 12,
                        'minMessage' => 'Votre mot de passe doit avoir au moins {{ limit }} caractères',
                        // max length allowed by Symfony for security reasons
                        'max' => 20,
                        'maxMessage' => 'Votre mot de passe doit avoir au maximum {{ limit }} caractères',
                    ]),
                    //new Regex([
                    //    'htmlPattern' => "(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{12,20}",
                    //    'pattern' => "/[0-9]{1,}[a-z]{11,19}/i",
                    //])
                   
                ],
            ])
            ->add('save', Submittype::class, [
                'label' => "Enregistrer",
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
