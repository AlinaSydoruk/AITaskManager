<?php

namespace App\Form;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TimeType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
class EstimateType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('hours', IntegerType::class, [
                'label' => 'hours',
                'required' => false,
                'attr' => [
                    'min' => 0,
                    'class' => 'py-2.5 px-4 w-24'
                ],
                'row_attr' => ['class' => 'mb-2']
            ])
            ->add('minutes', IntegerType::class, [
                'label' => 'minutes',
                'required' => false,
                'attr' => [
                    'min' => 0,
                    'max' => 59,
                    'class' => 'py-2.5 px-4 w-24'
                ],
                'row_attr' => ['class' => 'mb-2']
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'mapped' => false,
        ]);
    }

    public function getBlockPrefix(): string
    {
        return 'estimate';
    }
}