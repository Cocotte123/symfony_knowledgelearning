<?php

namespace App\Form;

use App\Entity\Cursus;
use App\Entity\Thema;
use App\Repository\ThemaRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class CursusRegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'attr' => ['class' => 'form-control'],
                'label' => "Nom du cursus :"
            ])
            ->add('level', ChoiceType::class, [
                'label' => "Niveau :",
                'choices'  => [
                    'Initiation' => "d'initiation",
                    'Approfondissement' => "d'approfondissement",
                    'Expertise' => "d'expertise",
                ],
            ])
            ->add('price', MoneyType::class, [
                'attr' => ['class' => 'form-control'],
                'label' => "Prix :",
            ])
            ->add('thema', EntityType::class,[
                'class'=> Thema::class,
                'choice_label'=> 'name',
                'label' => "Thème :",
                'required' => true,
                'query_builder' => function (ThemaRepository $themaRepository){return $themaRepository->createQueryBuilder('t')->orderBy('t.name','ASC'); },
               
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Cursus::class,
        ]);
    }
}
