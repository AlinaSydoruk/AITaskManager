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
                'label' => 'First Name',
                'constraints' => [
                    new NotBlank([
                        'message' => 'Please enter the first name',
                    ]),
                ],
            ])
            ->add('lastName', null, [
                'label' => 'Last Name',
                'constraints' => [
                    new NotBlank([
                        'message' => 'Please enter the last name',
                    ]),
                ],
            ])
            ->add('firstWorkingDay', DateTimeType::class, [
                'label' => 'First Working Day',
                //'format' => 'dd-MM-yyyy',
                'constraints' => [
                    new NotBlank([
                        'message' => 'Please enter the first working day',
                    ]),
                    new GreaterThanOrEqual([
                        'value' => 'today',
                        'message' => 'First working day cannot be earlier than today',
                    ]),
                ],
            ])
            ->add('lastWorkingDay', DateTimeType::class, [
                'widget' => 'single_text',
                'label' => 'Last Working Day',
              //  'format' => 'dd-MM-yyyy',
                'required'   => false,
                'constraints' => [
                    new GreaterThanOrEqual([
                        'propertyPath' => 'parent.all[firstWorkingDay].data',
                        'message' => 'Last working day cannot be earlier than First working date',
                    ]),
                ],

            ])
            ->add('workStatus', EnumType::class, [
                'class' => WorkStatus::class,
                'label' => 'work status',
                'constraints' => [
                    new NotBlank([
                        'message' => 'Please choose the work Status',
                    ]),
                ],
            ])
            ->add('email', EmailType::class, [
                'label' => 'Email',
                'constraints' => [
                    new NotBlank([
                        'message' => 'Please enter email',
                    ]),
                ],
            ])
            ->add('businessNumber', null, [
                'label' => 'Business Number',
                'required'   => false,
            ])
            ->add('privateNumber', null, [
                'label' => 'Private Number',
                'required'   => false,
            ])
            ->add('streetAndNumber', null, [
                'label' => 'Street and Number',
                'constraints' => [
                    new NotBlank([
                        'message' => 'Please enter a street and number',
                    ]),
                ],
            ])
            ->add('city', null, [
                'label' => 'City',
                'constraints' => [
                    new NotBlank([
                        'message' => 'Please enter a city',
                    ]),
                ],
            ])
            ->add('postalCode', null, [
                'label' => 'Postal Code',
                'constraints' => [
                    new NotBlank([
                        'message' => 'Please enter a postal code',
                    ]),
                ],
            ])
            ->add('monthlySalary', NumberType::class, [
                'label' => 'Monthly Salary (CHF)',
                'attr' => [
                    'placeholder' => 'Enter amount in CHF',
                ],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Please enter  monthly salary',
                    ]),
                    new Positive([
                        'message' => 'Monthly salary must be greater than 0',
                            ])
                ],
            ])
            ->add('jobTitle', null, [
                'label' => 'Job Title',
                'constraints' => [
                    new NotBlank([
                        'message' => 'Please enter the job title',
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
