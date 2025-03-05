<?php

namespace App\Form;

use App\Entity\AbsenceType;
use App\Entity\Employee;
use phpDocumentor\Reflection\Types\Integer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EmployeeFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('firstName', null, [
                'label' => 'employee.first_name',
            ])
            ->add('lastName', null, [
                'label' => 'employee.first_name',
            ])
            ->add('firstWorkingDay', DateType::class, [
                'label' => 'employee.first_working_day',
                'widget' => 'single_text',
            ])
            ->add('lastWorkingDay', DateType::class, [
                'label' => 'employee.last_working_day',
                'widget' => 'single_text',
                'required'   => false,
            ])
            ->add('email', EmailType::class, [
                'label' => 'employee.email',
            ])
            ->add('businessNumber', null, [
                'label' => 'employee.business_number',
                'required'   => false,
            ])
            ->add('privateNumber', null, [
                'label' => 'employee.private_number',
                'required'   => false,
            ])
            ->add('streetAndNumber', null, [
                'label' => 'employee.street_and_number',
            ])
            ->add('city', null, [
                'label' => 'employee.city',
            ])
            ->add('postalCode', null, [
                'label' => 'employee.postal_code',
            ])
            ->add('monthlySalary', MoneyType::class, [
                'label' => 'employee.monthly_salary',
                'currency' => 'CHF',
                'grouping' => true,
            ])
            ->add('jobTitle', null, [
                'label' => 'employee.job_title',
            ])
            ->add('availableVacationDays', IntegerType::class, [
                'mapped' => false,
                'label' => 'employee.available_vacations',
                'attr' => ['class' => 'w-50 '],
            ])
            ->add('isHalfDay', ChoiceType::class, [
                'label' => false,
                'choices'=>[
                    'absence.full_day' =>false,
                    'absence.half_day' =>true
                ],
                'multiple' => false,
                'choice_translation_domain' => 'validators',
                'attr' => ['class' => 'border-2 '],
                'row_attr' =>['class' => 'flex items-end'],
                'mapped' => false,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(['data_class' => Employee::class]);
    }
}
