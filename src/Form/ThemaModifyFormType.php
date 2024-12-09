<?php

namespace App\Form;

use App\Entity\Thema;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class ThemaModifyFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'attr' => ['class' => 'form-control'],
                'label' => "Nom du thème :"
            ])
            ->add('update', Submittype::class, [
                'label' => "Modifier",
                'attr' => ['style' => 'width: 200px;border:solid #82b864 2px;color:#82b864;height:40px;border-radius:5px;']
                
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Thema::class,
        ]);
    }
}
