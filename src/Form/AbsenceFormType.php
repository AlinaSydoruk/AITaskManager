<?php

namespace App\Form;

use App\Entity\Absence;
use App\Entity\AbsenceType;
use App\Entity\WorkStatus;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\EnumType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

class AbsenceFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('startDate', DateTimeType::class, [
                'label' => 'From date',
                'constraints' => [
                    new NotBlank([
                        'message' => 'Please enter the first absence day',
                    ]),
                ],
            ])
            ->add('endDate', DateTimeType::class, [
                'label' => 'To date',
                'constraints' => [
                    new NotBlank([
                        'message' => 'Please enter the last absence day',
                    ]),
                ],
            ])
            ->add('absenceType', EnumType::class, [
                'class' => AbsenceType::class,
                'label' => 'Absence type',
                'constraints' => [
                    new NotBlank([
                        'message' => 'Please choose the absence type',
                    ]),
                ],
            ])
            ->add('comment', TextareaType::class, [
                'label' => 'Comment',
                'required'   => false,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([]);
    }
}
