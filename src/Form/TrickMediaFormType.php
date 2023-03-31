<?php

namespace App\Form;

use App\Entity\TrickMedia;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TrickMediaFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('type', ChoiceType::class, [
                'choices' => [
                    'image' => 'image',
                    'iframe' => 'iframe',
                ],
            ])
            ->add('title')
            ->add('url', UrlType::class, [
                'required' => false,
                "label" => "URL ",
                'mapped' => false,
            ])
            ->add('file', FileType::class, [
                'required' => false,
                "label" => "File",
                'mapped' => false,
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Add media',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => TrickMedia::class,
        ]);
    }
}
