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
use Symfony\Component\Validator\Constraints\GreaterThanOrEqual;
use Symfony\Component\Validator\Constraints\NotBlank;

class AbsenceFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('startDate', DateTimeType::class, [
                'label' => 'From date',
                'row_attr' => ['class' => 'relative z-0 w-full mb-5 group'],
                'label_attr' => [
                    'class' =>' text-sm text-gray-600 '
                ],
                'attr' => [
                    'class' => ' py-2.5 px-3 w-full text-sm text-gray-900 bg-transparent border-0 border-b-2 border-gray-600 focus:outline-none focus:ring-0  ',
                    'placeholder' => ' ',

                ],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Please enter the first absence day',
                    ]),
                    new GreaterThanOrEqual([
                        'value' => 'today',
                        'message' => 'The start date cannot be earlier than today',
                    ]),
                ],
            ])
            ->add('endDate', DateTimeType::class, [
                'label' => 'To date',
                'row_attr' => ['class' => 'relative z-0 w-full mb-5 group'],
                'label_attr' => [
                    'class' =>' text-sm text-gray-600 '
                ],
                'attr' => [
                    'class' => ' py-2.5 px-3 w-full text-sm text-gray-900 bg-transparent border-0 border-b-2 border-gray-600 focus:outline-none focus:ring-0  ',
                    'placeholder' => ' ',

                ],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Please enter the last absence day',
                    ]),
                    new GreaterThanOrEqual([
                        'propertyPath' => 'parent.all[startDate].data',
                        'message' => 'The end date cannot be earlier than start date',
                    ]),
                ],
            ])
            ->add('absenceType', EnumType::class, [
                'class' => AbsenceType::class,
                'row_attr' => ['class' => 'relative z-0 w-full mb-5 group'],
                'label_attr' => [
                    'class' =>' text-sm text-gray-600 '
                ],
                'attr' => [
                    'class' => ' py-2.5 px-3 w-full text-sm text-gray-900 bg-transparent border-0 border-b-2 border-gray-600 focus:outline-none focus:ring-0  ',
                    'placeholder' => ' ',

                ],
                'label' => 'Absence type',
                'constraints' => [
                    new NotBlank([
                        'message' => 'Please choose the absence type',
                    ]),
                ],
            ])
            ->add('comment', TextareaType::class, [
                'row_attr' => ['class' => 'relative z-0 w-full mb-5 group'],
                'label_attr' => [
                    'class' =>' text-sm text-gray-600 '
                ],
                'attr' => [
                    'class' => ' py-2.5 px-3 w-full text-sm text-gray-900 bg-transparent border-2 border-gray-600 rounded-md focus:outline-none focus:ring-0  ',
                    'placeholder' => ' ',

                ],
                'label' => 'Comment',
                'required'   => false,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(['data_class' => Absence::class]);
    }
}
