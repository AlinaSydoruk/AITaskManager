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
                'row_attr' => ['class' => 'relative z-0 w-full mb-5 group'],
                'label' => 'First Name',
                'label_attr' => [
                    'class' =>' text-sm '
                ],
                'attr' => [
                    'class' => ' py-2.5 px-3 w-full text-sm text-gray-900 bg-transparent border-0 border-b-2 border-gray-600 focus:outline-none focus:ring-0  ',
                    'placeholder' => ' ',

                ],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Please enter the first name',
                    ]),
                ],
            ])
            ->add('lastName', null, [
                'label' => 'Last Name',
                'label_attr' => [
                    'class' =>' text-sm '
                ],
                'attr' => [
                    'class' => ' py-2.5 px-3 w-full text-sm text-gray-900 bg-transparent border-0 border-b-2 border-gray-600 focus:outline-none focus:ring-0  ',
                    'placeholder' => ' ',

                ],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Please enter the last name',
                    ]),
                ],
            ])
            ->add('firstWorkingDay', DateTimeType::class, [
                'label' => 'First Working Day',
                'widget' => 'single_text',
                'label_attr' => [
                    'class' =>' text-sm '
                ],
                'attr' => [
                    'class' => ' py-2.5 px-3 w-full text-sm text-gray-900 bg-transparent border-0 border-b-2 border-gray-600 focus:outline-none focus:ring-0  ',
                    'placeholder' => ' ',

                ],
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
                'label' => 'Last Working Day',
                'widget' => 'single_text',
                'label_attr' => [
                    'class' =>' text-sm '
                ],
                'attr' => [
                    'class' => ' py-2.5 px-3 w-full text-sm text-gray-900 bg-transparent border-0 border-b-2 border-gray-600 focus:outline-none focus:ring-0  ',
                    'placeholder' => ' ',

                ],
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
                'label_attr' => [
                    'class' =>' text-sm '
                ],
                'attr' => [
                    'class' => ' py-2.5 px-3 w-full text-sm text-gray-900 bg-transparent border-0 border-b-2 border-gray-600 focus:outline-none focus:ring-0  ',
                    'placeholder' => ' ',

                ],
                'label' => 'work status',
                'constraints' => [
                    new NotBlank([
                        'message' => 'Please choose the work Status',
                    ]),
                ],
            ])
            ->add('email', EmailType::class, [
                'label' => 'Email',
                'label_attr' => [
                    'class' =>' text-sm '
                ],
                'attr' => [
                    'class' => ' py-2.5 px-3 w-full text-sm text-gray-900 bg-transparent border-0 border-b-2 border-gray-600 focus:outline-none focus:ring-0  ',
                    'placeholder' => ' ',

                ],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Please enter email',
                    ]),
                ],
            ])
            ->add('businessNumber', null, [
                'label' => 'Business Number',
                'label_attr' => [
                    'class' =>' text-sm '
                ],
                'attr' => [
                    'class' => ' py-2.5 px-3 w-full text-sm text-gray-900 bg-transparent border-0 border-b-2 border-gray-600 focus:outline-none focus:ring-0  ',
                    'placeholder' => ' ',

                ],
                'required'   => false,
            ])
            ->add('privateNumber', null, [
                'label' => 'Private Number',
                'label_attr' => [
                    'class' =>' text-sm '
                ],
                'attr' => [
                    'class' => ' py-2.5 px-3 w-full text-sm text-gray-900 bg-transparent border-0 border-b-2 border-gray-600 focus:outline-none focus:ring-0  ',
                    'placeholder' => ' ',

                ],
                'required'   => false,
            ])
            ->add('streetAndNumber', null, [
                'label' => 'Street and Number',
                'label_attr' => [
                    'class' =>' text-sm '
                ],
                'attr' => [
                    'class' => ' py-2.5 px-3 w-full text-sm text-gray-900 bg-transparent border-0 border-b-2 border-gray-600 focus:outline-none focus:ring-0  ',
                    'placeholder' => ' ',

                ],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Please enter a street and number',
                    ]),
                ],
            ])
            ->add('city', null, [
                'label' => 'City',
                'label_attr' => [
                    'class' =>' text-sm '
                ],
                'attr' => [
                    'class' => ' py-2.5 px-3 w-full text-sm text-gray-900 bg-transparent border-0 border-b-2 border-gray-600 focus:outline-none focus:ring-0  ',
                    'placeholder' => ' ',

                ],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Please enter a city',
                    ]),
                ],
            ])
            ->add('postalCode', null, [
                'label' => 'Postal Code',
                'label_attr' => [
                    'class' =>' text-sm '
                ],
                'attr' => [
                    'class' => ' py-2.5 px-3 w-full text-sm text-gray-900 bg-transparent border-0 border-b-2 border-gray-600 focus:outline-none focus:ring-0  ',
                    'placeholder' => ' ',

                ],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Please enter a postal code',
                    ]),
                ],
            ])
            ->add('monthlySalary', NumberType::class, [
                'label' => 'Monthly Salary (CHF)',
                'label_attr' => [
                    'class' =>' text-sm '
                ],
                'attr' => [
                    'class' => ' py-2.5 px-3 w-full text-sm text-gray-900 bg-transparent border-0 border-b-2 border-gray-600 focus:outline-none focus:ring-0  ',
                    'placeholder' => ' ',

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
                'label_attr' => [
                    'class' =>' text-sm '
                ],
                'attr' => [
                    'class' => ' py-2.5 px-3 w-full text-sm text-gray-900 bg-transparent border-0 border-b-2 border-gray-600 focus:outline-none focus:ring-0  ',
                    'placeholder' => ' ',

                ],
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
