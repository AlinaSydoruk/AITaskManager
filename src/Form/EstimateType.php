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
            ->add('days', IntegerType::class, [
                'label' => 'task.approximate_estimate_days',
                'attr' => [
                    'class' => 'py-2.5 px-4 w-full ',
                    'min' => 0
                ],
                'row_attr' => ['class' => ' mt-2 '],

            ])
            ->add('time', TimeType::class, [
                'label' => 'task.approximate_estimate_hours',
                'input' => 'datetime',
                'widget' => 'single_text',
                'attr' => ['class' => 'border-2 py-2.5 px-4 w-36'],
                'row_attr' => ['class' => 'w-full flex flex-col mt-2 items-end text-right'],
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