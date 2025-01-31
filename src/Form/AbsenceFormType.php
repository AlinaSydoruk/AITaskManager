<?php

namespace App\Form;

use App\Entity\Absence;
use App\Entity\AbsenceType;
use App\Entity\WorkStatus;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
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
            ->add('startDate', DateType::class, [
                'label' => 'absence.start_date',
                'constraints' => [
                    new NotBlank([
                        'message' => 'error.please_enter_the_first_absence_day',
                    ]),
                ],
            ])
            ->add('endDate', DateType::class, [
                'label' => 'absence.end_date',
                'constraints' => [
                    new NotBlank([
                        'message' => 'error.please_enter_the_last_absence_day',
                    ]),
                    new GreaterThanOrEqual([
                        'propertyPath' => 'parent.all[startDate].data',
                        'message' => 'error.the_end_date_cannot_be_earlier_than_start_date',
                    ]),
                ],
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
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(['data_class' => Absence::class]);
    }
}
