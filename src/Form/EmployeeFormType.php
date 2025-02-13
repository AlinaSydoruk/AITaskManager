<?php

namespace App\Form;

use App\Entity\Employee;
use App\Entity\WorkStatus;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CurrencyType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\EnumType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\GreaterThanOrEqual;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Positive;

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
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(['data_class' => Employee::class]);
    }
}
