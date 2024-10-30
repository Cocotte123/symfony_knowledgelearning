<?php

namespace App\Form;

use App\Entity\Lesson;
use App\Entity\Cursus;
use App\Repository\CursusRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;

class LessonRegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'attr' => ['class' => 'form-control'],
                'label' => "Nom de la leçon :"
            ])
            ->add('number', IntegerType::class, [
                'attr' => ['class' => 'form-control'],
                'label' => "Numéro :"
            ])
            ->add('price', MoneyType::class, [
                'attr' => ['class' => 'form-control'],
                'label' => "Prix :",
            ])
            ->add('video', UrlType::class, [
                'attr' => ['class' => 'form-control'],
                'required' => false,
                'label' => "Lien vers la video :"
            ])
            ->add('text', TextareaType::class, [
                'attr' => ['class' => 'form-control'],
                'label' => "Leçon :"
            ])
            ->add('cursus', EntityType::class,[
                'class'=> Cursus::class,
                'choice_label'=> 'name',
                'label' => "Cursus :",
                'required' => true,
                'query_builder' => function (CursusRepository $cursusRepository){return $cursusRepository->createQueryBuilder('c')->orderBy('c.name','ASC'); },
               
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Lesson::class,
        ]);
    }
}
