<?php

namespace App\Form;

use App\Entity\Absence;
use App\Entity\AbsenceType;
use App\Entity\Employee;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\EnumType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

class AbsenceFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('startDate', DateType::class, [
                'label' => 'absence.start_date',
            ])
            ->add('isStartDateHalfDay', ChoiceType::class, [
                'label' => ' ',
                'choices'=>[
                    'absence.morning' =>false,
                    'absence.midday' =>true
                ],
                'multiple' => false,
                'choice_translation_domain' => 'validators',
                'attr' => ['class' => 'border-2 '],
                'row_attr' =>['class' => 'flex items-end']

            ])
            ->add('endDate', DateType::class, [
                'label' => 'absence.end_date',
            ])
            ->add('isEndDateHalfDay', ChoiceType::class, [
                'label' => ' ',
                'choices'=>[
                    'absence.midday' =>true,
                    'absence.evening' =>false
                ],
                'multiple' => false,
                'choice_translation_domain' => 'validators',
                'attr' => ['class' => 'border-2 '],
                'row_attr' =>['class' => 'flex items-end']
            ])
            ->add('absenceType', EnumType::class, [
                'class' => AbsenceType::class,
                'label' => 'absence.absence_type',
                'constraints' => [
                    new NotBlank([
                        'message' => 'error.please_choose_the_absence_type',
                    ]),
                ],
            ])
            ->add('comment', TextareaType::class, [
                'label' => 'absence.comment',
                'required'   => false,
            ])
            ->add('substitute', EntityType::class, [
                'class' => Employee::class,
                'choice_label' => function (Employee $employee) {
                    return $employee->getFullName();
                },
                'label' => 'absence.substitute',
                'placeholder' => 'absence.choose_substitute',
                'required' => false,
                'attr' => ['class' => 'border-2'],
            ]);
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(['data_class' => Absence::class]);
    }
}
