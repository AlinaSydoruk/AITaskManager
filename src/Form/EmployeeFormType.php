<?php

namespace App\Form;

use App\Entity\Employee;
use App\Entity\WorkStatus;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\EnumType;
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
                'constraints' => [
                    new NotBlank([
                        'message' => 'error.please_fill_in_this_field',
                    ]),
                ],
            ])
            ->add('lastName', null, [
                'label' => 'employee.first_name',
                'constraints' => [
                    new NotBlank([
                        'message' => 'error.please_fill_in_this_field',
                    ]),
                ],
            ])
            ->add('firstWorkingDay', DateTimeType::class, [
                'label' => 'employee.first_working_day',
                'widget' => 'single_text',
                'constraints' => [
                    new NotBlank([
                        'message' => 'error.please_fill_in_this_field',
                    ]),
                    new GreaterThanOrEqual([
                        'value' => 'today',
                        'message' => 'error.the_first_working_day_cannot_be_earlier_than_today',
                    ]),
                ],
            ])
            ->add('lastWorkingDay', DateTimeType::class, [
                'label' => 'employee.last_working_day',
                'widget' => 'single_text',
                'required'   => false,
                'constraints' => [
                    new GreaterThanOrEqual([
                        'propertyPath' => 'parent.all[firstWorkingDay].data',
                        'message' => 'error.last_working_day_cannot_be_earlier_than_first_working_date',
                    ]),
                ],

            ])
            ->add('workStatus', EnumType::class, [
                'class' => WorkStatus::class,
                'label' => 'employee.work_status',
                'constraints' => [
                    new NotBlank([
                        'message' => 'error.please_choose_work_status',
                    ]),
                ],
            ])
            ->add('email', EmailType::class, [
                'label' => 'employee.email',
                'constraints' => [
                    new NotBlank([
                        'message' => 'error.please_fill_in_this_field',
                    ]),
                ],
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
                'constraints' => [
                    new NotBlank([
                        'message' => 'error.please_fill_in_this_field',
                    ]),
                ],
            ])
            ->add('city', null, [
                'label' => 'employee.city',
                'constraints' => [
                    new NotBlank([
                        'message' => 'error.please_fill_in_this_field',
                    ]),
                ],
            ])
            ->add('postalCode', null, [
                'label' => 'employee.postal_code',
                'constraints' => [
                    new NotBlank([
                        'message' => 'error.please_fill_in_this_field',
                    ]),
                ],
            ])
            ->add('monthlySalary', NumberType::class, [
                'label' => 'employee.monthly_salary',
                'constraints' => [
                    new NotBlank([
                        'message' => 'error.please_fill_in_this_field ',
                    ]),
                    new Positive([
                        'message' => 'error.monthly_salary_must_be_greater_than_0',
                            ])
                ],
            ])
            ->add('jobTitle', null, [
                'label' => 'employee.job_title',
                'constraints' => [
                    new NotBlank([
                        'message' => 'error.please_fill_in_this_field',
                    ]),
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(['data_class' => Employee::class]);
    }
}
